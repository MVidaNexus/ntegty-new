<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($breadcrumbs)): ?>
    <nav class="mb-6 text-sm no-print">
        <ol class="flex items-center gap-2 text-gray-600">
            <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index > 0): ?>
                    <li><i class="fa-solid fa-chevron-left text-xs mx-2"></i></li>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($crumb['url'])): ?>
                        <a href="<?php echo e($crumb['url']); ?>" class="hover:text-blue-600"><?php echo e($crumb['name']); ?></a>
                    <?php else: ?>
                        <span class="text-gray-800 font-semibold"><?php echo e($crumb['name']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ol>
    </nav>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php
        $serviceType = isset($examType) ? ($examType->result_service_type ?? 'search') : 'search';
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($serviceType === 'embed' && isset($examType)): ?>
        
        <?php echo $__env->make('partials.result-embed', ['examType' => $examType, 'title' => $title ?? $examType->name_ar], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($serviceType === 'pdf' && isset($examType)): ?>
        
        <?php echo $__env->make('partials.result-pdf', ['examType' => $examType, 'title' => $title ?? $examType->name_ar], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($serviceType === 'governorate_table' && isset($examType)): ?>
        
        <?php echo $__env->make('partials.result-governorate-table', ['examType' => $examType, 'title' => $title ?? $examType->name_ar], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        
        <?php
            $searchLabel = 'ابحث برقم الجلوس أو الاسم';
            $searchPlaceholder = 'أدخل رقم الجلوس أو اسم الطالب';
            $commonKeywords = [];

            switch($country->code) {
                case 'EG': // Egypt
                    $searchLabel = 'البحث برقم الجلوس أو الاسم';
                    $searchPlaceholder = 'أدخل رقم الجلوس أو الاسم...';
                    $commonKeywords = [
                        'نتيجة ' . $examType->name_ar . ' برقم الجلوس',
                        'نتيجة ' . $examType->name_ar . ' بالاسم',
                        'بوابة النتائج وزارة التربية والتعليم'
                    ];
                    break;
                case 'IQ': // Iraq
                    $searchLabel = 'البحث بالرقم الامتحاني أو الاسم الكامل';
                    $searchPlaceholder = 'أدخل الرقم الامتحاني أو الاسم الثلاثي...';
                    $commonKeywords = [
                        'نتائج السادس الإعدادي 2025 الدور الأول',
                        'تحميل نتائج السادس الإعدادي',
                        'وزارة التربية العراقية النتائج'
                    ];
                    break;
                case 'LY': // Libya
                    $searchLabel = 'البحث برقم الجلوس أو رقم القيد';
                $searchPlaceholder = 'أدخل رقم الجلوس أو رقم القيد...';
                $commonKeywords = [
                    'نتائج الشهادة الإعدادية 2025 ليبيا',
                    'موقع منظومة الامتحانات',
                    'نتيجة الإعدادية برقم الجلوس والقيد'
                ];
                break;
            case 'PS': // Palestine
            case 'JO': // Jordan
                $searchLabel = 'البحث برقم الجلوس فقط';
                $searchPlaceholder = 'أدخل رقم الجلوس...';
                $commonKeywords = [
                    'نتائج التوجيهي 2025',
                    'نتائج الثانوية العامة ' . $country->name_ar,
                    'وزارة التربية والتعليم'
                ];
                break;
            case 'SY': // Syria
                $searchLabel = 'البحث برقم الاكتتاب';
                $searchPlaceholder = 'أدخل رقم الاكتتاب الخاص بك...';
                $commonKeywords = [
                    'نتائج البكالوريا 2025 حسب رقم الاكتتاب',
                    'نتائج الثانوية العامة سوريا',
                    'وزارة التربية السورية'
                ];
                break;
            case 'TN': // Tunisia
                $searchLabel = 'البحث برقم التسجيل (بطاقة الترشح)';
                $searchPlaceholder = 'أدخل رقم التسجيل أو ب.ت.و...';
                $commonKeywords = [
                    'نتائج الباك تونس',
                    'résultat bac tunisie',
                    'bac tn'
                ];
                break;
            case 'DZ': // Algeria
                $searchLabel = 'البحث برقم التسجيل والرقم السري';
                $searchPlaceholder = 'أدخل رقم التسجيل...';
                $commonKeywords = [
                    'نتائج الباك 2025 الجزائر',
                    'الديوان الوطني للامتحانات والمسابقات',
                    'bac.onec.dz'
                ];
                break;
        }
    ?>

    <!-- Search Section -->
    <div class="w-full max-w-6xl mx-auto px-3" x-data="searchComponent()">
        <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl sm:rounded-3xl shadow-2xl p-5 sm:p-8 md:p-10 border border-blue-100">
            <!-- Header with Flag -->
            <div class="text-center mb-6 sm:mb-8 no-print">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->flag_path): ?>
                    <img src="<?php echo e(asset('uploads/' . $country->flag_path)); ?>" 
                         alt="<?php echo e($country->name_ar); ?>" 
                         class="w-16 h-12 sm:w-20 sm:h-16 object-cover rounded-lg shadow-md mx-auto mb-3 sm:mb-4">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-black text-gray-800 mb-2 sm:mb-3 leading-relaxed text-center px-2">
                    <?php echo e($title ?? "نتيجة {$examType->name_ar} في {$country->name_ar}"); ?>

                </h1>
                
                <!-- Result Timer -->
                <div class="mb-4 sm:mb-6">
                    <?php if (isset($component)) { $__componentOriginalffad86c0bcbb60d75763de100e32cdb8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalffad86c0bcbb60d75763de100e32cdb8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.result-timer','data' => ['country' => $country->slug,'type' => $examType->level ?? 'secondary','governorate' => $governorate->slug ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('result-timer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['country' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($country->slug),'type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($examType->level ?? 'secondary'),'governorate' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($governorate->slug ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalffad86c0bcbb60d75763de100e32cdb8)): ?>
<?php $attributes = $__attributesOriginalffad86c0bcbb60d75763de100e32cdb8; ?>
<?php unset($__attributesOriginalffad86c0bcbb60d75763de100e32cdb8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalffad86c0bcbb60d75763de100e32cdb8)): ?>
<?php $component = $__componentOriginalffad86c0bcbb60d75763de100e32cdb8; ?>
<?php unset($__componentOriginalffad86c0bcbb60d75763de100e32cdb8); ?>
<?php endif; ?>
                </div>
                
                <p class="text-sm sm:text-base text-gray-600 font-medium text-center px-2">
                    <?php echo e($searchLabel); ?>

                </p>
            </div>

            
            <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'country-after-title'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ad-unit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdUnit::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88)): ?>
<?php $attributes = $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88; ?>
<?php unset($__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88)): ?>
<?php $component = $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88; ?>
<?php unset($__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88); ?>
<?php endif; ?>

            <!-- Search Form -->
            
            <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'country-before-search'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ad-unit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdUnit::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88)): ?>
