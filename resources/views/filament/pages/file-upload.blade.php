<x-filament-panels::page>
    <x-filament::card>
        <form wire:submit.prevent="submit">
            {{ $this->form }}
            
            <div class="mt-6 flex justify-end gap-3">
                <x-filament::button wire:click.prevent="submit" type="submit" color="success" size="lg">
                    <x-slot name="icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </x-slot>
                    رفع الملف واستيراد البيانات
                </x-filament::button>
            </div>
            
            @if(count($previewHeaders) > 0)
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-bold mb-4 text-primary-600">معاينة البيانات (أول 5 سجلات)</h3>
                    <div class="overflow-x-auto border rounded-xl shadow-sm">
                        <table class="w-full text-sm text-right">
                            <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <tr>
                                    @foreach($previewHeaders as $header)
                                        <th scope="col" class="px-6 py-3 border-b dark:border-gray-600 font-bold whitespace-nowrap">
                                            {{ $header }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($previewRows as $row)
                                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        @foreach($row as $cell)
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                                {{ $cell }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 p-4 rounded-lg mt-4 text-sm flex items-start gap-2">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>هذه معاينة أولية للبيانات. يرجى التأكد من أن الأعمدة (رقم الجلوس، الاسم، المجموع، الحالة) واضحة وصحيحة. سيقوم النظام بمحاولة التعرف عليها تلقائياً.</span>
                    </div>
                </div>
            @endif
        </form>
    </x-filament::card>
</x-filament-panels::page>
