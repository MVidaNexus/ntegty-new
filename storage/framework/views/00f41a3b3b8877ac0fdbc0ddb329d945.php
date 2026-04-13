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
                    <!-- علم مصر - اليمين -->
                    <div class="flex items-center gap-3">
                        <img src="https://flagcdn.com/w160/eg.png" class="h-12 w-auto object-contain" alt="علم مصر">
                        <div class="text-right">
                            <h2 class="text-lg font-black text-gray-900 leading-tight">جمهورية مصر العربية</h2>
                            <p class="text-sm text-gray-700 font-bold"><?php echo e($certName ?? 'الشهادة الإعدادية'); ?> - محافظة <?php echo e($governorate->name_ar); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($suffix ?? ''); ?></p>
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
            
            <!-- عنوان الأوائل للطباعة -->
            <div class="text-center mb-4">
                <h1 class="text-xl font-black text-gray-900">
                    <i class="fa-solid fa-trophy text-amber-500"></i>
                    العشرة الأوائل - <?php echo e($pageSubtitle ?? $filterLabel); ?>

                </h1>
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

        <!-- Page Header with Trophy -->
        <div class="text-center mb-8 no-print">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-amber-400 to-yellow-500 rounded-full mb-4 shadow-lg">
                <i class="fa-solid fa-trophy text-4xl text-white"></i>
            </div>
            <h1 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-2 leading-tight">
                العشرة الأوائل في الشهادة الإعدادية
            </h1>
            <p class="text-lg md:text-xl font-bold text-emerald-600 mb-1">
                <?php echo e($pageSubtitle ?? $filterLabel); ?>

            </p>
            <p class="text-sm text-gray-500">
                <i class="fa-solid fa-calendar-alt ml-1"></i>
                <?php echo e($suffix ?? ''); ?>

            </p>
        </div>

        <!-- زر الطباعة الرئيسي -->
        <div class="flex justify-center mb-6 no-print">
            <button type="button" onclick="window.print()" 
                    class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl hover:scale-105 cursor-pointer text-lg">
                <i class="fa-solid fa-print text-xl"></i>
                <span>طباعة كشف الأوائل</span>
            </button>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 max-w-3xl mx-auto no-print">
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-4 border-2 border-amber-200 text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fa-solid fa-users text-amber-600 text-xl"></i>
                </div>
                <p class="text-sm text-amber-700 font-medium">إجمالي الطلاب</p>
                <p class="text-2xl font-black text-amber-800"><?php echo e(number_format($totalStudents)); ?></p>
            </div>
            <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl p-4 border-2 border-emerald-200 text-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fa-solid fa-trophy text-emerald-600 text-xl"></i>
                </div>
                <p class="text-sm text-emerald-700 font-medium">أعلى مجموع</p>
                <p class="text-2xl font-black text-emerald-800"><?php echo e($highestScore); ?></p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border-2 border-blue-200 text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <i class="fa-solid fa-star text-blue-600 text-xl"></i>
                </div>
                <p class="text-sm text-blue-700 font-medium">عدد الأوائل</p>
                <p class="text-2xl font-black text-blue-800"><?php echo e($results->count()); ?></p>
            </div>
            <!-- زر الطباعة -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-4 border-2 border-indigo-200 text-center flex flex-col items-center justify-center">
                <button type="button" onclick="window.print()" 
                        class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-2 hover:bg-indigo-200 transition cursor-pointer">
                    <i class="fa-solid fa-print text-indigo-600 text-xl"></i>
                </button>
                <p class="text-sm text-indigo-700 font-medium">طباعة الأوائل</p>
            </div>
        </div>

        <!-- Top Students Cards -->
        <div class="space-y-4 print:space-y-2">
            <?php
                $absentMarkers = $examType?->getAbsentMarkers() ?? \App\Models\ExamType::DEFAULT_ABSENT_MARKERS;
                $medalColors = [
                    1 => 'from-amber-400 to-yellow-500',
                    2 => 'from-slate-300 to-gray-400',
                    3 => 'from-amber-600 to-orange-700',
                ];
                $borderColors = [
                    1 => 'border-amber-400',
                    2 => 'border-slate-400',
                    3 => 'border-amber-600',
                ];
            ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $rankNum = $result->rank;
                $isTopThree = is_numeric($rankNum) && $rankNum <= 3;
                $gradientClass = $medalColors[$rankNum] ?? 'from-blue-500 to-indigo-600';
                $borderClass = $borderColors[$rankNum] ?? 'border-blue-500';
                // Get administration from subjects_data
                $administration = $result->subjects_data['الادارة'] ?? $result->subjects_data['الاداره'] ?? $result->subjects_data['الإدارة'] ?? $result->subjects_data['الإداره'] ?? null;
                $school = $result->subjects_data['المدرسة'] ?? $result->subjects_data['المدرسه'] ?? null;
                $cardId = 'card-' . $result->id;
            ?>
            <div class="bg-white rounded-2xl shadow-lg border-2 <?php echo e($isTopThree ? $borderClass : 'border-gray-200'); ?> overflow-hidden transition-all hover:shadow-xl print:rounded-lg print:shadow-none print:border" x-data="{ showSubjects: false }">
                <!-- Card Header -->
                <div class="bg-gradient-to-r <?php echo e($gradientClass); ?> p-4 text-white print:p-2 print:bg-gray-800">
                    <div class="flex items-center justify-between flex-wrap gap-3 print:gap-2">
                        <div class="flex items-center gap-4 print:gap-2">
                            <!-- Rank Badge -->
                            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm flex-shrink-0 print:w-10 print:h-10">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rankNum == 1): ?>
                                    <i class="fa-solid fa-medal text-3xl sm:text-4xl text-yellow-300 print:text-xl"></i>
                                <?php elseif($rankNum == 2): ?>
                                    <i class="fa-solid fa-medal text-3xl sm:text-4xl text-gray-200 print:text-xl"></i>
                                <?php elseif($rankNum == 3): ?>
                                    <i class="fa-solid fa-medal text-3xl sm:text-4xl text-amber-400 print:text-xl"></i>
                                <?php else: ?>
                                    <span class="text-xl sm:text-2xl font-black print:text-lg"><?php echo e($rankNum); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <!-- Student Info -->
                            <div>
                                <h3 class="text-lg sm:text-xl font-black"><?php echo e($result->student_name); ?></h3>
                                <p class="text-xs sm:text-sm opacity-90">
                                    <i class="fa-solid fa-id-card ml-1"></i>
                                    رقم الجلوس: <?php echo e($result->seat_number); ?>

                                </p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($school): ?>
                                <p class="text-xs opacity-80 mt-1">
                                    <i class="fa-solid fa-school ml-1"></i>
                                    <?php echo e($school); ?>

                                </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($administration): ?>
                                <p class="text-xs opacity-80">
                                    <i class="fa-solid fa-building-columns ml-1"></i>
                                    <?php echo e($administration); ?>

                                </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <!-- Total Score -->
                        <div class="text-center sm:text-left">
                            <p class="text-xs sm:text-sm opacity-90">المجموع</p>
                            <p class="text-2xl sm:text-3xl font-black"><?php echo e($result->total_score); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Card Body -->
                <div class="p-4">
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap justify-center gap-2 mb-3">
                        <button @click="showSubjects = !showSubjects" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold rounded-lg transition text-sm">
                            <i class="fa-solid" :class="showSubjects ? 'fa-eye-slash' : 'fa-eye'"></i>
                            <span x-text="showSubjects ? 'إخفاء المواد' : 'عرض تفاصيل المواد'"></span>
                        </button>
                        <a href="/egypt/preparatory/<?php echo e($governorate->slug); ?>/<?php echo e($academicYear->year ?? '2024-2025'); ?>/<?php echo e(isset($semester) && $semester == 2 ? 'term2' : 'term1'); ?>/<?php echo e($result->seat_number); ?>" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 font-bold rounded-lg transition text-sm">
                            <i class="fa-solid fa-user"></i>
                            صفحة النتيجة - <?php echo e($result->student_name); ?>

                        </a>
                    </div>
                    
                    <!-- Subjects Grid (Hidden by default) -->
                    <div x-show="showSubjects" x-collapse>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2 pt-3 border-t border-gray-100">
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
                                <div class="text-center p-2 rounded-lg <?php echo e($isAbsent ? 'bg-red-50' : 'bg-gray-50'); ?>">
                                    <p class="text-[10px] text-gray-500 font-medium truncate" title="<?php echo e($subject); ?>"><?php echo e($subject); ?></p>
                                    <p class="text-lg font-black <?php echo e($isAbsent ? 'text-red-500' : 'text-gray-800'); ?>">
                                        <?php echo e($isAbsent ? 'غائب' : $subjectScore); ?>

                                    </p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-200 p-12 text-center">
                <i class="fa-solid fa-inbox text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">لا توجد نتائج</p>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <!-- ==================== جدول الطباعة فقط ==================== -->
        <div class="hidden print:block mt-4">
            <div class="bg-white rounded-xl overflow-hidden print:shadow-none print:border-2 print:border-gray-800 print:rounded-none">
                <!-- عنوان الجدول للطباعة -->
                <div class="bg-gray-100 px-3 py-2 border-b-2 border-gray-800">
                    <div class="flex justify-between items-center text-xs">
                        <span>إجمالي: <?php echo e(number_format($totalStudents)); ?> طالب</span>
                        <span class="font-bold">كشف أوائل <?php echo e($pageSubtitle ?? $filterLabel); ?></span>
                        <span>أعلى مجموع: <?php echo e($highestScore); ?></span>
                    </div>
                </div>
                
                <table class="w-full print:text-xs">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="px-2 py-2 text-right text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">الترتيب</th>
                            <th class="px-2 py-2 text-right text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">رقم الجلوس</th>
                            <th class="px-2 py-2 text-right text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">الاسم</th>
                            <th class="px-2 py-2 text-center text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">الإدارة</th>
                            <th class="px-2 py-2 text-center text-xs font-bold print:px-1 print:py-1 print:text-[8pt]">المدرسة</th>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-2 py-2 text-center text-xs font-bold whitespace-nowrap print:px-1 print:py-1 print:text-[7pt]"><?php echo e($subject); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <th class="px-2 py-2 text-center text-xs font-bold bg-gray-900 print:px-1 print:py-1 print:text-[8pt]">المجموع</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-400">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $rankNum = $result->rank;
                            $isTopThree = is_numeric($rankNum) && $rankNum <= 3;
                            $administration = $result->subjects_data['الادارة'] ?? $result->subjects_data['الاداره'] ?? $result->subjects_data['الإدارة'] ?? $result->subjects_data['الإداره'] ?? $result->subjects_data['اسم الإدارة'] ?? $result->subjects_data['اسم الادارة'] ?? '-';
                            $school = $result->subjects_data['المدرسة'] ?? $result->subjects_data['المدرسه'] ?? $result->subjects_data['اسم المدرسة'] ?? $result->subjects_data['اسم المدرسه'] ?? '-';
                        ?>
                        <tr class="<?php echo e($isTopThree ? 'bg-yellow-50' : ($index % 2 == 0 ? 'bg-white' : 'bg-gray-50')); ?>">
                            <td class="px-2 py-2 text-center font-black text-sm print:px-1 print:py-1 print:text-[9pt] <?php echo e($isTopThree ? 'text-amber-600' : 'text-gray-700'); ?>">
                                <?php echo e($rankNum); ?>

                            </td>
                            <td class="px-2 py-2 font-bold text-gray-800 text-sm print:px-1 print:py-1 print:text-[8pt]"><?php echo e($result->seat_number); ?></td>
                            <td class="px-2 py-2 font-semibold text-gray-800 text-sm print:px-1 print:py-1 print:text-[8pt] print:max-w-[100px]"><?php echo e($result->student_name); ?></td>
                            <td class="px-2 py-2 text-center text-xs text-gray-600 print:px-1 print:py-1 print:text-[7pt] print:max-w-[60px] truncate"><?php echo e($administration); ?></td>
                            <td class="px-2 py-2 text-center text-xs text-gray-600 print:px-1 print:py-1 print:text-[7pt] print:max-w-[70px] truncate"><?php echo e($school); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $subjectScore = $result->subjects_data[$subject] ?? '-';
                                ?>
                                <td class="px-2 py-2 text-center text-xs font-medium print:px-1 print:py-1 print:text-[8pt] text-gray-700"><?php echo e($subjectScore); ?></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <td class="px-2 py-2 text-center font-black text-base text-emerald-600 bg-emerald-50 print:px-1 print:py-1 print:text-[9pt] print:text-black print:bg-gray-100"><?php echo e($result->total_score); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
            $yearParam = isset($academicYear) && $academicYear ? 'academic_year_id=' . $academicYear->id : '';
        ?>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type !== 'governorate'): ?>
            <a href="<?php echo e(route('egypt.governorate.top-results', $governorate)); ?><?php echo e($yearParam ? '?' . $yearParam : ''); ?>" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fa-solid fa-medal"></i>
                أوائل المحافظة
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <a href="<?php echo e(route('egypt.governorate.all-results', $governorate)); ?>?<?php echo e($yearParam); ?><?php echo e($type !== 'governorate' && $name ? ($yearParam ? '&' : '') . 'search=' . urlencode($name) : ''); ?>" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fa-solid fa-list"></i>
                عرض جميع النتائج
            </a>
            
            <a href="<?php echo e(route('egypt.governorate.results', $governorate)); ?><?php echo e($yearParam ? '?' . $yearParam : ''); ?>" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                <i class="fa-solid fa-arrow-right"></i>
                العودة للبحث
            </a>
        </div>
        
        <!-- تنويه -->
        <div class="mt-6 p-4 bg-amber-50 border-2 border-amber-300 rounded-xl text-center no-print">
            <p class="text-amber-800 font-bold">
                <i class="fa-solid fa-triangle-exclamation ml-2"></i>
                تنبيه: هذا الكشف غير رسمي - قم بمراجعة مدرستك للتأكد من النتيجة
            </p>
        </div>
        
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($examType) && $examType->show_content_section && ($examType->content_title || $examType->content_body) && !str_contains($examType->code ?? '', 'preparatory')): ?>
        <div class="w-full max-w-6xl mx-auto mt-12 no-print">
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
                
                <article class="gov-content-body-top">
                    <?php echo $governorate->getFormattedContentBody(); ?>

                </article>
                <style>
                    .gov-content-body-top { color: #374151; line-height: 1.7; font-size: 1rem; }
                    .gov-content-body-top * { max-width: 100%; box-sizing: border-box; }
                    .gov-content-body-top h1, .gov-content-body-top h2, .gov-content-body-top h3, .gov-content-body-top h4, .gov-content-body-top h5, .gov-content-body-top h6 { font-weight: 700; color: #1f2937; margin-top: 1.25rem; margin-bottom: 0.5rem; line-height: 1.4; }
                    .gov-content-body-top h2 { font-size: 1.375rem; border-right: 4px solid #10b981; padding-right: 0.75rem; }
                    .gov-content-body-top h3 { font-size: 1.125rem; color: #047857; }
                    .gov-content-body-top p { margin-bottom: 0.75rem; line-height: 1.7; }
                    .gov-content-body-top ul, .gov-content-body-top ol { margin: 0.5rem 0; padding-right: 1.25rem; }
                    .gov-content-body-top ul { list-style-type: disc; }
                    .gov-content-body-top ol { list-style-type: decimal; }
                    .gov-content-body-top li { margin-bottom: 0.25rem; line-height: 1.6; padding: 0.125rem 0; }
                    .gov-content-body-top li p { margin-bottom: 0.25rem; }
                    .gov-content-body-top li br { display: none; }
                    .gov-content-body-top strong { font-weight: 700; color: #111827; }
                    .gov-content-body-top a { color: #059669; text-decoration: underline; }
                    .gov-content-body-top table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.95rem; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
                    .gov-content-body-top table th, .gov-content-body-top table td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: right; }
                    .gov-content-body-top table th { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 700; }
                    .gov-content-body-top table tbody tr:nth-child(even) { background-color: #f9fafb; }
                    .gov-content-body-top table tbody tr:hover { background-color: #ecfdf5; }
                    .gov-content-body-top br + br { display: none; }
                    .gov-content-body-top > br:first-child { display: none; }
                </style>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        
        <?php if(isset($governorate)): ?>
            <?php echo $__env->make('partials.governorates-internal-links', ['currentGovernorateSlug' => $governorate->slug ?? null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
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
            </div>
        </div>
    </div>

    <!-- كلمات البحث الشائعة -->
    <div class="max-w-4xl mx-auto no-print">
        <?php echo $__env->make('partials.popular-keywords', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    /* إعدادات الصفحة */
    @page { 
        size: A4 portrait;
        margin: 8mm;
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
    .breadcrumbs {
        display: none !important;
    }
    
    /* إظهار عناصر الطباعة */
    .print\\:block {
        display: block !important;
    }
    
    /* ترويسة الطباعة */
    .print-header {
        page-break-after: avoid;
    }
    
    /* إخفاء الكروت وإظهار الجدول فقط */
    .space-y-4 {
        display: none !important;
    }
    
    /* الحاوية */
    .max-w-\[1400px\] {
        max-width: none !important;
    }
    
    /* إظهار جدول الطباعة */
    .hidden.print\:block {
        display: block !important;
    }
    
    /* إعدادات الجدول */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 8pt !important;
    }
    
    th, td {
        border: 1px solid #333 !important;
        padding: 3px 4px !important;
    }
    
    th {
        background: #333 !important;
        color: white !important;
    }
    
    /* تمييز الثلاثة الأوائل */
    tr.bg-yellow-50 {
        background: #fef9c3 !important;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ntegty/public_html/resources/views/egypt/governorate-top-results.blade.php ENDPATH**/ ?>