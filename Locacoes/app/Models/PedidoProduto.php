<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    // Ao criar o item (reserva)
    public function reservar(): bool
    {
        if ($this->status !== self::STATUS_RESERVADO) return false;

        $eq = $this->equipamento;
        if (!$eq || !$eq->reservar((int)$this->quantidade)) return false;

        // Congela o preço do dia no momento da reserva
        $this->daily_rate_snapshot = $eq->daily_rate ?? 0;

        return $this->save();
    }

    // Inicia a locação (retirada)
    public function retirar(): bool
    {
        if ($this->status !== self::STATUS_RESERVADO) return false;

        $this->status   = self::STATUS_EM_LOCACAO;
        $this->start_at = Carbon::now();

        $this->equipamento?->retirar((int)$this->quantidade);

        return $this->save();
    }

    // Devolve e calcula o valor
    public function devolver(): bool
    {
        if ($this->status !== self::STATUS_EM_LOCACAO) return false;

        $this->end_at = Carbon::now();

        $calc = $this->calcularValor(); // ['total'=>..., 'detalhe'=>...]
        $this->computed_total      = $calc['total'];
        $this->calc_breakdown_json = $calc['detalhe']; // cast cuida do JSON

        $this->status = self::STATUS_DEVOLVIDO;

        // Libera estoque
        $this->equipamento?->devolver((int)$this->quantidade);

        return $this->save();
    }

    // Cancela reserva antes de retirar
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

    // Regra: melhor preço entre diária/semana/mês, com carência de 2h e mínimo 1 diária
    protected function calcularValor(): array
    {
        $start = Carbon::parse($this->start_at);
        $end   = Carbon::parse($this->end_at);

        // Carência de 2h
        $duracaoMin = $start->copy()->addHours(2);
        if ($end->lessThanOrEqualTo($duracaoMin)) {
            $diasCobrados = 1;
        } else {
            $diffHoras    = $end->diffInHours($start);
            $diasCobrados = (int) ceil($diffHoras / 24);
            $diasCobrados = max(1, $diasCobrados);
        }

        $rateDia = (float)($this->daily_rate_snapshot ?? $this->equipamento?->daily_rate ?? 0);
        $rateSem = (float)($this->equipamento?->weekly_rate ?? ($rateDia * 7 * 0.90));
        $rateMes = (float)($this->equipamento?->monthly_rate ?? ($rateDia * 30 * 0.80));

        // Combinação “best price”
        $resto   = $diasCobrados;
        $meses   = intdiv($resto, 30); $resto   -= $meses * 30;
        $semanas = intdiv($resto, 7);  $resto   -= $semanas * 7;
        $totalBest = $meses * $rateMes + $semanas * $rateSem + $resto * $rateDia;

        return [
            'total'   => round($totalBest * (int)$this->quantidade, 2),
            'detalhe' => [
                'dias_cobrados' => $diasCobrados,
                'meses' => $meses, 'semanas' => $semanas, 'dias' => $resto,
                'rate_dia' => $rateDia, 'rate_semana' => $rateSem, 'rate_mes' => $rateMes,
                'qtd' => (int)$this->quantidade,
            ],
        ];
    }

    /**
     * Gera uma série dia-a-dia de valores decorridos para este item.
     * Se o item estiver devolvido, utiliza a data de devolução; caso contrário,
     * usa a data atual ou a data fornecida em $ate. A série contém, para cada dia,
     * as horas cobradas, a parcela proporcional do valor diário e o acumulado até então.
     *
     * @param  \DateTimeInterface|null  $ate  Data limite para projeção (somente para itens em andamento)
     * @return array
     */
    public function breakdownDecorrido(?\DateTimeInterface $ate = null): array
    {
        // Determina início: se já foi retirada, usa start_at; senão, usa created_at ou agora
        $start = $this->start_at ?? $this->created_at ?? Carbon::now();
        // Determina fim conforme status
        if ($this->status === self::STATUS_DEVOLVIDO) {
            $end = $this->end_at ?? Carbon::now();
        } else {
            $end = $ate ? Carbon::instance($ate) : Carbon::now();
        }
        // Garante que end >= start
        if ($end->lt($start)) {
            return [];
        }

        $rateDia = (float)($this->daily_rate_snapshot ?? $this->equipamento?->daily_rate ?? 0);
        $qtd     = (int)$this->quantidade;
        $series = [];
        $acumulado = 0.0;
        // Itera cada dia inteiro no intervalo [start, end]
        $current = $start->copy()->startOfDay();
        $endDay  = $end->copy()->startOfDay();
        while ($current->lte($endDay)) {
            $nextDay = $current->copy()->addDay();
            // Intervalo real dentro deste dia
            $intervalStart = $current->gt($start) ? $current->copy() : $start->copy();
            $intervalEnd   = $nextDay->lt($end) ? $nextDay->copy() : $end->copy();
            // Calcula horas reais naquele dia (em minutos para precisão)
            $minutes = $intervalEnd->diffInMinutes($intervalStart);
            $horas   = $minutes / 60.0;
            $horas   = max(0.0, min($horas, 24.0));
            // Parcela proporcional ao valor diário
            $parcela = ($horas / 24.0) * $rateDia * $qtd;
            $acumulado += $parcela;
            $series[] = [
                'data'      => $current->format('Y-m-d'),
                'horas'     => round($horas, 2),
                'parcela'   => round($parcela, 2),
                'regra'     => $this->status === self::STATUS_DEVOLVIDO ? 'consolidado' : 'projecao',
                'acumulado' => round($acumulado, 2),
            ];
            $current->addDay();
        }
        return $series;
    }
}
