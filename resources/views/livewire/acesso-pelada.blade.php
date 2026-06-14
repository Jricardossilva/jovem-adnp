<div class="space-y-4" wire:key="acesso">

    {{-- Versículo dinâmico --}}
    @if ($versiculo)
        <div class="rounded-2xl bg-emerald-50 p-4 text-center ring-1 ring-emerald-100">
            <p class="text-sm italic leading-relaxed text-emerald-900">“{{ $versiculo->texto }}”</p>
            <p class="mt-1 text-xs font-semibold text-emerald-700">{{ $versiculo->referencia }}</p>
        </div>
    @endif

    {{-- Mensagens --}}
    @if ($erro)
        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-100">{{ $erro }}</div>
    @endif
    @if ($aviso)
        <div class="rounded-xl bg-blue-50 px-4 py-3 text-sm text-blue-700 ring-1 ring-blue-100">{{ $aviso }}</div>
    @endif

    {{-- ETAPA: código --}}
    @if ($etapa === 'codigo')
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
            <label class="block text-sm font-medium text-slate-700">Código da pelada</label>
            <p class="mb-3 text-xs text-slate-400">Pegue o código no grupo do WhatsApp.</p>
            <input type="text" wire:model="codigo" wire:keydown.enter="buscarPelada"
                   class="w-full rounded-xl border-slate-200 text-center text-lg font-bold uppercase tracking-widest focus:border-emerald-500 focus:ring-emerald-500"
                   maxlength="8" placeholder="ABC234" autocomplete="off">
            <button wire:click="buscarPelada" wire:loading.attr="disabled"
                    class="mt-4 w-full rounded-xl bg-emerald-600 py-3 font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-60">
                Acessar lista
            </button>
        </div>
    @endif

    {{-- ETAPA: telefone --}}
    @if ($etapa === 'telefone')
        @if ($this->pelada)
            <div class="rounded-xl bg-slate-100 px-4 py-2 text-center text-sm text-slate-600">
                {{ $this->pelada->local->nome ?? '' }} • {{ $this->pelada->data->format('d/m') }} às {{ \Illuminate\Support\Str::of($this->pelada->horario)->substr(0,5) }}
            </div>
        @endif
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
            <label class="block text-sm font-medium text-slate-700">Verificação</label>
            <p class="mb-3 text-xs text-slate-400">Digite os 4 últimos dígitos do seu telefone.</p>
            <input type="tel" inputmode="numeric" wire:model="telefone4" wire:keydown.enter="verificarTelefone"
                   class="w-full rounded-xl border-slate-200 text-center text-2xl font-bold tracking-[0.5em] focus:border-emerald-500 focus:ring-emerald-500"
                   maxlength="4" placeholder="••••" autocomplete="off">
            <button wire:click="verificarTelefone" wire:loading.attr="disabled"
                    class="mt-4 w-full rounded-xl bg-emerald-600 py-3 font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-60">
                Continuar
            </button>
            <button wire:click="irParaCadastro" class="mt-2 w-full py-2 text-sm font-medium text-emerald-700">
                Não estou na lista
            </button>
        </div>
    @endif

    {{-- ETAPA: lista / confirmar nome --}}
    @if ($etapa === 'lista')
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
            <p class="mb-3 text-sm font-medium text-slate-700">Confirme seu nome para entrar na lista:</p>
            <div class="space-y-2">
                @forelse ($candidatos as $c)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-3">
                        <span class="text-sm font-medium text-slate-800">{{ $c['nome'] }}</span>
                        @if ($c['inscrito'])
                            <span class="rounded-lg bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">✓ na lista</span>
                        @elseif (! $c['pode'])
                            <span class="text-xs text-red-500">{{ $c['motivo'] }}</span>
                        @else
                            <button wire:click="entrarNaLista({{ $c['id'] }})"
                                    class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                Sou eu
                            </button>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Nenhum atleta encontrado.</p>
                @endforelse
            </div>
            <button wire:click="irParaCadastro" class="mt-3 w-full py-2 text-sm font-medium text-emerald-700">
                Não estou na lista
            </button>
        </div>
    @endif

    {{-- ETAPA: confirmado --}}
    @if ($etapa === 'confirmado')
        <div class="rounded-2xl bg-white p-6 text-center shadow-sm ring-1 ring-slate-100">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-3xl">✅</div>
            <h2 class="text-lg font-bold text-slate-900">Você está na lista!</h2>
            <p class="mt-1 text-sm text-slate-500">Nos vemos na quadra. Bom jogo!</p>
            <button wire:click="voltarAoInicio" class="mt-4 w-full rounded-xl bg-slate-100 py-2.5 text-sm font-semibold text-slate-700">
                Início
            </button>
        </div>
    @endif

    {{-- ETAPA: cadastro --}}
    @if ($etapa === 'cadastro')
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
            <h2 class="text-base font-semibold text-slate-900">Solicitar cadastro</h2>
            <p class="mb-3 text-xs text-slate-400">O organizador precisará aprovar antes da sua primeira participação.</p>
            <div class="space-y-3">
                <input type="text" wire:model="novoNome" placeholder="Nome completo"
                       class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <input type="text" wire:model="novoApelido" placeholder="Apelido (opcional)"
                       class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <input type="tel" wire:model="novoTelefone" placeholder="Telefone (com DDD)"
                       class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            @error('novoNome') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            <button wire:click="solicitarCadastro" wire:loading.attr="disabled"
                    class="mt-4 w-full rounded-xl bg-emerald-600 py-3 font-semibold text-white hover:bg-emerald-700 disabled:opacity-60">
                Enviar cadastro
            </button>
            <button wire:click="voltarAoInicio" class="mt-2 w-full py-2 text-sm font-medium text-slate-500">Cancelar</button>
        </div>
    @endif

    {{-- Lista de participantes já confirmados --}}
    @if (in_array($etapa, ['lista', 'confirmado']) && $participantes->isNotEmpty())
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
            <h3 class="mb-2 text-sm font-semibold text-slate-700">Na lista ({{ $participantes->count() }})</h3>
            <ol class="space-y-1 text-sm text-slate-600">
                @foreach ($participantes as $i => $nome)
                    <li class="flex gap-2"><span class="w-5 text-right text-slate-400">{{ $i + 1 }}.</span>{{ $nome }}</li>
                @endforeach
            </ol>
        </div>
    @endif
</div>