<?php $attributes = $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88; ?>
<?php unset($__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88)): ?>
<?php $component = $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88; ?>
<?php unset($__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88); ?>
<?php endif; ?>

            <form @submit.prevent="search" class="space-y-4 sm:space-y-6 no-print">
                <div class="flex flex-col sm:flex-row gap-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($showYearFilter) && $showYearFilter && isset($academicYears)): ?>
                    <div class="w-full sm:w-1/3">
                        <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-2 sm:mb-3">
                            السنة الدراسية
                        </label>
                        <select x-model="academicYearId" 
                                class="w-full px-4 py-3 sm:px-6 sm:py-4 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none text-base sm:text-lg transition-all bg-white">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($year->id); ?>"><?php echo e($year->year); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="w-full <?php echo e((isset($showYearFilter) && $showYearFilter) ? 'sm:w-2/3' : ''); ?>">
                        <label class="block text-xs sm:text-sm font-bold text-gray-700 mb-2 sm:mb-3">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <?php echo e($searchLabel); ?>

                            </span>
                        </label>
                        <input type="text" 
                               x-model="query" 
                               required
                               placeholder="<?php echo e($searchPlaceholder); ?>"
                               class="w-full px-4 py-3 sm:px-6 sm:py-4 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none text-base sm:text-lg transition-all">
                    </div>
                </div>

                
                <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'country-inside-search'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ad-unit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdUnit::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88)): ?>
