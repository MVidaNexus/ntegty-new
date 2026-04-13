<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Info Banner -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <x-heroicon-o-information-circle class="w-6 h-6 text-blue-500" />
                </div>
                <div>
                    <h3 class="font-bold text-blue-800 dark:text-blue-200">روابط السوشيال الافتراضية (الأزرار العائمة)</h3>
                    <p class="text-blue-700 dark:text-blue-300 text-sm mt-1">
                        هذه الروابط ستظهر كأزرار عائمة في أسفل يسار الشاشة في جميع صفحات الموقع.
                        يمكنك تخصيص روابط مختلفة لكل دولة أو شهادة من صفحة "السوشيال حسب الصفحة".
                    </p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form wire:submit="save">
            {{ $this->form }}
            
            <div class="mt-6">
                <x-filament::button type="submit" color="success" icon="heroicon-o-check">
                    حفظ التغييرات
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
