<div class="max-w-3xl mx-auto mb-10 no-print" x-data="preRegistrationForm()">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden relative transition-colors duration-300">
        
        <!-- Subtle Top Border -->
        <div class="h-1.5 w-full bg-gradient-to-r from-emerald-400 to-emerald-600"></div>

        <div class="p-6 md:p-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row items-center gap-4 mb-8">
                <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center flex-shrink-0 text-emerald-600 dark:text-emerald-400">
                    <i class="fa-solid fa-bell-concierge text-2xl"></i>
                </div>
                <div class="text-center md:text-right">
                    <h3 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white mb-1">
                        سجل بياناتك لتصلك النتيجة فور ظهورها
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                        انضم لآلاف الطلاب الذين ستصلهم نتيجتهم عبر رسالة مجانية على الواتساب بمجرد اعتمادها رسمياً.
                    </p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- Name Input -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">الاسم رباعي <span class="text-emerald-500">*</span></label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                <i class="fa-regular fa-user"></i>
                            </span>
                            <input type="text" x-model="formData.name" required placeholder="مثال: أحمد محمد علي" class="w-full pr-12 pl-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm dark:text-slate-200 shadow-sm">
                        </div>
                        <template x-if="errors.name">
                            <span class="text-xs text-red-500 mt-1.5 block" x-text="errors.name[0]"></span>
                        </template>
                    </div>

                    <!-- Phone Input -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">رقم الواتساب <span class="text-emerald-500">*</span></label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </span>
                            <input type="tel" x-model="formData.phone" required placeholder="01012345678" class="w-full pr-12 pl-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm text-left dark:text-slate-200 shadow-sm" dir="ltr">
                        </div>
                        <template x-if="errors.phone">
                            <span class="text-xs text-red-500 mt-1.5 block" x-text="errors.phone[0]"></span>
                        </template>
                    </div>

                    <!-- Seat Number -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">رقم الجلوس <span class="text-slate-400 text-xs font-normal">(اختياري)</span></label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                <i class="fa-solid fa-hashtag"></i>
                            </span>
                            <input type="text" x-model="formData.seat_number" placeholder="12345" class="w-full pr-12 pl-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm font-bold text-center dark:text-slate-200 shadow-sm">
                        </div>
                    </div>
                    
                    <!-- Governorate -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            المحافظة 
                            @if(isset($examTypeSlug) && str_contains($examTypeSlug, 'preparatory'))
                                <span class="text-emerald-500">*</span>
                            @else
                                <span class="text-slate-400 text-xs font-normal">(اختياري)</span>
                            @endif
                        </label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                <i class="fa-solid fa-map-pin"></i>
                            </span>
                            <input type="text" x-model="formData.governorate_slug" 
                                @if(isset($examTypeSlug) && str_contains($examTypeSlug, 'preparatory')) required @endif 
                                placeholder="القاهرة، الإسكندرية..." 
                                class="w-full pr-12 pl-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm dark:text-slate-200 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 dark:border-slate-700/50 pt-5">
                    <p class="text-xs text-slate-400 flex items-center gap-1.5 order-2 sm:order-1">
                        <i class="fa-solid fa-lock text-emerald-500"></i>
                        بياناتك مشفرة ومؤمنة تماماً.
                    </p>
                    <button type="submit" :disabled="isLoading" class="w-full sm:w-auto order-1 sm:order-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 flex justify-center items-center gap-2 disabled:opacity-70">
                        <span x-show="!isLoading">سجل الآن للحصول على النتيجة</span>
                        <span x-show="!isLoading"><i class="fa-solid fa-paper-plane"></i></span>
                        <span x-show="isLoading" class="animate-spin"><i class="fa-solid fa-circle-notch"></i></span>
                        <span x-show="isLoading">جاري التسجيل...</span>
                    </button>
                </div>

                <!-- Success Message -->
                <div x-show="successMessage" x-transition.opacity style="display: none;" class="mt-4 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-700 dark:text-emerald-400 text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span x-text="successMessage"></span>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function preRegistrationForm() {
        return {
            formData: {
                name: '',
                phone: '',
                seat_number: '',
                governorate_slug: '',
                exam_type_slug: '{{ $examTypeSlug ?? "unknown" }}',
                _token: '{{ csrf_token() }}'
            },
            errors: {},
            isLoading: false,
            successMessage: '',
            
            submitForm() {
                this.isLoading = true;
                this.errors = {};
                this.successMessage = '';
                
                fetch('{{ route("pre-register.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                })
                .then(response => response.json())
                .then(data => {
                    this.isLoading = false;
                    if(data.success) {
                        this.successMessage = data.message;
                        // Reset form fields but keep exam_type_slug
                        this.formData.name = '';
                        this.formData.phone = '';
                        this.formData.seat_number = '';
                        this.formData.governorate_slug = '';
                    } else if (data.errors) {
                        this.errors = data.errors;
                    }
                })
                .catch(error => {
                    this.isLoading = false;
                    alert('حدث خطأ أثناء الاتصال بالخادم. يرجى المحاولة لاحقاً.');
                });
            }
        }
    }
</script>