<?php $attributes = $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88; ?>
<?php unset($__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88)): ?>
<?php $component = $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88; ?>
<?php unset($__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88); ?>
<?php endif; ?>

                <button type="submit" 
                        :disabled="loading"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 sm:py-4 px-6 sm:px-8 rounded-xl transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg hover:shadow-xl">
                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="text-base sm:text-xl">بحث عن النتيجة</span>
                    </span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm sm:text-base">جاري البحث...</span>
                    </span>
                </button>

                <!-- Search Tips -->
                <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-800">
                    <div class="flex items-center gap-2 mb-2 font-bold">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>تنويهات هامة للبحث:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-blue-700/90 leading-relaxed font-medium">
                        <li>عند البحث بالاسم، يجب كتابة <strong>الاسم ثلاثي</strong> على الأقل.</li>
                        <li>النظام يعالج تلقائياً الاختلافات في (أ/إ/آ) و (ة/هـ) و (ي/ى).</li>
                        <li>يفضل البحث برقم الجلوس للحصول على نتيجة أكثر دقة وسرعة.</li>
                    </ul>
                </div>
            </form>
            </div>

            
            <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'country-after-search'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('ad-unit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AdUnit::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88)): ?>
<?php $attributes = $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88; ?>
<?php unset($__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88)): ?>
<?php $component = $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88; ?>
<?php unset($__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88); ?>
<?php endif; ?>

            <!-- Results -->
            <div x-show="results.length > 0" class="mt-6 sm:mt-10 space-y-4 sm:space-y-6 print:mt-0 print:space-y-8" x-cloak>
                <div class="border-t-2 border-blue-200 pt-4 sm:pt-6 no-print">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                        النتائج
                    </h2>
                    
                    <template x-for="result in results" :key="result.id">
                        <div class="bg-white rounded-2xl p-4 sm:p-6 md:p-8 border-2 border-blue-100 shadow-lg hover:shadow-xl transition-shadow print:shadow-none print:border-2 print:border-gray-800 print:rounded-none break-inside-avoid">
                            
                            <!-- Header for Print Only -->
                        <div class="hidden print:block mb-6 border-b-2 border-gray-800 pb-4">
                            <div class="flex items-center justify-between px-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?php echo e(asset('uploads/' . $country->flag_path)); ?>" class="h-12 w-auto object-contain">
                                    <div class="text-right">
                                        <h2 class="text-lg font-bold text-gray-900 leading-tight">نتيجتي - <?php echo e($country->name_ar); ?></h2>
                                        <p class="text-xs text-gray-600"><?php echo e($examType->name_ar); ?></p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['logo'])): ?>
                                        <img src="<?php echo e(asset('uploads/' . $settings['logo'])); ?>" class="h-14 w-auto object-contain">
                                    <?php else: ?>
                                        <h1 class="text-2xl font-black text-gray-800 tracking-tighter">نتيجتي</h1>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Total Score - Top on Mobile -->
                        <div class="mb-4 sm:mb-5">
                                <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-xl p-4 text-center text-white shadow-md print:bg-none print:bg-white print:border-2 print:border-gray-800 print:text-black print:shadow-none">
                                    <p class="text-sm font-medium mb-1 opacity-90">المجموع الكلي</p>
                                    <p class="text-4xl sm:text-5xl font-black" x-text="result.total_score"></p>
                                </div>
                            </div>

                            <!-- Student Info -->
                            <div class="mb-4 sm:mb-5 pb-4 border-b border-gray-200 print:border-gray-800">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 print:hidden">
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 print:text-black flex-1">
                                        <span class="hidden print:inline text-gray-600 font-medium ml-1">اسم الطالب:</span>
                                        <span x-text="result.student_name"></span>
                                    </h3>
                                </div>
                                
                                <div class="space-y-2 mr-12">
                                    <div class="flex items-center gap-2 text-sm sm:text-base text-gray-700">
                                        <span class="text-blue-600 font-semibold"><i class="fa-solid fa-hashtag"></i></span>
                                        <span>رقم الجلوس:</span>
                                        <strong class="text-gray-900" x-text="result.seat_number"></strong>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm sm:text-base text-gray-700" x-show="result.governorate">
                                        <span class="text-red-500"><i class="fa-solid fa-location-dot"></i></span>
                                        <span>المحافظة:</span>
                                        <strong class="text-gray-900" x-text="result.governorate"></strong>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm sm:text-base text-gray-700" x-show="result.subjects && (result.subjects['الإدارة'] || result.subjects['الادارة'] || result.subjects['الاداره'] || result.subjects['الإداره'] || result.subjects['EDARA'] || result.subjects['Edara'] || result.subjects['edara'])">
                                        <span class="text-purple-500"><i class="fa-solid fa-building"></i></span>
                                        <span>الإدارة:</span>
                                        <strong class="text-gray-900" x-text="result.subjects['الإدارة'] || result.subjects['الادارة'] || result.subjects['الاداره'] || result.subjects['الإداره'] || result.subjects['EDARA'] || result.subjects['Edara'] || result.subjects['edara']"></strong>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm sm:text-base text-gray-700" x-show="result.subjects && (result.subjects['المدرسة'] || result.subjects['المدرسه'] || result.subjects['SCHOOL'] || result.subjects['School'] || result.subjects['school'])">
                                        <span class="text-green-500"><i class="fa-solid fa-school"></i></span>
                                        <span>المدرسة:</span>
                                        <strong class="text-gray-900" x-text="result.subjects['المدرسة'] || result.subjects['المدرسه'] || result.subjects['SCHOOL'] || result.subjects['School'] || result.subjects['school']"></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Subjects Grid -->
                            <div x-show="result.subjects && Object.keys(result.subjects).length > 0">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0-2.443.29-3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                                    </svg>
                                    <h4 class="font-bold text-gray-800 text-sm sm:text-base">درجات المواد</h4>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-3 mb-4 sm:mb-5 print:grid-cols-3 print:gap-4">
                                    <template x-for="(score, subject) in result.subjects" :key="subject">
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-3 text-center border-2 border-blue-100 print:bg-white print:border-2 print:border-gray-900 print:p-0 print:rounded-lg print:break-inside-avoid"
                                         :class="isAbsentScore(score) ? 'from-red-50 to-orange-50 border-red-200' : ''">
                                        <div class="print:bg-gray-100 print:border-b-2 print:border-gray-900 print:p-2">
                                            <p class="text-xs text-gray-600 font-medium line-clamp-1 print:text-sm print:text-black print:font-bold print:line-clamp-none" x-text="subject"></p>
                                        </div>
                                        <div class="print:p-3">
                                            <p class="text-2xl font-black print:text-3xl print:text-black" 
                                               :class="isAbsentScore(score) ? 'text-red-500' : 'text-blue-600'" 
                                               x-text="formatScore(score)"></p>
                                        </div>
                                    </div>
                                </template>
                                </div>
                            </div>

                            <!-- Status & Actions -->
                            <div class="pt-6 border-t border-gray-200 print:border-gray-800">
                                <!-- Large Status Display -->
                                <div class="flex justify-center mb-6 print:mb-8">
                                    <div class="px-10 py-4 rounded-2xl font-black text-2xl sm:text-3xl shadow-md border-2 transform hover:scale-105 transition-transform duration-300 print:shadow-none print:border-4 print:border-gray-900 print:w-full print:text-center print:text-4xl print:py-6"
                                         :class="result.status === 'ناجح' ? 'bg-gradient-to-r from-green-50 to-emerald-50 text-emerald-600 border-emerald-200' : 'bg-gradient-to-r from-red-50 to-pink-50 text-red-600 border-red-200 print:bg-white print:text-black'">
                                        <span x-show="result.status === 'ناجح'" class="print:hidden"><i class="fa-solid fa-medal ml-2"></i></span>
                                        <span x-show="result.status !== 'ناجح'" class="print:hidden"><i class="fa-solid fa-heart-crack ml-2"></i></span>
                                        <span x-text="result.status || 'غير محدد'"></span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 no-print">
                                    <!-- Print Button -->
                                    <button @click="window.print()" 
                                       type="button"
                                       class="w-full sm:w-auto px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 flex items-center justify-center gap-2 group">
                                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                        <span>طباعة النتيجة</span>
                                    </button>

                                    <!-- Certificate Button -->
                                    <a :href="`<?php echo e(route('certificate.index')); ?>?name=${encodeURIComponent(result.student_name)}&score=${result.total_score}&school=${encodeURIComponent(result.governorate || '')}&type=<?php echo e(urlencode($examType->name_ar)); ?>`" 
                                       target="_blank"
                                       class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 flex items-center justify-center gap-2 group relative overflow-hidden">
                                        <span class="absolute top-0 left-0 w-full h-full bg-white/20 -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></span>
                                    <i class="fa-solid fa-trophy text-lg group-hover:rotate-12 transition-transform"></i>
                                    <span>تصميم شهادة التقدير</span>
                                </a>
                                </div>

                                <!-- Copy Link Button (Unified) -->
                                <div class="mt-4 text-center no-print">
                                    <button @click="const link = '<?php echo e(route('country.exam', ['country' => $country->slug, 'slug' => $examType->slug])); ?>/' + result.seat_number; navigator.clipboard.writeText(link); $el.innerHTML = '<i class=\'fa-solid fa-check text-green-500\'></i> تم نسخ الرابط بنجاح'; setTimeout(() => $el.innerHTML = '<i class=\'fa-solid fa-link\'></i> نسخ الرابط المباشر للنتيجة', 3000)" 
                                            class="text-gray-500 hover:text-blue-600 text-sm font-medium transition-colors flex items-center justify-center gap-2 mx-auto">
                                        <i class="fa-solid fa-link"></i>
                                        <span>نسخ الرابط المباشر للنتيجة</span>
                                    </button>
                                </div>
                            </div>
                            </div>
                            
                            <!-- Footer for Print Only -->
                            <div class="hidden print:block text-center mt-6 pt-4 border-t-2 border-gray-800">
                                <p class="text-sm font-bold text-gray-800">ntegty.com</p>
                                <p class="text-xs text-gray-500">تم استخراج النتيجة بتاريخ <?php echo e(date('Y-m-d H:i')); ?></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Error Message -->
            <div x-show="error" 
                 class="mt-4 sm:mt-6 p-4 sm:p-5 bg-red-50 border-2 border-red-200 rounded-xl text-red-700 text-center text-sm sm:text-base" x-cloak>
                <div class="flex items-center justify-center gap-2 sm:gap-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold" x-text="error"></span>
                </div>
            </div>
        </div>
    </div>

