<?php

namespace Database\Seeders;

use App\Enums\FrequenciaRecorrencia;
use App\Enums\Modalidade;
use App\Enums\MetodoSorteio;
use App\Enums\SituacaoCadastro;
use App\Enums\StatusAtleta;
use App\Enums\StatusPelada;
use App\Models\Atleta;
use App\Models\Local;
use App\Models\Pelada;
use App\Models\Recorrencia;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $local = Local::firstOrCreate(['nome' => 'Quadra da Igreja'], [
            'endereco' => 'Rua da Igreja, 100',
            'ativo' => true,
        ]);

        Recorrencia::firstOrCreate(['nome' => 'Pelada de quinta'], [
            'local_id' => $local->id,
            'modalidade' => Modalidade::Futsal->value,
            'jogadores_por_time' => 4,
            'com_goleiro' => true,
            'metodo_sorteio' => MetodoSorteio::Aleatorio->value,
            'frequencia' => FrequenciaRecorrencia::Semanal->value,
            'dia_semana' => 4, // quinta
            'horario' => '20:00',
            'exige_verificacao_telefone' => true,
            'ativo' => true,
        ]);

        $nomes = [
            ['Pedro', true, 4], ['Tiago', false, 3], ['João', false, 5], ['André', false, 2],
            ['Filipe', false, 3], ['Tomé', true, 4], ['Mateus', false, 3], ['Lucas', false, 4],
            ['Marcos', false, 2], ['Paulo', false, 5], ['Barnabé', false, 3], ['Silas', false, 3],
        ];

        foreach ($nomes as $i => [$nome, $goleiro, $nivel]) {
            Atleta::firstOrCreate(['nome' => $nome], [
                'telefone' => '4199990'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'e_goleiro' => $goleiro,
                'nivel' => $nivel,
                'status' => StatusAtleta::Ativo->value,
                'situacao_cadastro' => SituacaoCadastro::Aprovado->value,
                'aprovado_em' => now(),
            ]);
        }

        Pelada::firstOrCreate(
            ['local_id' => $local->id, 'data' => now()->next(\Carbon\Carbon::THURSDAY)->toDateString()],
            [
                'modalidade' => Modalidade::Futsal->value,
                'jogadores_por_time' => 4,
                'com_goleiro' => true,
                'metodo_sorteio' => MetodoSorteio::Aleatorio->value,
                'horario' => '20:00',
                'status' => StatusPelada::Aberta->value,
                'exige_verificacao_telefone' => true,
            ]
        );
    }
}
