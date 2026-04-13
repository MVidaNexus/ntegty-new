
<div class="max-w-6xl mx-auto">
    
    <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'result-before-content'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

    <!-- Header -->
    <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-xl p-6 mb-6 border border-blue-100">
        <h1 class="text-2xl md:text-3xl font-black text-center text-gray-800 mb-3">
            <?php echo e($title); ?>

        </h1>
        <p class="text-center text-gray-600 text-sm md:text-base">
            <i class="fa-solid fa-globe text-blue-500 ml-2"></i>
            يتم عرض النتيجة من مصدر خارجي
        </p>
    </div>

    
    <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'result-in-article'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

    <!-- Embed Container -->
    <?php
        // استخدام المصدر الممرر (محافظة أو نوع امتحان)
        $embedSource = $source ?? $examType;
        
        $embedCode = $embedSource->embed_code ?? '';
        $isUrl = filter_var($embedCode, FILTER_VALIDATE_URL);
        
        // Crop settings - from all 4 sides
        $cropEnabled = $embedSource->iframe_crop_enabled ?? false;
        $cropTop = (int) ($embedSource->iframe_crop_top ?? 0);
        $cropRight = (int) ($embedSource->iframe_crop_right ?? 0);
        $cropBottom = (int) ($embedSource->iframe_crop_bottom ?? 0);
        $cropLeft = (int) ($embedSource->iframe_crop_left ?? 0);
        $zoom = (float) ($embedSource->iframe_zoom ?? 1.0);
        
        // Check if any crop is set
        $hasCrop = $cropEnabled && ($cropTop > 0 || $cropRight > 0 || $cropBottom > 0 || $cropLeft > 0);
        
        // حساب الارتفاع المرئي بعد القص
        $iframeHeight = 2000; // ارتفاع كبير يكفي لأي صفحة
        $visibleHeight = $iframeHeight - $cropTop - $cropBottom;
        
        // Responsive scaling للموبايل
        // على الموبايل نصغر الـ iframe ليناسب الشاشة
        $mobileScale = 0.5; // 50% على الموبايل
        $tabletScale = 0.75; // 75% على التابلت
    ?>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
        
        <div class="iframe-scroll-hint">
            <i class="fa-solid fa-arrows-left-right"></i>
            <span>اسحب يميناً ويساراً لعرض المحتوى كاملاً</span>
        </div>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isUrl): ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasCrop): ?>
                
                <div class="iframe-crop-container iframe-responsive" 
                     data-crop-top="<?php echo e($cropTop); ?>"
                     data-crop-right="<?php echo e($cropRight); ?>"
                     data-crop-bottom="<?php echo e($cropBottom); ?>"
                     data-crop-left="<?php echo e($cropLeft); ?>"
                     data-zoom="<?php echo e($zoom); ?>"
                     style="
                        --crop-top: <?php echo e($cropTop); ?>px;
                        --crop-right: <?php echo e($cropRight); ?>px;
                        --crop-bottom: <?php echo e($cropBottom); ?>px;
                        --crop-left: <?php echo e($cropLeft); ?>px;
                        --zoom: <?php echo e($zoom); ?>;
                        --iframe-height: <?php echo e($iframeHeight); ?>px;
                        --visible-height: <?php echo e($visibleHeight); ?>px;
                        position: relative;
                        width: 100%;
                        height: min(<?php echo e($visibleHeight); ?>px, 80vh);
                        overflow: hidden;
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                    ">
                    <iframe 
                        src="<?php echo e($embedCode); ?>" 
                        class="border-0 iframe-content"
                        style="
                            position: absolute;
                            top: calc(-1 * var(--crop-top));
                            left: calc(-1 * var(--crop-left));
                            width: calc(100% + var(--crop-left) + var(--crop-right));
                            height: var(--iframe-height);
                            transform: scale(var(--zoom));
                            transform-origin: top left;
                            min-width: 800px;
                        "
                        loading="lazy"
                        allow="fullscreen"
                        sandbox="allow-same-origin allow-scripts allow-forms allow-popups"
                    ></iframe>
                </div>
            <?php else: ?>
                
                <div class="iframe-responsive-wrapper" style="
                    position: relative;
                    width: 100%;
                    height: 80vh;
                    max-height: 800px;
                    overflow: hidden;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                ">
                    <iframe 
                        src="<?php echo e($embedCode); ?>" 
                        class="w-full border-0"
                        style="min-height: 100%; height: 100%; min-width: 800px;"
                        loading="lazy"
                        allow="fullscreen"
                        sandbox="allow-same-origin allow-scripts allow-forms allow-popups"
                    ></iframe>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php elseif(!empty($embedCode)): ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasCrop): ?>
                <div class="iframe-crop-container iframe-responsive" style="
                    --crop-top: <?php echo e($cropTop); ?>px;
                    --crop-left: <?php echo e($cropLeft); ?>px;
                    --crop-right: <?php echo e($cropRight); ?>px;
                    --iframe-height: <?php echo e($iframeHeight); ?>px;
                    --visible-height: <?php echo e($visibleHeight); ?>px;
                    --zoom: <?php echo e($zoom); ?>;
                    position: relative;
                    width: 100%;
                    height: min(<?php echo e($visibleHeight); ?>px, 80vh);
                    overflow: hidden;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                ">
                    <div style="
                        position: absolute;
                        top: calc(-1 * var(--crop-top));
                        left: calc(-1 * var(--crop-left));
                        width: calc(100% + var(--crop-left) + var(--crop-right));
                        height: var(--iframe-height);
                        transform: scale(var(--zoom));
                        transform-origin: top left;
                        min-width: 800px;
                    ">
                        <?php echo $embedCode; ?>

                    </div>
                </div>
            <?php else: ?>
                <div class="embed-container" style="min-height: 80vh;">
                    <?php echo $embedCode; ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            
            <div class="flex flex-col items-center justify-center py-20 text-gray-500">
                <i class="fa-solid fa-code text-6xl mb-4 opacity-50"></i>
                <h3 class="text-xl font-bold mb-2">لم يتم تكوين الإيفريم</h3>
                <p class="text-sm">يرجى التواصل مع الإدارة لإعداد رابط النتيجة</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-amber-500 text-xl mt-0.5"></i>
            <div>
                <h4 class="font-bold text-amber-800 mb-1">ملاحظة هامة</h4>
                <p class="text-amber-700 text-sm">
                    يتم عرض النتيجة من موقع خارجي. في حالة عدم ظهور النتيجة، يرجى:
                </p>
                <ul class="list-disc list-inside text-amber-700 text-sm mt-2 space-y-1">
                    <li>تحديث الصفحة والمحاولة مرة أخرى</li>
                    <li>التأكد من اتصال الإنترنت</li>
                    <li>تجربة متصفح آخر</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Popular Searches -->

    
    <?php if (isset($component)) { $__componentOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa4ff1bcd31c19df0f7c5c977e6eaf88 = $attributes; } ?>
