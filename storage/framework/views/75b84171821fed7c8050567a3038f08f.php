<?php
    // SEO من إعدادات الدولة
    $pageTitle = $country->seo_title ?: "نتائج شهادات {$country->name_ar} | نتيجتي";
    $pageDescription = $country->seo_description ?: "نتائج امتحانات الشهادات في {$country->name_ar} - البحث بالاسم ورقم الجلوس. منصة نتيجتي لعرض النتائج فور اعتمادها.";
    $pageKeywords = $country->seo_keywords ?: "نتائج {$country->name_ar}, امتحانات {$country->name_ar}, شهادات {$country->name_ar}";
?>

<?php $__env->startSection('meta'); ?>
    <title><?php echo e($pageTitle); ?></title>
    <meta name="description" content="<?php echo e($pageDescription); ?>">
    <meta name="keywords" content="<?php echo e($pageKeywords); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($breadcrumbs)): ?>
    <nav class="mb-6 text-sm">
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

    <!-- Page Header -->
    <div class="text-center mb-12">
        <div class="flex items-center justify-center gap-4 mb-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->flag_path): ?>
                <img src="<?php echo e(asset('uploads/' . $country->flag_path)); ?>" 
                     alt="<?php echo e($country->name_ar); ?>" 
                     class="w-20 h-16 object-cover rounded-lg shadow-lg">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <h1 class="text-4xl md:text-5xl font-black text-gray-800 leading-tight">
                <?php echo e($title ?? "نتائج شهادات {$country->name_ar}"); ?>

            </h1>
        </div>
        <p class="text-lg md:text-xl text-gray-600 font-medium">
            اختر نوع الشهادة للاستعلام عن النتيجة
        </p>
    </div>

    <!-- Exam Types Flex Grid -->
    <div class="w-full max-w-6xl mx-auto px-3 flex flex-wrap justify-center gap-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $examTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('country.exam', [$country, $examType->slug])); ?>" 
           class="group block bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border-2 border-blue-100 hover:border-blue-400 transform hover:-translate-y-1 w-full md:w-[calc(50%-12px)] lg:max-w-sm">
            <div class="p-8">
                <!-- Icon -->
                <div class="flex items-center justify-center mb-6">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->level === 'preparatory'): ?>
                        <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform">
                            <i class="fa-solid fa-user-graduate text-3xl text-white"></i>
                        </div>
                    <?php else: ?>
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform">
                            <i class="fa-solid fa-certificate text-3xl text-white"></i>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Title -->
                <h2 class="text-2xl font-bold text-gray-800 text-center mb-3 group-hover:text-blue-600 transition">
                    <?php echo e($examType->name_ar); ?>

                </h2>

                <!-- Description -->
                <p class="text-gray-600 text-center text-sm mb-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->level === 'preparatory'): ?>
                        عرض جميع المحافظات وتحميل النتائج
                    <?php else: ?>
                        البحث الموحد عن النتيجة
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>

                <!-- Arrow Icon -->
                <div class="flex justify-center mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fa-solid fa-arrow-left text-blue-600 text-xl transform group-hover:-translate-x-2 transition-transform"></i>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Info Section -->
    <div class="w-full max-w-6xl mx-auto px-3 mt-12">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-r-4 border-blue-500 rounded-xl p-6 shadow-md">
            <div class="flex items-start gap-4">
                <svg class="w-8 h-8 text-blue-600 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">معلومات هامة</h3>
                    <ul class="text-blue-800 space-y-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $examTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-check text-blue-600 mt-1.5 text-xs"></i>
                            <span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type->level === 'preparatory'): ?>
                                    يمكنك تحميل نتائج <strong><?php echo e($type->name_ar); ?></strong> كملفات PDF أو البحث في قاعدة البيانات المتاحة.
                                <?php else: ?>
                                    الاستعلام عن نتائج <strong><?php echo e($type->name_ar); ?></strong> متاح الآن باستخدام الاسم أو رقم الجلوس وبدون أي رسوم.
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-check text-blue-600 mt-1.5 text-xs"></i>
                            <span>
                                يتم رفع النتائج وتحديث الروابط فور اعتمادها رسمياً من 
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Str::contains($country->name_ar, ['العراق', 'الكويت'])): ?>
                                    <strong>وزارة التربية <?php echo e($country->name_ar == 'العراق' ? 'العراقية' : ''); ?></strong>.
                                <?php else: ?>
                                    <strong>وزارة التربية والتعليم</strong>.
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Box from Country Settings -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->show_content_section && ($country->content_title || $country->content_body)): ?>
    <div class="w-full max-w-6xl mx-auto mt-12 px-3">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6 md:p-10 border border-blue-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->content_title): ?>
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-blue-800 mb-5 pb-3 border-b-2 border-blue-200 flex items-center gap-3">
                <i class="fa-solid fa-globe text-blue-600"></i>
                <?php echo e($country->content_title); ?>

            </h2>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->content_intro): ?>
            <p class="text-blue-700 mb-6 text-base md:text-lg leading-relaxed"><?php echo e($country->content_intro); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($country->content_body): ?>
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-blue-800 prose-headings:mt-6 prose-headings:mb-3
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
    <div class="max-w-4xl mx-auto">
        <?php echo $__env->make('partials.popular-keywords', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ntegty/public_html/resources/views/country/index.blade.php ENDPATH**/ ?>