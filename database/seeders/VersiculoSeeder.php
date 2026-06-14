<?php

namespace Database\Seeders;

use App\Models\Versiculo;
use Illuminate\Database\Seeder;

class VersiculoSeeder extends Seeder
{
    public function run(): void
    {
        $versiculos = [
            ['Melhor é serem dois do que um, porque têm melhor paga do seu trabalho.', 'Eclesiastes 4:9', 'união'],
            ['Tudo posso naquele que me fortalece.', 'Filipenses 4:13', 'esforço'],
            ['E tudo quanto fizerdes, fazei-o de todo o coração, como ao Senhor e não aos homens.', 'Colossenses 3:23', 'disciplina'],
            ['Acima de tudo, porém, tende ardente amor uns para com os outros.', '1 Pedro 4:8', 'respeito'],
            ['O ferro com o ferro se afia; assim o homem afia o rosto do seu amigo.', 'Provérbios 27:17', 'companheirismo'],
            ['Não fazendo nada por contenda ou por vanglória, mas por humildade.', 'Filipenses 2:3', 'humildade'],
            ['Suportai-vos uns aos outros, e perdoai-vos.', 'Colossenses 3:13', 'perdão'],
            ['Sede fortes e corajosos; não temais.', 'Deuteronômio 31:6', 'coragem'],
            ['O que perdoa a transgressão busca a amizade.', 'Provérbios 17:9', 'respeito'],
            ['Corramos com paciência a carreira que nos está proposta.', 'Hebreus 12:1', 'esforço'],
            ['Não desprezeis o pequeno começo.', 'Zacarias 4:10', 'perseverança'],
            ['Amarás o teu próximo como a ti mesmo.', 'Mateus 22:39', 'respeito'],
            ['Onde não há conselho os projetos saem vãos, mas com a multidão de conselheiros se confirmam.', 'Provérbios 15:22', 'união'],
            ['Alegrai-vos com os que se alegram.', 'Romanos 12:15', 'companheirismo'],
            ['Tudo seja feito com decência e ordem.', '1 Coríntios 14:40', 'disciplina'],
            ['A resposta branda desvia o furor.', 'Provérbios 15:1', 'paciência'],
            ['Bem-aventurados os pacificadores.', 'Mateus 5:9', 'paz'],
            ['Esforçai-vos, e ele fortalecerá o vosso coração.', 'Salmos 31:24', 'coragem'],
            ['Levai as cargas uns dos outros.', 'Gálatas 6:2', 'companheirismo'],
            ['Tudo o que fizerdes, seja em palavra, seja em ação, fazei-o em nome do Senhor Jesus.', 'Colossenses 3:17', 'fé'],
        ];

        foreach ($versiculos as [$texto, $referencia, $tema]) {
            Versiculo::firstOrCreate(
                ['referencia' => $referencia],
                ['texto' => $texto, 'tema' => $tema, 'ativo' => true]
            );
        }
    }
}
