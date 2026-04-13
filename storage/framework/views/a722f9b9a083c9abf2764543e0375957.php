<?php $__env->startSection('structured_data'); ?>
<?php echo $structuredData; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-12">
    <!-- Hero Section -->
    <?php
        $heroTitle = \App\Models\SiteSetting::get('hero_title', 'نتيجتي');
        $heroSubtitle = \App\Models\SiteSetting::get('hero_subtitle', 'بوابتك الرسمية لنتائج الامتحانات في الوطن العربي');
    ?>
    <div class="text-center mb-12 mt-4">
        <h1 class="text-2xl md:text-4xl font-black text-slate-800 mb-6 leading-relaxed flex flex-col md:flex-row items-center justify-center gap-2 md:gap-3">
            <span class="text-emerald-600"><?php echo e($heroTitle); ?></span>
            <span class="hidden md:inline text-slate-300">|</span>
            <span class="text-xl md:text-3xl text-slate-700"><?php echo e($heroSubtitle); ?></span>
        </h1>
    </div>

    
    <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'home-header-bottom'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

    <!-- Social Media Buttons -->
    <?php
        $whatsappUrl = \App\Models\Setting::get('homepage_whatsapp_url');
        $whatsappLabel = \App\Models\Setting::get('homepage_whatsapp_label', 'جروب واتساب');
        $whatsappActive = \App\Models\Setting::get('homepage_whatsapp_active', '1');
        
        $telegramUrl = \App\Models\Setting::get('homepage_telegram_url');
        $telegramLabel = \App\Models\Setting::get('homepage_telegram_label', 'قناة تليجرام');
        $telegramActive = \App\Models\Setting::get('homepage_telegram_active', '1');
        
        $facebookPageUrl = \App\Models\Setting::get('homepage_facebook_url');
        $facebookPageLabel = \App\Models\Setting::get('homepage_facebook_label', 'صفحة فيسبوك');
        $facebookPageActive = \App\Models\Setting::get('homepage_facebook_active', '1');
        
        $facebookGroupUrl = \App\Models\Setting::get('homepage_facebook_group_url');
        $facebookGroupLabel = \App\Models\Setting::get('homepage_facebook_group_label', 'جروب فيسبوك');
        $facebookGroupActive = \App\Models\Setting::get('homepage_facebook_group_active', '1');
        
        $hasAnySocial = ($whatsappActive && $whatsappUrl) || ($telegramActive && $telegramUrl) || 
                        ($facebookPageActive && $facebookPageUrl) || ($facebookGroupActive && $facebookGroupUrl);
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAnySocial): ?>
    <div class="flex flex-wrap justify-center gap-3 mb-12">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($whatsappActive && $whatsappUrl): ?>
        <a href="<?php echo e($whatsappUrl); ?>" target="_blank" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-full font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fab fa-whatsapp text-lg"></i>
            <span><?php echo e($whatsappLabel); ?></span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($telegramActive && $telegramUrl): ?>
        <a href="<?php echo e($telegramUrl); ?>" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-full font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fab fa-telegram text-lg"></i>
            <span><?php echo e($telegramLabel); ?></span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($facebookPageActive && $facebookPageUrl): ?>
        <a href="<?php echo e($facebookPageUrl); ?>" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fab fa-facebook text-lg"></i>
            <span><?php echo e($facebookPageLabel); ?></span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($facebookGroupActive && $facebookGroupUrl): ?>
        <a href="<?php echo e($facebookGroupUrl); ?>" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-medium transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5">
            <i class="fab fa-facebook text-lg"></i>
            <span><?php echo e($facebookGroupLabel); ?></span>
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'home-before-search'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

    <!-- Country Selection - Card Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 md:gap-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                // Country code for flag images (ISO 3166-1 alpha-2)
                $flagCodes = [
                    'egypt' => 'eg',
                    'iraq' => 'iq',
                    'syria' => 'sy',
                    'libya' => 'ly',
                    'palestine' => 'ps',
                    'jordan' => 'jo',
                    'tunisia' => 'tn',
                    'algeria' => 'dz',
                    'lebanon' => 'lb',
                    'morocco' => 'ma',
                    'sudan' => 'sd',
                    'yemen' => 'ye',
                    'kuwait' => 'kw',
                    'saudi' => 'sa',
                    'uae' => 'ae',
                    'bahrain' => 'bh',
                    'qatar' => 'qa',
                    'oman' => 'om',
                ];
                $flagCode = $flagCodes[$country->slug] ?? 'un';
            ?>
            <a href="<?php echo e(route('country.index', ['country' => $country->slug])); ?>" 
               class="group relative bg-slate-50/80 rounded-2xl p-5 md:p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 overflow-hidden">
                
                <!-- Background Flag Watermark -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                    <img 
                        src="https://flagcdn.com/w320/<?php echo e($flagCode); ?>.png"
                        alt=""
                        class="w-full h-full object-cover opacity-[0.08] scale-150"
                    >
                </div>
                
                <!-- Flag Circle - Real Flag Image -->
                <div class="relative z-10 flex justify-center mb-4">
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full shadow-lg border-4 border-white overflow-hidden group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
                        <img 
                            src="https://flagcdn.com/w160/<?php echo e($flagCode); ?>.png" 
                            srcset="https://flagcdn.com/w320/<?php echo e($flagCode); ?>.png 2x"
                            alt="علم <?php echo e($country->name_ar); ?>"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </div>
                </div>
                
                <!-- Country Name -->
                <h2 class="relative z-10 text-lg md:text-xl font-bold text-slate-800 text-center mb-2 group-hover:text-emerald-600 transition-colors">
                    <?php echo e($country->name_ar); ?>

                </h2>
                
                <!-- Certificates Count -->
                <div class="relative z-10 flex items-center justify-center gap-1.5 text-sm text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span><?php echo e($country->examTypes->count()); ?> شهادات متاحة</span>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'home-after-search'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

    <!-- Features Section -->
    <div class="mt-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8 max-w-7xl mx-auto px-4">
            <!-- Search Card -->
            <div class="group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden text-center">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-6 -mt-6 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="mb-4 text-blue-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-magnifying-glass text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">بحث سهل</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        ابحث بالاسم أو رقم الجلوس بسهولة وسرعة فائقة
                    </p>
                </div>
            </div>

            <!-- Mobile Card -->
            <div class="group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden text-center">
                <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-bl-full -mr-6 -mt-6 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="mb-4 text-purple-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-mobile-screen text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">متوافق مع الجوال</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        استعرض النتائج بسلاسة من أي جهاز محمول
                    </p>
                </div>
            </div>

            <!-- Printing Card -->
            <div class="group relative bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-100 overflow-hidden text-center">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-6 -mt-6 transition-transform group-hover:scale-110"></div>
                <div class="relative z-10">
                    <div class="mb-4 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-print text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">طباعة احترافية</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        اطبع كشف درجاتك بتصميم أنيق واحترافي
                    </p>
                </div>
            </div>
            
            <!-- Certificate Generator Card -->
            <a href="<?php echo e(route('certificate.index')); ?>" class="group relative text-center p-6 bg-gradient-to-br from-amber-400 via-orange-400 to-rose-500 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 hover:scale-105 overflow-hidden border-2 border-amber-300/50">
                <div class="absolute inset-0 bg-gradient-to-tr from-yellow-300/30 via-transparent to-pink-300/30 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                
                <div class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg transform rotate-12 group-hover:rotate-0 transition-transform duration-300">
                    جديد <i class="fa-solid fa-star text-[10px] mr-1 text-yellow-300"></i>
                </div>
                
                <div class="relative z-10">
                    <div class="mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-award text-5xl text-white drop-shadow-lg"></i>
                    </div>
                    
                    <h3 class="text-xl font-black mb-2 text-white drop-shadow-md">
                        شهادة تقدير
                    </h3>
                    <p class="text-white/90 font-semibold text-sm mb-3">
                        اصنع شهادتك بنفسك في ثوانٍ! <i class="fa-solid fa-wand-magic-sparkles text-yellow-300"></i>
                    </p>
                    <div class="flex items-center justify-center gap-2 text-white font-bold text-xs opacity-90 group-hover:opacity-100">
                        <span>ابدأ الآن</span>
                        <i class="fa-solid fa-arrow-left animate-bounce"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'home-footer-top'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
    
    <!-- About Section - من الإعدادات -->
    <?php
        $aboutActive = \App\Models\SiteSetting::get('about_section_active', '1') === '1';
        $aboutTitle = \App\Models\SiteSetting::get('about_section_title', 'عن نتيجتي');
        $aboutContent = \App\Models\SiteSetting::get('about_section_content', 'موقع نتيجتي هو المنصة العربية الأكبر والأحدث المخصصة لعرض نتائج الشهادات العامة والأزهرية والدبلومات الفنية فور اعتمادها رسمياً. ننفرد بتغطية شاملة وحصرية لنتائج الامتحانات في مصر، العراق، ليبيا، فلسطين وغيرها من الدول. لا نكتفي بعرض النتيجة فحسب، بل نقدم أدوات ذكية تتيح لك البحث بالاسم أو رقم الجلوس، تصميم شهادات تقدير احترافية، وطباعة كشف الدرجات بضغطة زر. هدفنا هو توفير تجربة مستخدم سهلة، سريعة، وموثوقة لجميع الطلاب وأولياء الأمور.');
    ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($aboutActive && $aboutContent): ?>
    <div class="mt-16 mb-8">
        <div class="max-w-4xl mx-auto bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-6 md:p-10 border-2 border-emerald-200 shadow-lg relative overflow-hidden">
            <!-- الديكور -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-200/30 rounded-full -mr-10 -mt-10"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-teal-200/30 rounded-full -ml-8 -mb-8"></div>
            
            <div class="relative z-10">
                <!-- العنوان -->
                <div class="flex items-center justify-center gap-3 mb-6">
                    <div class="h-px flex-1 bg-gradient-to-l from-emerald-400 to-transparent"></div>
                    <h2 class="text-xl md:text-2xl font-black text-emerald-700 flex items-center gap-2">
                        <i class="fa-solid fa-info-circle"></i>
                        <?php echo e($aboutTitle); ?>

                    </h2>
                    <div class="h-px flex-1 bg-gradient-to-r from-emerald-400 to-transparent"></div>
                </div>
                
                <!-- المحتوى -->
                <p class="text-base md:text-lg text-slate-700 leading-loose text-justify md:text-center font-medium">
                    <?php echo nl2br(e($aboutContent)); ?>

                </p>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto">
        <?php echo $__env->make('partials.popular-keywords', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/Masry/GitHub/ntegty/resources/views/home.blade.php ENDPATH**/ ?>