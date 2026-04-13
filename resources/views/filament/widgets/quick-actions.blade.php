<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bolt class="w-6 h-6 text-amber-500" />
                <span>إجراءات سريعة</span>
            </div>
        </x-slot>

        <div class="grid grid-cols-2 gap-3">
            <a href="/dashboard/upload-logs" 
               class="group flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-100 hover:border-blue-300 hover:shadow-lg transition-all">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-arrow-up-tray class="w-6 h-6 text-white" />
                </div>
                <span class="text-sm font-bold text-gray-700">رفع ملف جديد</span>
            </a>

            <a href="/dashboard/result-schedules" 
               class="group flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-100 hover:border-amber-300 hover:shadow-lg transition-all">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-clock class="w-6 h-6 text-white" />
                </div>
                <span class="text-sm font-bold text-gray-700">إضافة موعد</span>
            </a>

            <a href="/dashboard/governorates" 
               class="group flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-green-50 border-2 border-emerald-100 hover:border-emerald-300 hover:shadow-lg transition-all">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-check-badge class="w-6 h-6 text-white" />
                </div>
                <span class="text-sm font-bold text-gray-700">اعتماد نتيجة</span>
            </a>

            <a href="/dashboard/site-settings" 
               class="group flex flex-col items-center justify-center p-4 rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-100 hover:border-purple-300 hover:shadow-lg transition-all">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-white" />
                </div>
                <span class="text-sm font-bold text-gray-700">الإعدادات</span>
            </a>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100">
            <a href="/dashboard/analytics" 
               class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-gradient-to-r from-gray-800 to-gray-900 text-white font-bold hover:from-gray-700 hover:to-gray-800 transition-all">
                <x-heroicon-o-chart-bar class="w-5 h-5" />
                <span>عرض الإحصائيات الكاملة</span>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
