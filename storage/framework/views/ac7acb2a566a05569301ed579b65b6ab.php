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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($crumb['url']) && $crumb['url']): ?>
                        <a href="<?php echo e($crumb['url']); ?>" class="hover:text-amber-600"><?php echo e($crumb['name']); ?></a>
                    <?php else: ?>
                        <span class="text-gray-800 font-semibold"><?php echo e($crumb['name']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ol>
    </nav>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php
        $serviceType = $examType->result_service_type ?? 'search';
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($serviceType === 'embed'): ?>
        
        <?php echo $__env->make('partials.result-embed', ['examType' => $examType, 'title' => $title], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($serviceType === 'pdf'): ?>
        
        <?php echo $__env->make('partials.result-pdf', ['examType' => $examType, 'title' => $title], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        
        <!-- Search Section -->
        <div class="w-full max-w-6xl mx-auto px-3" x-data="searchComponent()">
            
            <!-- Result Timer - Above Search Box -->
            <div class="mb-4 no-print">
                <?php if (isset($component)) { $__componentOriginalffad86c0bcbb60d75763de100e32cdb8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalffad86c0bcbb60d75763de100e32cdb8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.result-timer','data' => ['country' => 'egypt','type' => $examType->code]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('result-timer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['country' => 'egypt','type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($examType->code)]); ?>
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
            
            <div class="bg-gradient-to-br from-white to-amber-50 rounded-2xl sm:rounded-3xl shadow-2xl p-5 sm:p-8 md:p-10 border border-amber-100 print:shadow-none print:border-0 print:p-0 print:bg-white">
                <!-- Azhar Icon -->
                <div class="text-center mb-4 no-print">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl shadow-lg">
                        <i class="fa-solid fa-mosque text-3xl text-white"></i>
                    </div>
                </div>
                
                <h1 class="text-lg sm:text-xl md:text-2xl font-black text-center text-gray-800 mb-2 leading-tight px-2 no-print">
                    نتيجة <?php echo e($examName); ?>

                </h1>
                
                <p class="text-center text-gray-500 text-sm mb-4 no-print">
                    <i class="fa-solid fa-calendar-alt ml-1"></i>
                    <?php echo e($egypt->academic_year ?? ''); ?>

                </p>

                <p class="text-center text-gray-600 mb-6 sm:mb-8 text-sm sm:text-base px-2 no-print">
                    ابحث برقم الجلوس أو الاسم
                </p>

                <!-- Search Form -->
                <form @submit.prevent="search" class="space-y-4 no-print">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($showYearFilter) && $showYearFilter && isset($academicYears)): ?>
                        <div class="w-full sm:w-1/3">
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                                السنة الدراسية
                            </label>
                            <select x-model="academicYearId" 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 focus:outline-none text-base sm:text-lg transition-all bg-white">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($year->id); ?>"><?php echo e($year->year); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="w-full <?php echo e((isset($showYearFilter) && $showYearFilter) ? 'sm:w-2/3' : ''); ?>">
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-2">
                                رقم الجلوس أو الاسم
                            </label>
                            <input type="text" 
                                   x-model="query" 
                                   required
                                   placeholder="أدخل رقم الجلوس أو الاسم"
                                   class="w-full px-4 py-3 sm:px-4 sm:py-3 border-2 border-gray-300 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-100 focus:outline-none text-base sm:text-lg transition-all">
                        </div>
                    </div>

                    <button type="submit" 
                            :disabled="loading"
                            class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold py-3 sm:py-4 px-6 rounded-xl text-base sm:text-lg shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <template x-if="loading">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </template>
                        <template x-if="!loading">
                            <i class="fa-solid fa-search"></i>
                        </template>
                        <span x-text="loading ? 'جاري البحث...' : 'بحث عن النتيجة'"></span>
                    </button>
                </form>
            </div>

            <!-- Popular Searches - Outside Search Box -->


                <!-- Error Message -->
                <div x-show="error" x-cloak class="mt-6 bg-red-50 border-2 border-red-200 rounded-xl p-4 text-center">
                    <i class="fa-solid fa-exclamation-circle text-red-500 text-2xl mb-2"></i>
                    <p class="text-red-700 font-bold" x-text="error"></p>
                </div>

                <!-- Results -->
                <div x-show="results.length > 0" x-cloak class="mt-8 space-y-4" id="results-section">
                    <template x-for="result in results" :key="result.id">
                        <div class="bg-white rounded-xl border-2 border-amber-200 p-6 hover:shadow-lg transition-all">
                            <!-- Student Info -->
                            <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800" x-text="result.student_name"></h3>
                                    <p class="text-gray-500 text-sm">
                                        رقم الجلوس: <span class="font-bold text-amber-600" x-text="result.seat_number"></span>
                                    </p>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-black" :class="result.status === 'ناجح' ? 'text-green-600' : 'text-red-600'" x-text="result.total_score"></div>
                                    <div class="text-xs text-gray-500">المجموع</div>
                                </div>
                            </div>

                            <!-- Subjects -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 mb-4">
                                <template x-for="(score, subject) in result.subjects_data" :key="subject">
                                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                                        <div class="text-xs text-gray-500 truncate" x-text="subject"></div>
                                        <div class="font-bold text-gray-800" x-text="score"></div>
                                    </div>
                                </template>
                            </div>

                            <!-- Status & Actions -->
                            <div class="flex items-center justify-between">
                                <span class="px-4 py-2 rounded-full font-bold text-sm"
                                      :class="result.status === 'ناجح' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                      x-text="result.status"></span>
                                
                                <div class="flex gap-2">
                                    <a :href="'/result/' + result.id + '/print'" 
                                       target="_blank"
                                       class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg font-bold text-sm transition-all">
                                        <i class="fa-solid fa-print ml-1"></i> طباعة
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Content Section -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($examType) && $examType->show_content_section && ($examType->content_title || $examType->content_body)): ?>
    <div class="w-full max-w-6xl mx-auto mt-12 px-3">
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-amber-100">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->content_title): ?>
            <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-amber-100 flex items-center gap-3">
                <i class="fa-solid fa-mosque text-amber-500"></i>
                <?php echo e($examType->content_title); ?>

            </h2>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            
            <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                        prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                        prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-amber-500 prose-h2:pr-4 prose-h2:py-1
                        prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-amber-700
                        prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                        prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                        prose-a:text-amber-600 prose-a:hover:text-amber-700">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->content_intro): ?>
                <div class="text-lg md:text-xl font-medium text-gray-600 mb-6 leading-relaxed">
                    <?php echo $examType->content_intro; ?>

                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($examType->content_body): ?>
                <?php echo $examType->getFormattedContentBody(); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function searchComponent() {
    return {
        query: '',
        loading: false,
        error: null,
        results: [],
        academicYearId: '<?php echo e($academicYears->first()?->id ?? ''); ?>',
        
        async search() {
            if (!this.query.trim()) {
                this.error = 'يرجى إدخال رقم الجلوس أو الاسم';
                return;
            }
            
            this.loading = true;
            this.error = null;
            this.results = [];
            
            try {
                const formData = new FormData();
                formData.append('query', this.query);
                formData.append('exam_type_id', '<?php echo e($examType->id); ?>');
                formData.append('academic_year_id', this.academicYearId);
                formData.append('_token', '<?php echo e(csrf_token()); ?>');
                
                const response = await fetch('/search', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success && data.results && data.results.length > 0) {
                    this.results = data.results;
                    
                    // Scroll to results
                    setTimeout(() => {
                        document.getElementById('results-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                } else {
                    this.error = data.message || 'لم يتم العثور على نتائج';
                }
            } catch (e) {
                this.error = 'حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.';
                console.error(e);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ntegty/public_html/resources/views/egypt/azhar.blade.php ENDPATH**/ ?>