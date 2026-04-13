<?php
    $meta = [
        'title' => 'تواصل معنا | نتيجتي',
        'description' => 'تواصل مع فريق نتيجتي لأي استفسارات أو اقتراحات - نحن هنا لمساعدتك',
        'og_title' => 'تواصل معنا - نتيجتي',
        'og_description' => 'تواصل مع فريق نتيجتي لأي استفسارات أو اقتراحات',
    ];
    $structuredData = \App\Services\SchemaService::contactPage();
?>

<?php $__env->startSection('structured_data'); ?>
<?php echo $structuredData; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-slate-50 py-12 md:py-20 font-tajawal">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Header Section -->
        <div class="text-center mb-16 space-y-4">
            <span class="inline-block p-3 rounded-2xl bg-blue-100 text-blue-600 mb-2">
                <i class="fa-solid fa-headset text-2xl"></i>
            </span>
            <h1 class="text-4xl md:text-5xl font-black text-slate-800 tracking-tight leading-relaxed">
                تواصل معنا <span class="text-blue-600">بسهولة</span>
            </h1>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto leading-relaxed">
                نحن هنا لمساعدتك. إذا كان لديك أي استفسار أو اقتراح، لا تتردد في التواصل معنا عبر القنوات التالية.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12 mb-20">
            <!-- Contact Info Sidebar (Clean & Organized) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Contact Channels Card -->
                <div class="bg-white rounded-[2rem] p-8 shadow-lg shadow-slate-200/50 border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-[4rem] -mr-4 -mt-4 opacity-50"></div>
                    
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3 relative z-10">
                        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600">
                            <i class="fa-solid fa-address-book"></i>
                        </span>
                        قنوات التواصل
                    </h3>
                    
                    <div class="space-y-4 relative z-10">
                        <!-- Email Item -->
                        <a href="mailto:support@ntegty.com" class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 hover:bg-blue-50 border border-slate-100 hover:border-blue-200 transition-all group">
                            <div class="w-12 h-12 bg-white text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-slate-400 font-bold mb-1">البريد الإلكتروني</div>
                                <div class="text-slate-700 font-bold group-hover:text-blue-700 transition-colors ltr:font-sans" dir="ltr">support@ntegty.com</div>
                            </div>
                            <div class="text-slate-300 group-hover:text-blue-500 group-hover:-translate-x-1 transition-all">
                                <i class="fa-solid fa-arrow-left"></i>
                            </div>
                        </a>

                        <!-- WhatsApp Item -->
                        <a href="https://wa.me/YOUR_NUMBER" target="_blank" class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 hover:bg-green-50 border border-slate-100 hover:border-green-200 transition-all group">
                            <div class="w-12 h-12 bg-white text-green-600 rounded-xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-slate-400 font-bold mb-1">خدمة العملاء</div>
                                <div class="text-slate-700 font-bold group-hover:text-green-700 transition-colors">محادثة عبر واتساب</div>
                            </div>
                            <div class="text-slate-300 group-hover:text-green-500 group-hover:-translate-x-1 transition-all">
                                <i class="fa-solid fa-arrow-left"></i>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Social Media Card -->
                <div class="bg-white rounded-[2rem] p-8 shadow-lg shadow-slate-200/50 border border-slate-100">
                     <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                        <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-purple-100 text-purple-600">
                            <i class="fa-solid fa-hashtag"></i>
                        </span>
                        تابعنا على المنصات
                    </h3>
                    <div class="flex flex-wrap gap-4">
                        <a href="#" class="flex-1 min-w-[100px] flex flex-col items-center justify-center gap-3 p-5 rounded-2xl bg-slate-50 hover:bg-[#1877F2]/5 hover:text-[#1877F2] transition-all group border border-slate-100 hover:border-[#1877F2]/20">
                            <i class="fa-brands fa-facebook-f text-2xl group-hover:scale-110 transition-transform text-slate-400 group-hover:text-[#1877F2]"></i>
                            <span class="text-xs font-bold text-slate-500 group-hover:text-[#1877F2]">فيسبوك</span>
                        </a>
                        <a href="#" class="flex-1 min-w-[100px] flex flex-col items-center justify-center gap-3 p-5 rounded-2xl bg-slate-50 hover:bg-[#229ED9]/5 hover:text-[#229ED9] transition-all group border border-slate-100 hover:border-[#229ED9]/20">
                            <i class="fa-brands fa-telegram text-2xl group-hover:scale-110 transition-transform text-slate-400 group-hover:text-[#229ED9]"></i>
                            <span class="text-xs font-bold text-slate-500 group-hover:text-[#229ED9]">تيليجرام</span>
                        </a>
                        <a href="#" class="flex-1 min-w-[100px] flex flex-col items-center justify-center gap-3 p-5 rounded-2xl bg-slate-50 hover:bg-[#E4405F]/5 hover:text-[#E4405F] transition-all group border border-slate-100 hover:border-[#E4405F]/20">
                            <i class="fa-brands fa-instagram text-2xl group-hover:scale-110 transition-transform text-slate-400 group-hover:text-[#E4405F]"></i>
                            <span class="text-xs font-bold text-slate-500 group-hover:text-[#E4405F]">انستجرام</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form (3 cols) -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/50 p-8 md:p-10 border border-slate-100 h-full relative overflow-hidden">
                    <!-- Decorative Background Element -->
                    <div class="absolute top-0 left-0 w-32 h-32 bg-blue-50 rounded-full -translate-x-1/2 -translate-y-1/2 opacity-50 blur-2xl"></div>
                    
                    <h2 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-3 relative z-10">
                        <span class="w-2 h-8 bg-blue-600 rounded-full inline-block"></span>
                        أرسل رسالة مباشرة
                    </h2>

                    <form action="#" method="POST" class="space-y-6 relative z-10">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700">الاسم الكامل</label>
                                <div class="relative">
                                    <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 pl-10 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" placeholder="الاسم هنا">
                                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700">البريد الإلكتروني</label>
                                <div class="relative">
                                    <input type="email" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 pl-10 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" placeholder="example@email.com">
                                    <i class="fa-solid fa-at absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">الموضوع</label>
                            <div class="relative">
                                <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 pl-10 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none" placeholder="موضوع رسالتك">
                                <i class="fa-solid fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">الرسالة</label>
                            <textarea rows="5" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none resize-none" placeholder="اكتب تفاصيل رسالتك هنا..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-600/20 hover:shadow-blue-600/40 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
                            <span>إرسال الرسالة</span>
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-black text-slate-800 mb-3">الأسئلة الشائعة</h2>
                <p class="text-slate-500">إجابات سريعة على معظم استفسارات الطلاب</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6" x-data="{ active: null }">
                <!-- FAQ Item 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:border-blue-200 transition-colors cursor-pointer group" @click="active = (active === 1 ? null : 1)">
                    <div class="flex justify-between items-center mb-0">
                        <h3 class="font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-black group-hover:bg-blue-600 group-hover:text-white transition-colors">?</span>
                            كيف أبحث عن نتيجتي؟
                        </h3>
                        <i class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-300" :class="active === 1 ? 'rotate-180 text-blue-600' : ''"></i>
                    </div>
                    <div class="text-slate-600 leading-relaxed mt-3 overflow-hidden transition-all duration-300" x-show="active === 1" x-collapse>
                        بسيطة جداً! اختر دولتك من الصفحة الرئيسية، ثم اختر المرحلة الدراسية، وأدخل رقم الجلوس أو الاسم في خانة البحث ستظهر النتيجة فوراً.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:border-blue-200 transition-colors cursor-pointer group" @click="active = (active === 2 ? null : 2)">
                    <div class="flex justify-between items-center mb-0">
                        <h3 class="font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-sm font-black group-hover:bg-green-600 group-hover:text-white transition-colors">$</span>
                            هل جميع الخدمات مجانية؟
                        </h3>
                        <i class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-300" :class="active === 2 ? 'rotate-180 text-green-600' : ''"></i>
                    </div>
                    <div class="text-slate-600 leading-relaxed mt-3 overflow-hidden transition-all duration-300" x-show="active === 2" x-collapse>
                        نعم، منصة نتيجتي مجانية 100% ولا تطلب أي رسوم مقابل البحث عن النتائج أو طباعة الشهادات.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:border-blue-200 transition-colors cursor-pointer group" @click="active = (active === 3 ? null : 3)">
                    <div class="flex justify-between items-center mb-0">
                        <h3 class="font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-black group-hover:bg-purple-600 group-hover:text-white transition-colors">#</span>
                            النتيجة لم تظهر بعد؟
                        </h3>
                        <i class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-300" :class="active === 3 ? 'rotate-180 text-purple-600' : ''"></i>
                    </div>
                    <div class="text-slate-600 leading-relaxed mt-3 overflow-hidden transition-all duration-300" x-show="active === 3" x-collapse>
                        نقوم بتحديث البيانات فور اعتمادها رسمياً. إذا لم تجد نتيجتك، يرجى المحاولة لاحقاً أو التأكد من صحة البيانات المدخلة.
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:border-blue-200 transition-colors cursor-pointer group" @click="active = (active === 4 ? null : 4)">
                    <div class="flex justify-between items-center mb-0">
                        <h3 class="font-bold text-slate-800 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-sm font-black group-hover:bg-orange-600 group-hover:text-white transition-colors">@</span>
                            مشكلة في الشهادة؟
                        </h3>
                        <i class="fa-solid fa-chevron-down text-slate-400 transition-transform duration-300" :class="active === 4 ? 'rotate-180 text-orange-600' : ''"></i>
                    </div>
                    <div class="text-slate-600 leading-relaxed mt-3 overflow-hidden transition-all duration-300" x-show="active === 4" x-collapse>
                        تأكد من إدخال البيانات بشكل صحيح في صفحة "شهادة تقدير". يمكنك التواصل معنا عبر الواتساب للمساعدة الفورية.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ntegty/public_html/resources/views/contact.blade.php ENDPATH**/ ?>