<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PedidoProduto extends Model
{
    protected $table = 'pedido_produto';

    protected $fillable = [
        'pedido_id',
        'equipamento_id',
        'quantidade',
        'status',
        'daily_rate_snapshot',
        'start_at',
        'end_at',
        'computed_total',
        'calc_breakdown_json',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'computed_total' => 'decimal:2',
        'calc_breakdown_json' => 'array',
    ];

    public const STATUS_RESERVADO  = 'reservado';
    public const STATUS_EM_LOCACAO = 'em_locacao';
    public const STATUS_DEVOLVIDO  = 'devolvido';
    public const STATUS_CANCELADO  = 'cancelado';

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function reservar(): bool
    {
        if ($this->status !== self::STATUS_RESERVADO) return false;

        $eq = $this->equipamento;
        if (!$eq || !$eq->reservar((int)$this->quantidade)) return false;

        $this->daily_rate_snapshot = $eq->daily_rate ?? 0;

        return $this->save();
    }

    public function retirar(): bool
    {
        if ($this->status !== self::STATUS_RESERVADO) return false;

        $this->status   = self::STATUS_EM_LOCACAO;
        $this->start_at = Carbon::now();

        $this->equipamento?->retirar((int)$this->quantidade);

        return $this->save();
    }

    public function devolver(): bool
    {
        if ($this->status !== self::STATUS_EM_LOCACAO) return false;

        $this->end_at = Carbon::now();

        $calc = $this->calcularValor();
        $this->computed_total      = $calc['total'];
        $this->calc_breakdown_json = $calc['detalhe'];

        $this->status = self::STATUS_DEVOLVIDO;

        $this->equipamento?->devolver((int)$this->quantidade);

        return $this->save();
    }

    public function cancelar(): bool
    {
        if ($this->status !== self::STATUS_RESERVADO) return false;

        $this->status = self::STATUS_CANCELADO;
        $ok = $this->save();

        if ($ok) {
            $this->equipamento?->liberar((int)$this->quantidade);
        }
        return $ok;
    }

    protected function calcularValor(): array
    {
        $start = Carbon::parse($this->start_at);
        $end   = Carbon::parse($this->end_at);

        $duracaoMin = $start->copy()->addHours(2);

        if ($end->lessThanOrEqualTo($duracaoMin)) {
            $diasCobrados = 1;
        } else {
            $diffHoras    = abs($end->floatDiffInHours($start));
            
            $diasCobrados = (int) ceil($diffHoras / 24);
            $diasCobrados = max(1, $diasCobrados);
        }

        $rateDia = (float)($this->daily_rate_snapshot ?? $this->equipamento?->daily_rate ?? 0);
        $rateSem = (float)($this->equipamento?->weekly_rate ?? ($rateDia * 7 * 0.90));
        $rateMes = (float)($this->equipamento?->monthly_rate ?? ($rateDia * 30 * 0.80));

        $resto   = $diasCobrados;
        $meses   = intdiv($resto, 30); $resto   -= $meses * 30;
        $semanas = intdiv($resto, 7);  $resto   -= $semanas * 7;
        $totalBest = $meses * $rateMes + $semanas * $rateSem + $resto * $rateDia;

        $totalFinal = round($totalBest * (int)$this->quantidade, 2);

        return [
            'total'   => $totalFinal,
            'detalhe' => [
                'dias_cobrados' => $diasCobrados,
                'meses' => $meses,
                'semanas' => $semanas,
                'dias' => $resto,
                'rate_dia' => $rateDia,
                'rate_semana' => $rateSem,
                'rate_mes' => $rateMes,
                'qtd' => (int)$this->quantidade,
            ],
        ];
    }

    public function breakdownDecorrido(?\DateTimeInterface $ate = null): array
    {
        if ($this->status === self::STATUS_CANCELADO) {
            return [];
        }

        $start = $this->start_at ?? $this->created_at ?? Carbon::now();
        
        if ($this->status === self::STATUS_DEVOLVIDO) {
            $end = $this->end_at ?? Carbon::now();
        } else {
            $end = $ate ? Carbon::instance($ate) : Carbon::now();
        }

        if ($end->lt($start)) {
            return [];
        }

        $rateDia = (float)($this->daily_rate_snapshot ?? $this->equipamento?->daily_rate ?? 0);
        $qtd     = (int)$this->quantidade;
        $series = [];
        $acumulado = 0.0;

        $current = $start->copy()->startOfDay();
        $endDay  = $end->copy()->startOfDay();

        while ($current->lte($endDay)) {
            $nextDay = $current->copy()->addDay();
            
            $intervalStart = $current->max($start);
            $intervalEnd   = $nextDay->min($end);

            $minutes = 0;
            if ($intervalEnd->gt($intervalStart)) {
                $minutes = abs($intervalEnd->diffInMinutes($intervalStart));
            }

            $horas   = $minutes / 60.0;
            $horas   = max(0.0, min($horas, 24.0)); 

            $parcela = ($horas / 24.0) * $rateDia * $qtd;
            $acumulado += $parcela;
            $series[] = [
                'data'      => $current->format('Y-m-d'),
                'horas'     => round($horas, 2),
                'parcela'   => round($parcela, 2),
                'regra'     => $this->status === self::STATUS_DEVOLVIDO ? 'projecao' : 'projecao', 
                'acumulado' => round($acumulado, 2),
            ];
            $current->addDay();
        }

        if ($this->status === self::STATUS_DEVOLVIDO && $this->computed_total !== null) {
            $finalTotal = (float) $this->computed_total;

            $totalHours = 0.0;
            foreach ($series as $linha) {
                $totalHours += (float)($linha['horas'] ?? 0);
            }

            if ($totalHours > 0.0) {
                $acumuladoTmp = 0.0;
                foreach ($series as $idx => $linha) {
                    $alloc = ($linha['horas'] / $totalHours) * $finalTotal;
                    $series[$idx]['parcela'] = round($alloc, 2);
                    $acumuladoTmp += $alloc;
                    $series[$idx]['acumulado'] = round($acumuladoTmp, 2);
                    $series[$idx]['regra'] = 'consolidado';
                }
                $diff = round($finalTotal - $acumuladoTmp, 2);
                if (abs($diff) > 0.01 && count($series) > 0) {
                    $lastIndex = count($series) - 1;
                    $series[$lastIndex]['parcela'] = round($series[$lastIndex]['parcela'] + $diff, 2);
                    $series[$lastIndex]['acumulado'] = round($finalTotal, 2);
                }
            } else {
                $lastIndex = count($series) - 1;
                if ($lastIndex >= 0) {
                    $series[$lastIndex]['parcela'] = round($finalTotal, 2);
                    $series[$lastIndex]['acumulado'] = round($finalTotal, 2);
                    $series[$lastIndex]['regra'] = 'consolidado';
                }
            }
        }
        return $series;
    }

    public function alterarQuantidade(int $novaQuantidade): bool
    {
        if ($this->status !== self::STATUS_RESERVADO) {
            return false;
        }
        $novaQuantidade = (int)$novaQuantidade;
        $dif = $novaQuantidade - (int)$this->quantidade;
        if ($dif === 0) {
            return true;
        }
        $equipamento = $this->equipamento;
        if (!$equipamento) {
            return false;
        }
        
        if ($dif > 0) {
            if (!$equipamento->reservar($dif)) {
                return false;
            }
        } else {
            $equipamento->liberar(-$dif);
        }
        $this->quantidade = $novaQuantidade;
        return $this->save();
    }
}