<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['country' => 'egypt', 'type' => 'preparatory', 'governorate' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['country' => 'egypt', 'type' => 'preparatory', 'governorate' => null]); ?>
<?php foreach (array_filter((['country' => 'egypt', 'type' => 'preparatory', 'governorate' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    use App\Models\ResultSchedule;
    use App\Models\Governorate;
    use App\Models\ExamType;
    use App\Models\Country;
    use App\Models\UploadLog;
    use App\Models\AcademicYear;
    
    // Check if result is declared (from governorate or exam type)
    $isDeclared = false;
    $govName = null;
    $announcementText = null;
    $announcementSubtext = null;
    $examType = null;
    $countryData = null;
    $academicYear = null;
    $gov = null;
    
    // Get country data
    if ($country) {
        $countryData = Country::where('slug', $country)->first();
    }
    
    // Get exam type first (we need it for the announcement text)
    if ($type) {
        $examType = ExamType::where('code', $type)->first();
    }
    
    // Check governorate
    if ($governorate) {
        $gov = Governorate::where('slug', $governorate)->first();
        if ($gov) {
            $govName = $gov->name_ar ?? $gov->name;
            if ($gov->is_declared) {
                $isDeclared = true;
                
                // Get academic year from the latest upload log for this governorate
                $latestUpload = UploadLog::where('governorate_id', $gov->id)
                    ->where('status', 'completed')
                    ->whereNotNull('academic_year_id')
                    ->latest()
                    ->first();
                
                if ($latestUpload && $latestUpload->academic_year_id) {
                    $academicYearModel = AcademicYear::find($latestUpload->academic_year_id);
                    if ($academicYearModel) {
                        $academicYear = $academicYearModel->year;
                    }
                }
            }
        }
    }
    
    // Fallback to country academic year if not found from upload
    if (!$academicYear && $countryData) {
        $academicYear = $countryData->academic_year;
    }
    
    // Check exam type approval (for unified exams like secondary)
    if (!$isDeclared && $examType && $examType->is_result_approved) {
        $isDeclared = true;
    }
    
    // Get announcement text from ExamType if set
    if ($isDeclared && $examType) {
        if ($examType->result_announcement_text) {
            $lines = explode("\n", $examType->result_announcement_text);
            $announcementText = trim($lines[0] ?? '');
            $announcementSubtext = trim($lines[1] ?? '');
        }
        
        // If no custom text, build dynamic text with exam name, governorate and year
        if (empty($announcementText)) {
            $textParts = ["تم اعتماد نتيجة {$examType->name_ar}"];
            if ($govName) {
                $textParts[] = "محافظة {$govName}";
            }
            if ($academicYear) {
                $textParts[] = $academicYear;
            }
            $textParts[] = "رسمياً";
            $announcementText = implode(' ', $textParts);
        }
        if (empty($announcementSubtext)) {
            $announcementSubtext = "النتيجة متاحة الآن - يمكنك البحث برقم الجلوس";
        }
    }
    
    // If result is declared, show the declared banner
    if ($isDeclared) {
        // Show declared banner and exit
?>

<div class="w-full no-print">
    <!-- Result Declared Banner -->
    <div class="bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                
                <!-- Success Icon -->
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border-4 border-white/40">
                        <i class="fa-solid fa-check-double text-white text-3xl sm:text-4xl"></i>
                    </div>
                </div>
                
                <!-- Info Section -->
                <div class="text-center sm:text-right flex-1">
                    <div class="flex items-center justify-center sm:justify-start gap-3 mb-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black text-white">
                            <?php echo e($announcementText ?? 'تم اعتماد النتيجة رسمياً'); ?>

                        </h2>
                    </div>
                    
                    <p class="text-green-100 text-sm sm:text-base">
                        <?php echo e($announcementSubtext ?? 'النتيجة متاحة الآن - يمكنك البحث برقم الجلوس'); ?>

                    </p>
                </div>
                
                <!-- Badge -->
                <div class="flex-shrink-0">
                    <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg px-4 py-2 sm:px-6 sm:py-3">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-award text-yellow-300 text-xl sm:text-2xl"></i>
                            <span class="text-white font-bold text-sm sm:text-base">معتمدة</span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Success Bar -->
        <div class="h-1.5 w-full bg-green-700/50">
            <div class="h-full bg-gradient-to-r from-white/80 to-white/60 rounded-r-full w-full"></div>
        </div>
    </div>
</div>

<?php
        return;
    }
    
    // Get schedule from database ONLY - no fallback
    $schedule = ResultSchedule::getSchedule($country, $type, $governorate);
    
    // If no schedule in database, don't show timer at all
    if (!$schedule) {
        return;
    }
    
    $targetDate = $schedule->expected_date->format('Y-m-d H:i:s');
    $note = $schedule->note;
    
    $months = [
        '01' => 'يناير', '02' => 'فبراير', '03' => 'مارس', '04' => 'أبريل', 
        '05' => 'مايو', '06' => 'يونيو', '07' => 'يوليو', '08' => 'أغسطس', 
        '09' => 'سبتمبر', '10' => 'أكتوبر', '11' => 'نوفمبر', '12' => 'ديسمبر'
    ];
    $dateObj = \Carbon\Carbon::parse($targetDate);
    $formattedDate = $dateObj->day . ' ' . $months[$dateObj->format('m')] . ' ' . $dateObj->year;
    $formattedTime = $dateObj->format('h:i A');
?>

<div x-data="timer('<?php echo e($targetDate); ?>')" x-init="init()" class="w-full no-print">
    <!-- Professional Timer Card -->
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                
                <!-- Info Section -->
                <div class="text-center sm:text-right flex-1">
                    <div class="flex items-center justify-center sm:justify-start gap-2 mb-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-400"></span>
                        </span>
                        <h2 class="text-lg sm:text-xl font-bold text-white">
                            <i class="fa-solid fa-clock ml-2"></i>
                            الموعد المتوقع لظهور النتيجة
                        </h2>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                        <span class="font-black text-yellow-300 text-xl sm:text-2xl" dir="rtl">
                            <?php echo e($formattedDate); ?>

                        </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($note): ?>
                        <span class="text-blue-200 text-sm">
                            <?php echo e($note); ?>

                        </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <!-- Timer Digits -->
                <div class="flex-shrink-0" x-show="!expired">
                    <div class="flex items-center gap-2 sm:gap-3 text-center" dir="ltr">
                        <!-- Days -->
                        <div class="flex flex-col">
                            <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg w-14 h-12 sm:w-16 sm:h-14 flex items-center justify-center text-xl sm:text-2xl font-black text-white shadow-lg" x-text="days">00</div>
                            <span class="text-xs text-blue-200 mt-1 font-medium">يوم</span>
                        </div>
                        <span class="text-white/50 font-bold text-xl -mt-5">:</span>
                        
                        <!-- Hours -->
                        <div class="flex flex-col">
                            <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg w-14 h-12 sm:w-16 sm:h-14 flex items-center justify-center text-xl sm:text-2xl font-black text-white shadow-lg" x-text="hours">00</div>
                            <span class="text-xs text-blue-200 mt-1 font-medium">ساعة</span>
                        </div>
                        <span class="text-white/50 font-bold text-xl -mt-5">:</span>
                        
                        <!-- Minutes -->
                        <div class="flex flex-col">
                            <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-lg w-14 h-12 sm:w-16 sm:h-14 flex items-center justify-center text-xl sm:text-2xl font-black text-white shadow-lg" x-text="minutes">00</div>
                            <span class="text-xs text-blue-200 mt-1 font-medium">دقيقة</span>
                        </div>
                        <span class="text-white/50 font-bold text-xl -mt-5 hidden sm:inline">:</span>
                        
                        <!-- Seconds -->
                        <div class="flex-col hidden sm:flex">
                            <div class="bg-yellow-400/90 border border-yellow-300 rounded-lg w-16 h-14 flex items-center justify-center text-2xl font-black text-blue-900 shadow-lg" x-text="seconds">00</div>
                            <span class="text-xs text-blue-200 mt-1 font-medium">ثانية</span>
                        </div>
                    </div>
                </div>

                <!-- Expired State -->
                <div x-show="expired" x-cloak class="flex items-center gap-3 bg-green-500 px-6 py-3 rounded-lg shadow-lg">
                    <i class="fa-solid fa-check-circle text-white text-xl"></i>
                    <span class="font-bold text-white text-lg">النتيجة ظهرت الآن!</span>
                </div>

            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="h-1.5 w-full bg-blue-900/50">
            <div class="h-full bg-gradient-to-r from-yellow-400 to-yellow-300 rounded-r-full animate-pulse" style="width: 85%"></div>
        </div>
    </div>
    
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('timer', (targetDate) => ({
                target: new Date(targetDate).getTime(),
                now: new Date().getTime(),
                days: '00', hours: '00', minutes: '00', seconds: '00',
                expired: false, interval: null,
                init() {
                    this.update();
                    this.interval = setInterval(() => this.update(), 1000);
                },
                update() {
                    this.now = new Date().getTime();
                    const distance = this.target - this.now;
                    if (distance < 0) {
                        this.expired = true; clearInterval(this.interval);
                    } else {
                        const d = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const s = Math.floor((distance % (1000 * 60)) / 1000);
                        this.days = d < 10 ? "0" + d : d;
                        this.hours = h < 10 ? "0" + h : h;
                        this.minutes = m < 10 ? "0" + m : m;
                        this.seconds = s < 10 ? "0" + s : s;
                    }
                }
            }));
        });
    </script>
</div>
<?php /**PATH /Users/Masry/GitHub/ntegty/resources/views/components/result-timer.blade.php ENDPATH**/ ?>