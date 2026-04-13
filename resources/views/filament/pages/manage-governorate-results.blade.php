<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="p-4 bg-gradient-to-r from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-xl border border-amber-200 dark:border-amber-700">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-amber-500/20 rounded-lg">
                    <x-heroicon-o-map class="w-8 h-8 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-amber-900 dark:text-amber-100">إدارة ملفات نتائج المحافظات</h2>
                    <p class="text-sm text-amber-700 dark:text-amber-300">
                        ارفع ملف PDF لكل محافظة وحدد حالة اعتماد النتيجة
                    </p>
                </div>
            </div>
        </div>

        {{-- Country Selection --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            {{ $this->form }}
        </div>

        @if($this->selectedCountryId && count($this->governoratesData) > 0)
            {{-- Bulk Actions --}}
            <div class="flex flex-wrap gap-3">
                <button 
                    wire:click="declareAll" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition"
                >
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                    اعتماد الكل
                </button>
                <button 
                    wire:click="undeclareAll" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium transition"
                >
                    <x-heroicon-o-x-circle class="w-5 h-5" />
                    إلغاء اعتماد الكل
                </button>
                <a 
                    href="{{ route('filament.dashboard.pages.upload-result') }}?step=3&display_type=governorate_table" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition"
                >
                    <x-heroicon-o-arrow-left class="w-5 h-5" />
                    العودة لصفحة رفع النتائج
                </a>
            </div>

            {{-- Governorates Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">المحافظة</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">حالة الاعتماد</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">ملف PDF</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->governoratesData as $gov)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                                <x-heroicon-o-map-pin class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                                            </div>
                                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $gov['name_ar'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button 
                                            wire:click="toggleDeclared({{ $gov['id'] }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium transition cursor-pointer
                                                {{ $gov['is_declared'] 
                                                    ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50' 
                                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600' }}"
                                        >
                                            @if($gov['is_declared'])
                                                <x-heroicon-s-check-circle class="w-4 h-4" />
                                                معتمدة
                                            @else
                                                <x-heroicon-o-clock class="w-4 h-4" />
                                                قريباً
                                            @endif
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($gov['has_pdf'])
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                <x-heroicon-s-document class="w-4 h-4" />
                                                ملف مرفوع
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                                <x-heroicon-o-document class="w-4 h-4" />
                                                لا يوجد ملف
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- رابط التعديل --}}
                                            <a 
                                                href="{{ route('filament.dashboard.resources.governorates.edit', $gov['id']) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-100 text-primary-700 hover:bg-primary-200 dark:bg-primary-900/30 dark:text-primary-400 dark:hover:bg-primary-900/50 rounded-lg text-xs font-medium transition"
                                            >
                                                <x-heroicon-o-arrow-up-tray class="w-4 h-4" />
                                                رفع/تعديل
                                            </a>
                                            
                                            @if($gov['has_pdf'])
                                                {{-- عرض الملف --}}
                                                <a 
                                                    href="{{ asset('storage/' . $gov['result_pdf_path']) }}" 
                                                    target="_blank"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 rounded-lg text-xs font-medium transition"
                                                >
                                                    <x-heroicon-o-eye class="w-4 h-4" />
                                                    عرض
                                                </a>
                                                
                                                {{-- حذف الملف --}}
                                                <button 
                                                    wire:click="deletePdf({{ $gov['id'] }})"
                                                    wire:confirm="هل أنت متأكد من حذف ملف PDF لهذه المحافظة؟"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 rounded-lg text-xs font-medium transition"
                                                >
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                    حذف
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                            <x-heroicon-o-map class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">إجمالي المحافظات</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ count($this->governoratesData) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">محافظات معتمدة</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ collect($this->governoratesData)->where('is_declared', true)->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <x-heroicon-o-document class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ملفات مرفوعة</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ collect($this->governoratesData)->where('has_pdf', true)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($this->selectedCountryId)
            <div class="p-8 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-center">
                <x-heroicon-o-map class="w-16 h-16 mx-auto text-gray-400" />
                <p class="mt-4 text-gray-500 dark:text-gray-400">لا توجد محافظات لهذه الدولة</p>
            </div>
        @else
            <div class="p-8 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 text-center">
                <x-heroicon-o-flag class="w-16 h-16 mx-auto text-gray-400" />
                <p class="mt-4 text-gray-500 dark:text-gray-400">اختر الدولة لعرض محافظاتها</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
