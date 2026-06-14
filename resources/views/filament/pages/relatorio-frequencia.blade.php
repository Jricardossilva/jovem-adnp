<x-filament-panels::page>
    <div class="flex flex-wrap items-end gap-4">
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Início</label>
            <input type="date" wire:model.live="inicio"
                   class="block rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Fim</label>
            <input type="date" wire:model.live="fim"
                   class="block rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 text-left text-gray-500 dark:border-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 font-medium">Atleta</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-center">Presenças</th>
                    <th class="px-4 py-3 font-medium text-center">Ausências</th>
                    <th class="px-4 py-3 font-medium text-center">Total peladas</th>
                    <th class="px-4 py-3 font-medium text-center">Frequência</th>
                    <th class="px-4 py-3 font-medium text-center">Faltas seg.</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($this->linhas as $linha)
                    <tr class="text-gray-700 dark:text-gray-200">
                        <td class="px-4 py-3 font-medium">{{ $linha['nome'] }}</td>
                        <td class="px-4 py-3">{{ $linha['status']->getLabel() }}</td>
                        <td class="px-4 py-3 text-center">{{ $linha['presencas'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $linha['ausencias'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $linha['total_peladas'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $linha['percentual'] >= 70,
                                'bg-yellow-100 text-yellow-700' => $linha['percentual'] >= 40 && $linha['percentual'] < 70,
                                'bg-red-100 text-red-700' => $linha['percentual'] < 40,
                            ])>{{ $linha['percentual'] }}%</span>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $linha['faltas_consecutivas'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Sem dados no período.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
