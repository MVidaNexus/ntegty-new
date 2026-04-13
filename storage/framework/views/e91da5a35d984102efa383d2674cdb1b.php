<?php $__env->startSection('structured_data'); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($structuredData)): ?>
<?php echo $structuredData; ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full bg-gradient-to-b from-slate-50 to-white py-6 print:bg-white print:py-0">
    <div class="w-full px-3 max-w-[1400px] mx-auto print:max-w-none print:px-0">
        
        <!-- ==================== قسم الطباعة فقط ==================== -->
        <div class="hidden print:block">
            <!-- ترويسة الطباعة -->
            <div class="print-header mb-4 pb-3 border-b-2 border-gray-800">
                <div class="flex items-center justify-between px-4">
                    <!-- شعار نتيجتي + علم مصر - اليمين -->
                    <div class="flex items-center gap-3">
                        <img src="https://flagcdn.com/w160/eg.png" class="h-12 w-auto object-contain" alt="علم مصر">
                        <div class="text-right">
                            <h2 class="text-lg font-black text-gray-900 leading-tight">جمهورية مصر العربية</h2>
                            <p class="text-sm text-gray-700 font-bold"><?php echo e($certName ?? 'الشهادة الإعدادية'); ?> - محافظة <?php echo e($governorate->name_ar); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($suffix ?? ''); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pageTitle) && $pageTitle): ?> - <?php echo e($pageTitle); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></p>
                        </div>
                    </div>
                    
                    <!-- شعار نتيجتي - الوسط -->
                    <div class="text-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($settings['logo'])): ?>
                            <img src="<?php echo e(asset('uploads/' . $settings['logo'])); ?>" class="h-16 w-auto object-contain mx-auto mb-1" alt="نتيجتي">
                        <?php else: ?>
                            <span class="text-2xl font-black text-emerald-700">نتيجتي</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <p class="text-sm text-gray-700 font-bold">ntegty.com</p>
                    </div>
                    
                    <!-- شعار المحافظة - الشمال -->
                    <div class="text-left">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($governorate->logo_path): ?>
                            <img src="<?php echo e(asset('uploads/' . $governorate->logo_path)); ?>" class="h-14 w-auto object-contain" alt="<?php echo e($governorate->name_ar); ?>">
                        <?php else: ?>
                            <div class="h-14 w-14 bg-emerald-100 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-black text-emerald-700"><?php echo e(mb_substr($governorate->name_ar, 0, 1)); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== الشاشة العادية ==================== -->
        <!-- Breadcrumbs -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($breadcrumbs)): ?>
        <nav class="mb-4 text-sm no-print">
            <ol class="flex items-center gap-2 text-gray-600">
                <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index > 0): ?>
                        <li><i class="fa-solid fa-chevron-left text-xs mx-1"></i></li>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($crumb['url'])): ?>
                            <a href="<?php echo e($crumb['url']); ?>" class="hover:text-emerald-600 transition-colors"><?php echo e($crumb['name']); ?></a>
                        <?php else: ?>
                            <span class="text-gray-800 font-semibold"><?php echo e($crumb['name']); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ol>
        </nav>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Page Title -->
        <div class="text-center mb-6 no-print">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pageTitle) && $pageTitle): ?>
                <!-- Dynamic Title for Search (School/Administration) -->
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-5 border-2 border-emerald-200 mb-4">
                    <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-2 leading-tight">
                        <i class="fa-solid fa-search text-emerald-600 ml-2"></i>
                        نتائج <?php echo e($pageTitle); ?>

                    </h1>
                    <p class="text-base md:text-lg font-bold text-emerald-600 mb-1">
                        <?php echo e($certName ?? 'الشهادة الإعدادية'); ?> - محافظة <?php echo e($governorate->name_ar); ?>

                    </p>
                    <p class="text-sm text-gray-500">
                        <i class="fa-solid fa-calendar-alt ml-1"></i>
                        <?php echo e($suffix ?? ''); ?>

                    </p>
                </div>
            <?php else: ?>
                <!-- Default Title -->
                <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-2 leading-tight">
                    نتائج <?php echo e($certName ?? 'الشهادة الإعدادية'); ?>

                </h1>
                <p class="text-lg md:text-xl font-bold text-emerald-600 mb-1">
                    محافظة <?php echo e($governorate->name_ar); ?>

                </p>
                <p class="text-sm text-gray-500">
                    <i class="fa-solid fa-calendar-alt ml-1"></i>
                    <?php echo e($suffix ?? ''); ?>

                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- Action Buttons - Top -->
        <?php
            $search = request('search', '');
            $topUrl = route('egypt.governorate.top-results', $governorate);
            $yearParam = request('academic_year_id') ? 'academic_year_id=' . request('academic_year_id') : '';
            if ($search) {
                if (str_contains($search, 'مدرسة') || str_contains($search, 'مدرسه') || str_contains($search, 'اعدادي') || str_contains($search, 'إعدادي') || str_contains($search, 'ابتدائي')) {
                    $firstResult = $results->first();
                    $admin = $firstResult?->subjects_data['الادارة'] ?? $firstResult?->subjects_data['الاداره'] ?? $firstResult?->subjects_data['الإدارة'] ?? '';
                    $topUrl = route('egypt.governorate.top-results', $governorate) . '?' . http_build_query(['type' => 'school', 'name' => $search, 'admin' => $admin]);
                } elseif (str_contains($search, 'إدارة') || str_contains($search, 'ادارة') || str_contains($search, 'التعليمية')) {
                    $topUrl = route('egypt.governorate.top-results', $governorate) . '?' . http_build_query(['type' => 'admin', 'name' => $search]);
                }
            }
        ?>
        
        <div class="mb-6 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl p-4 border border-emerald-200 no-print">
            <div class="flex flex-wrap justify-between items-center gap-4">
                <!-- Back Button -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pageTitle) && $pageTitle): ?>
                <a href="<?php echo e(route('egypt.governorate.all-results', $governorate)); ?><?php echo e($yearParam ? '?' . $yearParam : ''); ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-arrow-right"></i>
                    <span>العودة لكل النتائج</span>
                </a>
                <?php else: ?>
                <a href="<?php echo e(route('egypt.governorate.results', $governorate)); ?><?php echo e($yearParam ? '?' . $yearParam : ''); ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-search"></i>
                    <span>البحث عن طالب</span>
                </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <!-- Quick Actions -->
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-sm text-emerald-700 font-medium hidden sm:inline">
                        <i class="fa-solid fa-bolt ml-1"></i>
                        إجراءات سريعة:
                    </span>
                    
                    <!-- Top Results Button -->
                    <a href="<?php echo e($topUrl); ?>" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg hover:scale-105">
                        <i class="fa-solid fa-trophy"></i>
                        <span>عرض الأوائل</span>
                    </a>
                    
                    <!-- Print Button -->
                    <button type="button" onclick="window.print()" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg hover:scale-105 cursor-pointer">
                        <i class="fa-solid fa-print"></i>
                        <span>طباعة الصفحة</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 no-print">
            <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-emerald-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-users text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">إجمالي النتائج</p>
                        <p class="text-2xl font-black text-gray-800"><?php echo e(number_format($stats['total'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-green-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">ناجح</p>
                        <p class="text-2xl font-black text-green-600"><?php echo e(number_format($stats['passed'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-red-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">راسب</p>
                        <p class="text-2xl font-black text-red-600"><?php echo e(number_format($stats['failed'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-blue-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-trophy text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">أعلى مجموع</p>
                        <p class="text-2xl font-black text-blue-600"><?php echo e($stats['highest']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter (hidden from UI but works via URL) -->
        <div class="hidden">
            <form id="searchForm" method="GET" action="<?php echo e(url()->current()); ?>">
                <input type="hidden" name="search" value="<?php echo e(request('search', '')); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('academic_year_id')): ?>
                <input type="hidden" name="academic_year_id" value="<?php echo e(request('academic_year_id')); ?>">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </form>
        </div>

        <!-- ==================== جدول النتائج (كشف بيان الدرجات) ==================== -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden print:shadow-none print:border-2 print:border-gray-800 print:rounded-none">
            <!-- عنوان الجدول للطباعة -->
            <div class="hidden print:block bg-gray-100 px-3 py-2 border-b-2 border-gray-800">
                <div class="flex justify-between items-center text-xs">
                    <span>إجمالي: <?php echo e(number_format($stats['total'])); ?> طالب</span>
                    <span class="font-bold">كشف بيان الدرجات</span>
                    <span>ناجح: <?php echo e(number_format($stats['passed'])); ?> | راسب: <?php echo e(number_format($stats['failed'])); ?></span>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full print:text-xs">
                    <thead class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white print:bg-gray-800 print:text-white">
                        <tr>
                            <th class="px-2 py-2 text-right text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">#</th>
                            <th class="px-2 py-2 text-right text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">رقم الجلوس</th>
                            <th class="px-2 py-2 text-right text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">الاسم</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAdministration ?? false): ?>
                                <th class="px-2 py-2 text-center text-xs font-bold whitespace-nowrap bg-emerald-700/50 print:px-1 print:py-1 print:text-[7pt] print:bg-gray-700">الإدارة</th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSchool ?? false): ?>
                                <th class="px-2 py-2 text-center text-xs font-bold whitespace-nowrap bg-emerald-700/50 print:px-1 print:py-1 print:text-[7pt] print:bg-gray-700">المدرسة</th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-2 py-2 text-center text-xs font-bold whitespace-nowrap print:px-1 print:py-1 print:text-[7pt]"><?php echo e($subject); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th class="px-2 py-2 text-center text-xs font-bold bg-emerald-700 print:px-1 print:py-1 print:text-[8pt] print:bg-gray-900">المجموع</th>
                            <th class="px-2 py-2 text-center text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">الحالة</th>
                            <th class="px-2 py-2 text-center text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">الترتيب</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 print:divide-gray-400">
                        <?php
                            $absentMarkers = $examType?->getAbsentMarkers() ?? \App\Models\ExamType::DEFAULT_ABSENT_MARKERS;
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $administration = $result->subjects_data['الادارة'] ?? $result->subjects_data['الاداره'] ?? $result->subjects_data['الإدارة'] ?? $result->subjects_data['الإداره'] ?? '-';
                            $school = $result->subjects_data['المدرسة'] ?? $result->subjects_data['المدرسه'] ?? '-';
                            $rankNum = $result->rank ?? '-';
                            $isTopTen = is_numeric($rankNum) && $rankNum <= 10;
                        ?>
                        <tr class="hover:bg-emerald-50/50 transition-colors <?php echo e($index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50'); ?> <?php echo e($isTopTen ? 'print:bg-yellow-50' : ''); ?> print:hover:bg-transparent">
                            <td class="px-2 py-2 text-gray-500 text-xs print:px-1 print:py-1 print:text-[8pt]"><?php echo e($results->firstItem() + $index); ?></td>
                            <td class="px-2 py-2 font-bold text-gray-800 text-sm print:px-1 print:py-1 print:text-[8pt]">
                                <a href="/egypt/preparatory/<?php echo e($governorate->slug); ?>/<?php echo e($academicYear->year ?? '2024-2025'); ?>/<?php echo e(isset($semester) && $semester == 2 ? 'term2' : 'term1'); ?>/<?php echo e($result->seat_number); ?>" 
                                   class="hover:text-emerald-600 hover:underline no-print">
                                    <?php echo e($result->seat_number); ?>

                                </a>
                                <span class="hidden print:inline"><?php echo e($result->seat_number); ?></span>
                            </td>
                            <td class="px-2 py-2 font-semibold text-gray-800 text-sm print:px-1 print:py-1 print:text-[8pt] print:max-w-[80px] print:truncate">
                                <a href="/egypt/preparatory/<?php echo e($governorate->slug); ?>/<?php echo e($academicYear->year ?? '2024-2025'); ?>/<?php echo e(isset($semester) && $semester == 2 ? 'term2' : 'term1'); ?>/<?php echo e($result->seat_number); ?>" 
                                   class="hover:text-emerald-600 hover:underline no-print">
                                    <?php echo e($result->student_name); ?>

                                </a>
                                <span class="hidden print:inline"><?php echo e($result->student_name); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isTopTen): ?>
                                    <span class="inline-flex items-center mr-1 no-print">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rankNum == 1): ?>
                                            <i class="fa-solid fa-medal text-yellow-500"></i>
                                        <?php elseif($rankNum == 2): ?>
                                            <i class="fa-solid fa-medal text-gray-400"></i>
                                        <?php elseif($rankNum == 3): ?>
                                            <i class="fa-solid fa-medal text-amber-600"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-star text-xs text-amber-500"></i>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasAdministration ?? false): ?>
                                <td class="px-2 py-2 text-center text-xs text-gray-600 bg-gray-50 print:px-1 print:py-1 print:text-[7pt] print:max-w-[60px] print:truncate"><?php echo e($administration); ?></td>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasSchool ?? false): ?>
                                <td class="px-2 py-2 text-center text-xs text-gray-600 bg-gray-50 max-w-[150px] truncate print:px-1 print:py-1 print:text-[7pt] print:max-w-[70px]" title="<?php echo e($school); ?>"><?php echo e($school); ?></td>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $subjectScore = $result->subjects_data[$subject] ?? '-';
                                    $scoreStr = trim((string)$subjectScore);
                                    $scoreLower = mb_strtolower($scoreStr);
                                    $isAbsent = false;
                                    foreach ($absentMarkers as $marker) {
                                        if ($scoreLower === mb_strtolower(trim($marker))) {
                                            $isAbsent = true;
                                            break;
                                        }
                                    }
                                    if (!$isAbsent && mb_strlen($scoreStr) <= 5 && str_starts_with($scoreStr, 'غ')) {
                                        $isAbsent = true;
                                    }
                                ?>
                                <td class="px-2 py-2 text-center text-xs font-medium print:px-1 print:py-1 print:text-[8pt] <?php echo e($isAbsent ? 'text-red-500 bg-red-50 print:text-red-600' : 'text-gray-700'); ?>">
                                    <?php echo e($isAbsent ? 'غ' : $subjectScore); ?>

                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <td class="px-2 py-2 text-center font-black text-base text-emerald-600 bg-emerald-50 print:px-1 print:py-1 print:text-[9pt] print:text-black print:bg-gray-100">
                                <?php echo e($result->total_score); ?>

                            </td>
                            <td class="px-2 py-2 text-center print:px-1 print:py-1">
                                <?php
                                    $status = $result->status;
                                    $semester = $result->semester ?? 0;
                                    if ($examType && $examType->auto_calculate_status) {
                                        $status = $examType->calculateStatus($result->total_score, $semester);
                                    }
                                ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold print:px-1 print:py-0 print:text-[7pt] print:rounded-none
                                    <?php echo e($status === 'ناجح' ? 'bg-green-100 text-green-700 print:bg-transparent print:text-green-700' : 'bg-red-100 text-red-700 print:bg-transparent print:text-red-700'); ?>">
                                    <?php echo e($status ?: '-'); ?>

                                </span>
                            </td>
                            <td class="px-2 py-2 text-center print:px-1 print:py-1">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold print:w-auto print:h-auto print:rounded-none print:text-[8pt]
                                    <?php echo e($isTopTen ? 'bg-amber-100 text-amber-700 print:text-amber-700 print:font-black' : 'bg-blue-100 text-blue-700 print:bg-transparent'); ?>">
                                    <?php echo e($rankNum); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e(count($subjects) + 6 + (($hasAdministration ?? false) ? 1 : 0) + (($hasSchool ?? false) ? 1 : 0)); ?>" class="px-4 py-12 text-center text-gray-500">
                                <i class="fa-solid fa-inbox text-4xl mb-3 block"></i>
                                لا توجد نتائج
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($results->hasPages()): ?>
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 no-print">
                <?php echo e($results->appends(request()->query())->links()); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- تذييل الطباعة -->
        <div class="hidden print:block mt-4 pt-3 border-t-2 border-gray-800">
            <div class="flex items-center justify-between px-4">
                <!-- التحذير -->
                <p class="text-sm font-bold text-red-600"><i class="fa-solid fa-triangle-exclamation"></i> هذا الكشف غير رسمي - راجع مدرستك <i class="fa-solid fa-triangle-exclamation"></i></p>
                
                <!-- لوجو نتيجتي والدومين -->
                <div class="flex items-center gap-2">
                    <span class="text-sm font-black text-emerald-700">نتيجتي</span>
                    <span class="text-xs text-gray-500">|</span>
                    <span class="text-xs font-bold text-gray-700">ntegty.com</span>
                </div>
                
                <!-- رقم الصفحة -->
                <p class="text-xs text-gray-500">صفحة <?php echo e($results->currentPage()); ?> من <?php echo e($results->lastPage()); ?></p>
            </div>
        </div>

        <!-- تنويه للشاشة -->
        <div class="mt-6 p-4 bg-amber-50 border-2 border-amber-300 rounded-xl text-center no-print">
            <p class="text-amber-800 font-bold">
                <i class="fa-solid fa-triangle-exclamation ml-2"></i>
                تنبيه: هذا الكشف غير رسمي - قم بمراجعة مدرستك للتأكد من النتيجة
            </p>
        </div>

        <!-- Action Buttons - Bottom -->
        <div class="mt-6 bg-gradient-to-r from-slate-50 to-gray-50 rounded-xl p-5 border border-gray-200 no-print">
            <p class="text-center text-gray-600 text-sm mb-4">
                <i class="fa-solid fa-hand-point-down ml-2 text-emerald-600"></i>
                للاطلاع على تفاصيل أكثر أو طباعة الكشف، اختر من الأزرار التالية:
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <!-- Top Results Button -->
                <a href="<?php echo e($topUrl); ?>" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105">
                    <i class="fa-solid fa-trophy text-lg"></i>
                    <span>العشرة الأوائل</span>
                </a>
                
                <!-- Print Button -->
                <button type="button" onclick="window.print()" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105 cursor-pointer">
                    <i class="fa-solid fa-print text-lg"></i>
                    <span>طباعة الصفحة</span>
                </button>
                
                <!-- Search Button -->
                <a href="<?php echo e(route('egypt.governorate.results', $governorate)); ?><?php echo e($yearParam ? '?' . $yearParam : ''); ?>" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl transition-all shadow-lg hover:shadow-xl border-2 border-gray-200 hover:border-emerald-300">
                    <i class="fa-solid fa-search text-lg text-emerald-600"></i>
                    <span>البحث عن طالب</span>
                </a>
            </div>
        </div>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($governorate) && $governorate->show_content_section && ($governorate->content_title || $governorate->content_body)): ?>
        <div class="w-full max-w-6xl mx-auto mt-8 no-print">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100 overflow-hidden">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($governorate->content_title): ?>
                <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-gray-100"><?php echo e($governorate->content_title); ?></h2>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($governorate->content_intro): ?>
                <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed"><?php echo e($governorate->content_intro); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($governorate->content_body): ?>
                
                <article class="gov-content-body-all">
                    <?php echo $governorate->getFormattedContentBody(); ?>

                </article>
                <style>
                    .gov-content-body-all { color: #374151; line-height: 1.7; font-size: 1rem; }
                    .gov-content-body-all * { max-width: 100%; box-sizing: border-box; }
                    .gov-content-body-all h1, .gov-content-body-all h2, .gov-content-body-all h3, .gov-content-body-all h4, .gov-content-body-all h5, .gov-content-body-all h6 { font-weight: 700; color: #1f2937; margin-top: 1.25rem; margin-bottom: 0.5rem; line-height: 1.4; }
                    .gov-content-body-all h2 { font-size: 1.375rem; border-right: 4px solid #10b981; padding-right: 0.75rem; }
                    .gov-content-body-all h3 { font-size: 1.125rem; color: #047857; }
                    .gov-content-body-all p { margin-bottom: 0.75rem; line-height: 1.7; }
                    .gov-content-body-all ul, .gov-content-body-all ol { margin: 0.5rem 0; padding-right: 1.25rem; }
                    .gov-content-body-all ul { list-style-type: disc; }
                    .gov-content-body-all ol { list-style-type: decimal; }
                    .gov-content-body-all li { margin-bottom: 0.25rem; line-height: 1.6; padding: 0.125rem 0; }
                    .gov-content-body-all li p { margin-bottom: 0.25rem; }
                    .gov-content-body-all li br { display: none; }
                    .gov-content-body-all strong { font-weight: 700; color: #111827; }
                    .gov-content-body-all a { color: #059669; text-decoration: underline; }
                    .gov-content-body-all table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.95rem; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                    .gov-content-body-all table th, .gov-content-body-all table td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: right; }
                    .gov-content-body-all table th { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 700; }
                    .gov-content-body-all table tbody tr:nth-child(even) { background-color: #f9fafb; }
                    .gov-content-body-all table tbody tr:hover { background-color: #ecfdf5; }
                    .gov-content-body-all br + br { display: none; }
                    .gov-content-body-all > br:first-child { display: none; }
                </style>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        
        <?php if(isset($governorate)): ?>
            <?php echo $__env->make('partials.governorates-internal-links', ['currentGovernorateSlug' => $governorate->slug ?? null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($examType) && $examType->show_content_section && ($examType->content_title || $examType->content_body) && !str_contains($examType->code ?? '', 'preparatory')): ?>
        <div class="w-full max-w-6xl mx-auto mt-8 no-print">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->content_title): ?>
                <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-gray-100"><?php echo e($examType->content_title); ?></h2>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->content_intro): ?>
                <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed"><?php echo e($examType->content_intro); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->content_body): ?>
                <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                            prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                            prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-emerald-500 prose-h2:pr-4 prose-h2:py-1
                            prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-emerald-700
                            prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                            prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                            prose-a:text-emerald-600 prose-a:hover:text-emerald-700">
                    <?php echo $examType->getFormattedContentBody(); ?>

                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- كلمات البحث الشائعة -->
        <div class="max-w-4xl mx-auto no-print">
            <?php echo $__env->make('partials.popular-keywords', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    /* إعدادات الصفحة - عرضي */
    @page { 
        size: A4 landscape;
        margin: 5mm;
    }
    
    /* إعدادات عامة */
    * { 
        -webkit-print-color-adjust: exact !important; 
        print-color-adjust: exact !important;
        box-sizing: border-box !important;
    }
    
    body, html { 
        background: white !important; 
        margin: 0 !important;
        padding: 0 !important;
        font-size: 10pt !important;
    }
    
    /* إخفاء العناصر غير المطلوبة */
    .no-print,
    nav,
    header,
    footer,
    .breadcrumbs,
    .pagination {
        display: none !important;
    }
    
    /* إظهار عناصر الطباعة */
    .print\\:block {
        display: block !important;
    }
    .print\\:inline {
        display: inline !important;
    }
    
    /* ترويسة الطباعة */
    .print-header {
        page-break-after: avoid;
    }
    .print-header h1 {
        font-size: 14pt !important;
    }
    .print-header h2 {
        font-size: 12pt !important;
    }
    .print-header h3 {
        font-size: 11pt !important;
    }
    
    /* الجدول */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 7pt !important;
    }
    
    thead {
        display: table-header-group !important;
    }
    
    th, td {
        border: 1px solid #333 !important;
        padding: 2px 3px !important;
    }
    
    th {
        background: #333 !important;
        color: white !important;
        font-size: 7pt !important;
        white-space: nowrap !important;
    }
    
    td {
        font-size: 7pt !important;
    }
    
    /* صفوف الأوائل */
    tr.print\\:bg-yellow-50 {
        background: #fef9c3 !important;
    }
    
    /* الحاوية */
    .max-w-\\[1400px\\] {
        max-width: none !important;
    }
    
    /* منع انقسام الصفوف */
    tr {
        page-break-inside: avoid !important;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ntegty/public_html/resources/views/egypt/governorate-all-results.blade.php ENDPATH**/ ?>