<?php $component = App\View\Components\AdUnit::resolve(['slug' => 'result-after-content'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

</div>

<style>
.embed-container iframe {
    width: 100%;
    min-height: 80vh;
    border: none;
}

/* Responsive iframe styles */
.iframe-responsive-wrapper,
.iframe-crop-container.iframe-responsive {
    /* تمكين التمرير الأفقي على الشاشات الصغيرة */
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
}

/* تخصيص scrollbar */
.iframe-responsive-wrapper::-webkit-scrollbar,
.iframe-crop-container.iframe-responsive::-webkit-scrollbar {
    height: 8px;
}

.iframe-responsive-wrapper::-webkit-scrollbar-track,
.iframe-crop-container.iframe-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.iframe-responsive-wrapper::-webkit-scrollbar-thumb,
.iframe-crop-container.iframe-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.iframe-responsive-wrapper::-webkit-scrollbar-thumb:hover,
.iframe-crop-container.iframe-responsive::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* تصغير على الموبايل مع الحفاظ على إمكانية التمرير */
@media (max-width: 768px) {
    .iframe-crop-container.iframe-responsive,
    .iframe-responsive-wrapper {
        height: 60vh !important;
        max-height: 500px;
    }
    
    .iframe-crop-container.iframe-responsive iframe,
    .iframe-crop-container.iframe-responsive > div,
    .iframe-responsive-wrapper iframe {
        /* تصغير المحتوى على الموبايل */
        transform: scale(calc(var(--zoom, 1) * 0.6)) !important;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .iframe-crop-container.iframe-responsive,
    .iframe-responsive-wrapper {
        height: 70vh !important;
        max-height: 600px;
    }
    
    .iframe-crop-container.iframe-responsive iframe,
    .iframe-crop-container.iframe-responsive > div,
    .iframe-responsive-wrapper iframe {
        /* تصغير قليل على التابلت */
        transform: scale(calc(var(--zoom, 1) * 0.8)) !important;
    }
}

/* رسالة تنبيه للتمرير على الموبايل */
.iframe-scroll-hint {
    display: none;
}

@media (max-width: 768px) {
    .iframe-scroll-hint {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 16px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px 8px 0 0;
    }
    
    .iframe-scroll-hint i {
        animation: swipe 1.5s ease-in-out infinite;
    }
    
    @keyframes swipe {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(-5px); }
    }
}
</style>
<?php /**PATH /home/ntegty/public_html/resources/views/partials/result-embed.blade.php ENDPATH**/ ?>