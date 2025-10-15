<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipamento; 
use Faker\Factory as Faker; 

class EquipamentoSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');

        
        Equipamento::create([
            'nome' => 'Gerador de Energia 8KVA',
            'tipo' => 'Gerador', 
            'quantidade_total' => 5, 
            'quantidade_disponivel' => 5, 
            'descricao_tecnica' => 'Gerador a gasolina com potência de 8KVA, ideal para obras e eventos. Possui partida elétrica e baixo nível de ruído.',
            'informacoes_manutencao' => 'Manutenção preventiva a cada 50 horas de uso. Verificar nível de óleo, filtro de ar e velas. Limpeza do tanque de combustível anual.',
            'imagem' => 'gerador-8kva.png', 
            'daily_rate' => 150.00, 
        ]);


        Equipamento::create([
            'nome' => 'Compressor de Ar com Reservatório',
            'tipo' => 'Compressor',
            'quantidade_total' => 3,
            'quantidade_disponivel' => 3,
            'descricao_tecnica' => 'Compressor de ar de pistão com reservatório de 100 litros, para uso profissional em diversas ferramentas pneumáticas.',
            'informacoes_manutencao' => 'Drenar o reservatório semanalmente para evitar corrosão. Verificar correias e nível de óleo do motor periodicamente.',
            'imagem' => 'compressor-de-ar-com-reservatorio.png',
            'daily_rate' => 80.00,
        ]);


        Equipamento::create([
            'nome' => 'Vibrador para Concreto',
            'tipo' => 'Ferramenta de Concreto',
            'quantidade_total' => 10,
            'quantidade_disponivel' => 10,
            'descricao_tecnica' => 'Vibrador de imersão para concreto, com motor elétrico de alta frequência, essencial para eliminação de bolhas e adensamento do concreto.',
            'informacoes_manutencao' => 'Verificar estado do chicote e motor antes de cada uso. Limpar a ponteira após a utilização.',
            'imagem' => 'vibrador-concreto.png',
            'daily_rate' => 60.00,
        ]);


        Equipamento::create([
            'nome' => 'Cortadora de Piso',
            'tipo' => 'Ferramenta de Corte',
            'quantidade_total' => 2,
            'quantidade_disponivel' => 2,
            'descricao_tecnica' => 'Cortadora de piso a gasolina com lâmina diamantada, para corte preciso em concreto, asfalto e pedra.',
            'informacoes_manutencao' => 'Verificar nível de óleo e combustível antes do uso. Limpar o filtro de ar regularmente. Inspecionar a lâmina quanto a desgaste.',
            'imagem' => 'cortadora-piso.png',
            'daily_rate' => 120.00,
        ]);


        Equipamento::create([
            'nome' => 'Acabadora de Piso',
            'tipo' => 'Ferramenta de Concreto',
            'quantidade_total' => 2,
            'quantidade_disponivel' => 2,
            'descricao_tecnica' => 'Acabadora de piso (alisadora) a gasolina, para um acabamento liso e uniforme em superfícies de concreto.',
            'informacoes_manutencao' => 'Limpar as pás após o uso. Verificar o motor e o sistema de embreagem. Lubrificar pontos de articulação.',
            'imagem' => 'acabadora-piso.png',
            'daily_rate' => 100.00,
        ]);
        

        Equipamento::create([
            'nome' => 'Betoneira 400L',
            'tipo' => 'Misturador',
            'quantidade_total' => 4,
            'quantidade_disponivel' => 4,
            'descricao_tecnica' => 'Betoneira de 400 litros com motor elétrico, ideal para preparo de argamassa e concreto em obras de médio porte.',
            'informacoes_manutencao' => 'Limpar o tambor após cada uso para evitar acúmulo de material. Verificar correias e sistema de engrenagens.',
            'imagem' => 'betoneira-400l.png',
            'daily_rate' => 90.00,
        ]);


        Equipamento::create([
            'nome' => 'Compactador de Solo',
            'tipo' => 'Compactador',
            'quantidade_total' => 3,
            'quantidade_disponivel' => 3,
            'descricao_tecnica' => 'Compactador de solo (placa vibratória) a gasolina, para compactação de solos granulares, pavimentos e aterros.',
            'informacoes_manutencao' => 'Verificar nível de óleo e combustível. Limpar o filtro de ar. Inspecionar a placa vibratória quanto a danos.',
            'imagem' => 'compactador-solo.png',
            'daily_rate' => 110.00,
        ]);


        Equipamento::create([
            'nome' => 'Rolo Compactador',
            'tipo' => 'Compactador',
            'quantidade_total' => 1,
            'quantidade_disponivel' => 1,
            'descricao_tecnica' => 'Rolo compactador vibratório, ideal para compactação de solos e asfaltos em áreas maiores.',
            'informacoes_manutencao' => 'Verificar níveis de fluídos (óleo do motor, fluido hidráulico). Inspecionar tambores quanto a danos. Limpeza geral após o uso.',
            'imagem' => 'rolo-compactador.png',
            'daily_rate' => 300.00,
        ]);


        Equipamento::create([
            'nome' => 'Transpalete Manual',
            'tipo' => 'Movimentação de Carga',
            'quantidade_total' => 8,
            'quantidade_disponivel' => 8,
            'descricao_tecnica' => 'Transpalete manual com capacidade de 2500 kg, para movimentação de cargas paletizadas em armazéns e depósitos.',
            'informacoes_manutencao' => 'Lubrificar rodas e pontos de articulação. Verificar vazamentos no sistema hidráulico.',
            'imagem' => 'transpalete.png',
            'daily_rate' => 40.00,
        ]);


        Equipamento::create([
            'nome' => 'Martelete Perfurador/Rompedor 16kg',
            'tipo' => 'Ferramenta Elétrica',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'descricao_tecnica' => 'Martelete perfurador e rompedor de 16kg, ideal para demolição e perfuração em concreto e alvenaria de alta resistência.',
            'informacoes_manutencao' => 'Limpar o encaixe do ponteiro. Verificar cabos e escovas de carvão. Lubrificar periodicamente.',
            'imagem' => 'martelete-perfurador-16kg.png',
            'daily_rate' => 95.00,
        ]);


        Equipamento::create([
            'nome' => 'Motosserra a Gasolina',
            'tipo' => 'Ferramenta de Corte',
            'quantidade_total' => 3,
            'quantidade_disponivel' => 3,
            'descricao_tecnica' => 'Motosserra a gasolina de alta potência, para corte de árvores, galhos e toras em trabalhos florestais ou de jardinagem pesada.',
            'informacoes_manutencao' => 'Verificar nível de óleo da corrente e combustível. Limpar filtro de ar e vela. Afiar a corrente regularmente.',
            'imagem' => 'motosserra.png',
            'daily_rate' => 130.00,
        ]);


        Equipamento::create([
            'nome' => 'Máquina de Solda Inversora 425A',
            'tipo' => 'Máquina de Solda',
            'quantidade_total' => 4,
            'quantidade_disponivel' => 4,
            'descricao_tecnica' => 'Máquina de solda inversora de 425A, leve e portátil, ideal para soldagem TIG e eletrodo revestido em diversos metais.',
            'informacoes_manutencao' => 'Limpar dutos de ventilação. Verificar conexões dos cabos. Proteger de umidade e poeira.',
            'imagem' => 'maquina-solda.png',
            'daily_rate' => 115.00,
        ]);


        Equipamento::create([
            'nome' => 'Transformador de Tensão 3000W',
            'tipo' => 'Elétrica',
            'quantidade_total' => 6,
            'quantidade_disponivel' => 6,
            'descricao_tecnica' => 'Transformador de tensão 3000W, bivolt, para conversão de 110V para 220V ou vice-versa, essencial para compatibilidade de equipamentos.',
            'informacoes_manutencao' => 'Verificar cabos e plugues quanto a danos. Proteger de sobrecarga e umidade.',
            'imagem' => 'transformador-3000w.png',
            'daily_rate' => 45.00,
        ]);


        Equipamento::create([
            'nome' => 'Minicarregadeira',
            'tipo' => 'Máquina Pesada',
            'quantidade_total' => 2,
            'quantidade_disponivel' => 2,
            'descricao_tecnica' => 'Minicarregadeira compacta e versátil, com diversos implementos, ideal para movimentação de terra e materiais em espaços reduzidos.',
            'informacoes_manutencao' => 'Verificar níveis de óleo e fluídos. Lubrificar pontos de articulação. Inspecionar pneus e estado geral da máquina.',
            'imagem' => 'minicarregadeira.png',
            'daily_rate' => 450.00,
        ]);


        Equipamento::create([
            'nome' => 'Mini Escavadeira',
            'tipo' => 'Máquina Pesada',
            'quantidade_total' => 1,
            'quantidade_disponivel' => 1,
            'descricao_tecnica' => 'Mini escavadeira hidráulica, compacta e potente, perfeita para escavações em locais de difícil acesso ou obras urbanas.',
            'informacoes_manutencao' => 'Verificar sistema hidráulico e níveis de óleo. Inspecionar esteiras e caçamba. Limpeza após o uso.',
            'imagem' => 'miniescavadeira.png',
            'daily_rate' => 550.00,
        ]);


        Equipamento::create([
            'nome' => 'Torre de Iluminação 4000W',
            'tipo' => 'Iluminação',
            'quantidade_total' => 2,
            'quantidade_disponivel' => 2,
            'descricao_tecnica' => 'Torre de iluminação com 4 refletores LED de 1000W cada, totalizando 4000W, montada em reboque para fácil transporte.',
            'informacoes_manutencao' => 'Verificar cabos e conexões elétricas. Limpar refletores. Inspecionar pneus e sistema de reboque.',
            'imagem' => 'torre-iluminacao-4000w.png',
            'daily_rate' => 280.00,
        ]);


        Equipamento::create([
            'nome' => 'Dumper de Carga 1500kg',
            'tipo' => 'Transporte de Carga',
            'quantidade_total' => 3,
            'quantidade_disponivel' => 3,
            'descricao_tecnica' => 'Dumper de carga com capacidade para 1500kg, ideal para transporte de materiais em obras e terrenos irregulares.',
            'informacoes_manutencao' => 'Verificar nível de óleo do motor e fluídos. Inspecionar pneus e sistema de direção. Limpeza após o uso.',
            'imagem' => 'dumper-1500kg.png',
            'daily_rate' => 220.00,
        ]);


        Equipamento::create([
            'nome' => 'Plataforma Tesoura 10m',
            'tipo' => 'Elevação de Pessoas',
            'quantidade_total' => 2,
            'quantidade_disponivel' => 2,
            'descricao_tecnica' => 'Plataforma elevatória tipo tesoura com altura máxima de trabalho de 10 metros, ideal para trabalhos em altura com segurança.',
            'informacoes_manutencao' => 'Verificar sistema hidráulico e baterias. Inspecionar pneus e sistema de segurança (guarda-corpo, freios).',
            'imagem' => 'plataforma-tesoura-10m.png',
            'daily_rate' => 350.00,
        ]);


        Equipamento::create([
            'nome' => 'Cortador de Grama a Gasolina',
            'tipo' => 'Jardinagem',
            'quantidade_total' => 5,
            'quantidade_disponivel' => 5,
            'descricao_tecnica' => 'Cortador de grama a gasolina com motor de 4 tempos, ideal para jardins de médio e grande porte, com ajuste de altura de corte.',
            'informacoes_manutencao' => 'Verificar nível de óleo e combustível. Limpar filtro de ar. Afiar a lâmina regularmente. Esvaziar o cesto coletor.',
            'imagem' => 'cortador-grama.png',
            'daily_rate' => 70.00,
        ]);


    }
}