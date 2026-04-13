
<?php
    // All 27 Egyptian Governorates with slugs and Arabic names - organized by region
    $egyptGovernorates = [
        // القاهرة الكبرى
        ['slug' => 'cairo', 'name' => 'القاهرة', 'region' => 'القاهرة الكبرى'],
        ['slug' => 'giza', 'name' => 'الجيزة', 'region' => 'القاهرة الكبرى'],
        ['slug' => 'qalyubia', 'name' => 'القليوبية', 'region' => 'القاهرة الكبرى'],
        // الدلتا
        ['slug' => 'alexandria', 'name' => 'الإسكندرية', 'region' => 'الدلتا'],
        ['slug' => 'sharqia', 'name' => 'الشرقية', 'region' => 'الدلتا'],
        ['slug' => 'dakahlia', 'name' => 'الدقهلية', 'region' => 'الدلتا'],
        ['slug' => 'gharbia', 'name' => 'الغربية', 'region' => 'الدلتا'],
        ['slug' => 'monufia', 'name' => 'المنوفية', 'region' => 'الدلتا'],
        ['slug' => 'kafr-el-sheikh', 'name' => 'كفر الشيخ', 'region' => 'الدلتا'],
        ['slug' => 'beheira', 'name' => 'البحيرة', 'region' => 'الدلتا'],
        ['slug' => 'damietta', 'name' => 'دمياط', 'region' => 'الدلتا'],
        // القناة
        ['slug' => 'port-said', 'name' => 'بورسعيد', 'region' => 'القناة'],
        ['slug' => 'ismailia', 'name' => 'الإسماعيلية', 'region' => 'القناة'],
        ['slug' => 'suez', 'name' => 'السويس', 'region' => 'القناة'],
        // سيناء والبحر الأحمر
        ['slug' => 'north-sinai', 'name' => 'شمال سيناء', 'region' => 'سيناء'],
        ['slug' => 'south-sinai', 'name' => 'جنوب سيناء', 'region' => 'سيناء'],
        ['slug' => 'red-sea', 'name' => 'البحر الأحمر', 'region' => 'سيناء'],
        ['slug' => 'matrouh', 'name' => 'مطروح', 'region' => 'سيناء'],
        // الصعيد
        ['slug' => 'fayoum', 'name' => 'الفيوم', 'region' => 'الصعيد'],
        ['slug' => 'beni-suef', 'name' => 'بني سويف', 'region' => 'الصعيد'],
        ['slug' => 'minya', 'name' => 'المنيا', 'region' => 'الصعيد'],
        ['slug' => 'asyut', 'name' => 'أسيوط', 'region' => 'الصعيد'],
        ['slug' => 'sohag', 'name' => 'سوهاج', 'region' => 'الصعيد'],
        ['slug' => 'qena', 'name' => 'قنا', 'region' => 'الصعيد'],
        ['slug' => 'luxor', 'name' => 'الأقصر', 'region' => 'الصعيد'],
        ['slug' => 'aswan', 'name' => 'أسوان', 'region' => 'الصعيد'],
        ['slug' => 'new-valley', 'name' => 'الوادي الجديد', 'region' => 'الصعيد'],
    ];
    
    // Get current governorate slug to exclude from the list
    $currentSlug = $currentGovernorateSlug ?? ($governorate->slug ?? null);
?>

<div class="w-full max-w-6xl mx-auto mt-12 px-3 no-print">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-5 md:p-6">
            <h2 class="text-xl md:text-2xl font-black text-white flex items-center gap-3">
                <i class="fa-solid fa-map-location-dot"></i>
                نتائج الشهادة الإعدادية في جميع المحافظات
            </h2>
            <p class="text-emerald-100 mt-2 text-sm md:text-base">
                اختر محافظتك للاستعلام عن نتيجة الترم الأول أو الترم الثاني
            </p>
        </div>
        
        
        <div class="divide-y divide-gray-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $egyptGovernorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $gov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gov['slug'] !== $currentSlug): ?>
                <a href="<?php echo e(url('/egypt/preparatory/' . $gov['slug'])); ?>" 
                   class="flex items-center justify-between p-4 hover:bg-emerald-50 transition-all duration-200 group">
                    
                    <div class="flex items-center gap-4">
                        
                        <div class="w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg flex items-center justify-center text-sm font-bold group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <?php echo e($index + 1); ?>

                        </div>
                        
                        
                        <div>
                            <h3 class="font-bold text-gray-800 group-hover:text-emerald-700 transition-colors">
                                نتيجة الشهادة الإعدادية محافظة <?php echo e($gov['name']); ?>

                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <i class="fa-solid fa-graduation-cap ml-1 text-emerald-500"></i>
                                الترم الأول والثاني <?php echo e(date('Y')); ?>

                            </p>
                        </div>
                    </div>
                    
                    
                    <div class="flex items-center gap-2">
                        <span class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <i class="fa-solid fa-search"></i>
                            استعلام
                        </span>
                        <i class="fa-solid fa-chevron-left text-gray-300 group-hover:text-emerald-600 transition-colors"></i>
                    </div>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        
        
        <div class="bg-gray-50 px-5 py-4 border-t border-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-gray-600">
                    <i class="fa-solid fa-info-circle text-emerald-600 ml-1"></i>
                    يتم تحديث النتائج فور اعتمادها رسمياً
                </p>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full font-bold">
                        <i class="fa-solid fa-map-marker-alt ml-1"></i>
                        <?php echo e(count($egyptGovernorates) - ($currentSlug ? 1 : 0)); ?> محافظة
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/Masry/GitHub/ntegty/resources/views/partials/governorates-internal-links.blade.php ENDPATH**/ ?>