<?php $__env->startPush('scripts'); ?>
<script>
function searchComponent() {
    return {
        query: <?php echo json_encode($seat_number ?? '', 15, 512) ?>,
        academicYearId: localStorage.getItem('academic_year_id') || '<?php echo e(\App\Models\AcademicYear::where("is_active", true)->value("id")); ?>',
        loading: false,
        results: [],
        error: '',
        defaultAbsentMarkers: <?php echo json_encode(\App\Models\ExamType::DEFAULT_ABSENT_MARKERS, 15, 512) ?>,
        // Metadata fields that should NOT have absent marker styling applied
        metadataFields: ['الإدارة', 'الادارة', 'الاداره', 'الإداره', 'المدرسة', 'المدرسه', 'الاسم', 'رقم الجلوس', 'المجموع', 'المجموع الكلي', 'الحالة', 'الترتيب', 'EDARA', 'SCHOOL', 'Edara', 'School', 'edara', 'school'],
        
        // Check if a field is a metadata field (not a subject)
        isMetadataField(fieldName) {
            return this.metadataFields.includes(fieldName);
        },
        
        // Check if score is absent marker (غ, غائب, etc.) - only for subject scores
        isAbsentScore(score, result = null) {
            if (score === null || score === undefined) return false;
            const scoreStr = String(score).trim();
            const scoreLower = scoreStr.toLowerCase();
            
            // Use result's absent_markers if available, otherwise use defaults
            const markers = (result && result.absent_markers) ? result.absent_markers : this.defaultAbsentMarkers;
            
            for (const marker of markers) {
                if (scoreLower === marker.toLowerCase().trim()) {
                    return true;
                }
            }
            // Check if starts with غ only for short values (5 chars or less - likely absence markers not names)
            if (scoreStr.length <= 5 && scoreStr.startsWith('غ')) {
                return true;
            }
            return false;
        },
        
        // Format score for display
        formatScore(score, result = null) {
            if (score === null || score === undefined || score === '') return '-';
            const scoreStr = String(score).trim();
            if (this.isAbsentScore(scoreStr, result)) {
                return 'غائب';
            }
            return scoreStr;
        },
        
        // Save preferences to localStorage
        savePreferences() {
            localStorage.setItem('academic_year_id', this.academicYearId);
        },
        
        async search() {
            // Save user preferences
            this.savePreferences();
            this.loading = true;
            this.error = '';
            this.results = [];
            
            try {
                const response = await fetch('<?php echo e(route("search")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({
                        query: this.query,
                        exam_type_id: <?php echo e($examType->id ?? 'null'); ?>,
                        country_id: <?php echo e($country->id ?? 'null'); ?>,
                        academic_year_id: this.academicYearId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.results = data.results;
                    if (this.results.length === 0) {
                        this.error = 'لم يتم العثور على نتائج. تأكد من البيانات المدخلة.';
                    } else {
                        // Update URL to unique student result page
                        const firstSeat = this.results[0].seat_number;
                        const newUrl = '/<?php echo e($country->slug); ?>/<?php echo e($examType->slug ?? ""); ?>/student/' + firstSeat;
                        window.history.pushState({path: newUrl}, '', newUrl);
                    }
                } else {
                    this.error = data.message || 'حدث خطأ أثناء البحث';
                }
            } catch (error) {
                this.error = 'حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.';
            } finally {
                this.loading = false;
            }
        },
        
        init() {
            if (this.query) {
                this.search();
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
@media print {
    .no-print { display: none !important; }
    body { background: white !important; height: auto !important; overflow: visible !important; }
    .container { max-width: 100% !important; padding: 0 !important; margin: 0 !important; width: 100% !important; }
    
    /* Ensure graphics print */
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    
    /* Avoid breaking inside cards */
    .break-inside-avoid { break-inside: avoid; page-break-inside: avoid; }
    
    /* Hide page margins/headers/footers from browser if possible */
    @page { margin: 0.5cm; size: auto; }
}
</style>
<?php $__env->stopPush(); ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($examType) && $examType->show_content_section && ($examType->content_title || $examType->content_body)): ?>
    <div class="w-full max-w-6xl mx-auto mt-12 px-3">
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->content_title): ?>
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-gray-100"><?php echo e($examType->content_title); ?></h2>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->content_intro): ?>
            <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed"><?php echo $examType->content_intro; ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->content_body): ?>
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-blue-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-blue-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-blue-600 prose-a:hover:text-blue-700">
                <?php echo $examType->getFormattedContentBody(); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($country) && $country->show_content_section && ($country->content_title || $country->content_body)): ?>
    <div class="w-full max-w-6xl mx-auto mt-8 px-3">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6 md:p-10 border border-blue-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->content_title): ?>
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-blue-200 flex items-center gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->flag_path): ?>
                <img src="<?php echo e(asset('uploads/' . $country->flag_path)); ?>" alt="<?php echo e($country->name_ar); ?>" class="w-8 h-6 object-cover rounded shadow">
                <?php else: ?>
                <i class="fa-solid fa-flag text-blue-600"></i>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php echo e($country->content_title); ?>

            </h2>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->content_intro): ?>
            <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed"><?php echo $country->content_intro; ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->content_body): ?>
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-blue-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-blue-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-blue-600 prose-a:hover:text-blue-700">
                <?php echo $country->content_body; ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto mt-8">
        <?php echo $__env->make('partials.popular-keywords', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Masry/GitHub/ntegty/resources/views/country/search.blade.php ENDPATH**/ ?>