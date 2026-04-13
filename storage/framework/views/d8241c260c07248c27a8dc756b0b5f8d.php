<?php
    $settings = \Illuminate\Support\Facades\Cache::get('all_settings') ?? [];
    $logo = $settings['logo'] ?? null;
    $siteName = $settings['site_name'] ?? 'نتيجتي';
?>
<div class="flex items-center gap-3 py-2">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo): ?>
        <img src="<?php echo e(asset('uploads/' . $logo)); ?>" alt="<?php echo e($siteName); ?>" class="h-10 w-auto object-contain">
    <?php else: ?>
        
        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 rounded-2xl blur opacity-40 group-hover:opacity-75 transition duration-500"></div>
            <div class="relative w-10 h-10 bg-gradient-to-br from-emerald-500 via-teal-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-xl">
                <svg class="w-6 h-6 text-white drop-shadow-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                </svg>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    
    <div class="flex flex-col">
        <span class="text-xl font-black bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-600 bg-clip-text text-transparent leading-tight tracking-tight">
            <?php echo e($siteName); ?>

        </span>
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-[10px] text-gray-500 font-semibold tracking-wide">لوحة التحكم</span>
        </div>
    </div>
</div>
<?php /**PATH /Users/Masry/GitHub/ntegty/resources/views/filament/brand-logo.blade.php ENDPATH**/ ?>