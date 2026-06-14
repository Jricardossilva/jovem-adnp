<?php

namespace App\Livewire;

use App\Enums\SituacaoCadastro;
use App\Enums\StatusPelada;
use App\Models\Atleta;
use App\Models\Inscricao;
use App\Models\Pelada;
use App\Models\Versiculo;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AcessoPelada extends Component
{
    public string $etapa = 'codigo';   // codigo | telefone | lista | cadastro | confirmado

    public string $codigo = '';
    public string $telefone4 = '';

    public ?int $peladaId = null;
    public ?int $atletaConfirmadoId = null;

    /** Atletas que casaram com os 4 dígitos (ou roster, quando sem verificação). */
    public array $candidatos = [];

    // Formulário de novo cadastro
    public string $novoNome = '';
    public string $novoApelido = '';
    public string $novoTelefone = '';

    public string $erro = '';
    public string $aviso = '';

    public function mount(?string $codigo = null): void
    {
        if ($codigo) {
            $this->codigo = strtoupper(trim($codigo));
            $this->buscarPelada();
        }
    }

    public function getPeladaProperty(): ?Pelada
    {
        return $this->peladaId ? Pelada::with('local')->find($this->peladaId) : null;
    }

    public function buscarPelada(): void
    {
        $this->reset('erro', 'aviso');
        $codigo = strtoupper(trim($this->codigo));

        $pelada = Pelada::where('codigo', $codigo)
            ->where('status', StatusPelada::Aberta->value)
            ->first();

        if (! $pelada) {
            $this->erro = 'Código inválido ou a lista desta pelada não está aberta.';
            $this->peladaId = null;

            return;
        }

        $this->peladaId = $pelada->id;

        if ($pelada->exige_verificacao_telefone) {
            $this->etapa = 'telefone';
        } else {
            $this->carregarRoster();
            $this->etapa = 'lista';
        }
    }

    public function verificarTelefone(): void
    {
        $this->reset('erro', 'aviso');

        if (! preg_match('/^\d{4}$/', $this->telefone4)) {
            $this->erro = 'Informe exatamente os 4 últimos dígitos do seu telefone.';

            return;
        }

        $atletas = Atleta::query()
            ->aprovados()
            ->where('telefone_final', $this->telefone4)
            ->orderBy('nome')
            ->get();

        if ($atletas->isEmpty()) {
            $this->erro = 'Nenhum atleta cadastrado com esses dígitos. Toque em "Não estou na lista" para se cadastrar.';

            return;
        }

        $this->candidatos = $atletas->map(fn (Atleta $a) => $this->mapearCandidato($a))->all();
        $this->etapa = 'lista';
    }

    private function carregarRoster(): void
    {
        // Modo sem verificação de telefone: mostra todos os atletas aprovados e ativos.
        $this->candidatos = Atleta::query()
            ->aprovados()
            ->ativos()
            ->orderBy('nome')
            ->get()
            ->map(fn (Atleta $a) => $this->mapearCandidato($a))
            ->all();
    }

    private function mapearCandidato(Atleta $a): array
    {
        $pelada = $this->pelada;
        $jaInscrito = $pelada
            ? Inscricao::where('pelada_id', $pelada->id)->where('atleta_id', $a->id)->exists()
            : false;

        return [
            'id' => $a->id,
            'nome' => $a->nomeExibicao(),
            'pode' => $a->podeParticipar(),
            'motivo' => $a->motivoBloqueio(),
            'inscrito' => $jaInscrito,
        ];
    }

    public function entrarNaLista(int $atletaId): void
    {
        $this->reset('erro', 'aviso');
        $pelada = $this->pelada;

        if (! $pelada || ! $pelada->listaAberta()) {
            $this->erro = 'A lista não está mais aberta.';

            return;
        }

        $atleta = Atleta::find($atletaId);
        if (! $atleta) {
            $this->erro = 'Atleta não encontrado.';

            return;
        }

        if (! $atleta->podeParticipar()) {
            $this->erro = $atleta->motivoBloqueio() ?? 'Atleta não pode participar.';

            return;
        }

        if ($pelada->max_atletas && $pelada->inscricoes()->count() >= $pelada->max_atletas) {
            $this->erro = 'A lista atingiu o limite de atletas.';

            return;
        }

        Inscricao::firstOrCreate(
            ['pelada_id' => $pelada->id, 'atleta_id' => $atleta->id],
            ['origem' => 'atleta']
        );

        $this->atletaConfirmadoId = $atleta->id;
        $this->etapa = 'confirmado';
    }

    public function solicitarCadastro(): void
    {
        $this->reset('erro', 'aviso');

        $dados = Validator::make([
            'novoNome' => $this->novoNome,
            'novoTelefone' => $this->novoTelefone,
        ], [
            'novoNome' => ['required', 'string', 'min:3', 'max:120'],
            'novoTelefone' => ['nullable', 'string', 'max:20'],
        ], [], [
            'novoNome' => 'nome',
            'novoTelefone' => 'telefone',
        ])->validate();

        Atleta::create([
            'nome' => $dados['novoNome'],
            'apelido' => $this->novoApelido ?: null,
            'telefone' => $dados['novoTelefone'] ?: null,
            'situacao_cadastro' => SituacaoCadastro::Pendente->value,
            'status' => \App\Enums\StatusAtleta::Ativo->value,
        ]);

        $this->reset('novoNome', 'novoApelido', 'novoTelefone');
        $this->aviso = 'Cadastro enviado! Assim que o organizador aprovar, você poderá entrar na lista.';
        $this->etapa = $this->peladaId ? ($this->pelada->exige_verificacao_telefone ? 'telefone' : 'lista') : 'codigo';
    }

    public function irParaCadastro(): void
    {
        $this->reset('erro', 'aviso');
        $this->etapa = 'cadastro';
    }

    public function voltarAoInicio(): void
    {
        $this->reset();
        $this->etapa = 'codigo';
    }

    public function getParticipantesProperty()
    {
        $pelada = $this->pelada;
        if (! $pelada) {
            return collect();
        }

        return $pelada->inscricoes()->with('atleta')->get()
            ->map(fn (Inscricao $i) => $i->atleta?->nomeExibicao())
            ->filter()
            ->values();
    }

    public function render()
    {
        return view('livewire.acesso-pelada', [
            'versiculo' => Versiculo::aleatorio(),
            'participantes' => $this->participantes,
        ]);
    }
}
