@extends('layouts.layout')

@section('structured_data')
{!! $structuredData !!}
@endsection

@section('content')
<div class="bg-slate-50 min-h-screen py-12 md:py-16 font-tajawal">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center flex-wrap gap-2 text-sm text-slate-400 font-bold mb-8" aria-label="Breadcrumb">
            @foreach($breadcrumbs as $index => $crumb)
                @if($crumb['url'])
                    <a href="{{ $crumb['url'] }}" class="hover:text-blue-600 transition-colors">{{ $crumb['name'] }}</a>
                @else
                    <span class="text-slate-600 truncate">{{ $crumb['name'] }}</span>
                @endif
                @if(!$loop->last)
                    <i class="fa-solid fa-chevron-left text-[10px] text-slate-300"></i>
                @endif
            @endforeach
        </nav>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12">
            
            <!-- Article Body (Col 8) -->
            <article class="lg:col-span-8 bg-white rounded-3xl p-6 md:p-10 border border-slate-100 shadow-lg shadow-slate-200/40">
                <!-- Header Info -->
                <div class="space-y-4 mb-8">
                    <span class="inline-block px-3.5 py-1.5 rounded-xl text-xs font-black text-white {{ match($post->category) {
                        'results' => 'bg-blue-600',
                        'alternatives' => 'bg-emerald-600',
                        'capabilities' => 'bg-amber-600',
                        'grades' => 'bg-purple-600',
                        default => 'bg-slate-600'
                    } }}">
                        {{ $post->category_name_ar }}
                    </span>

                    <h1 class="text-2xl md:text-4xl font-black text-slate-800 leading-tight">
                        {{ $post->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 font-semibold pt-2 border-t border-slate-50">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar-check text-blue-500"></i>
                            <span>تاريخ النشر: {{ $post->published_at ? $post->published_at->format('Y-m-d') : $post->created_at->format('Y-m-d') }}</span>
                        </span>
                        <span class="hidden md:inline text-slate-200">|</span>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-eye text-blue-500"></i>
                            <span>قراءة 5 دقائق</span>
                        </span>
                    </div>
                </div>

                <!-- Featured Image -->
                @if($post->image_path)
                    <div class="rounded-2xl overflow-hidden mb-8 shadow-sm border border-slate-50 aspect-[21/9] bg-slate-100">
                        <img src="{{ asset($post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- Content Body -->
                <div class="prose max-w-none text-slate-700 font-medium leading-loose text-base md:text-lg">
                    {!! $post->content !!}
                </div>

                <!-- Share Widget -->
                <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <span class="text-sm font-black text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-share-nodes text-blue-500"></i> مشاركة هذا الخبر:
                    </span>
                    <div class="flex items-center gap-3">
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" 
                           class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-[#1877F2]/10 hover:text-[#1877F2] text-slate-400 flex items-center justify-center text-lg transition-all" title="مشاركة على فيسبوك">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <!-- Twitter -->
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" 
                           class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-slate-900/10 hover:text-slate-900 text-slate-400 flex items-center justify-center text-lg transition-all" title="مشاركة على تويتر">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <!-- WhatsApp -->
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" 
                           class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-green-500/10 hover:text-green-600 text-slate-400 flex items-center justify-center text-lg transition-all" title="مشاركة على واتساب">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <!-- Copy Link -->
                        <button onclick="navigator.clipboard.writeText('{{ url()->current() }}'); alert('تم نسخ رابط المقال بنجاح!');" 
                                class="w-10 h-10 rounded-xl bg-slate-50 hover:bg-blue-600/10 hover:text-blue-600 text-slate-400 flex items-center justify-center text-lg transition-all" title="نسخ الرابط">
                            <i class="fa-solid fa-link"></i>
                        </button>
                    </div>
                </div>

            </article>

            <!-- Sidebar (Col 4) -->
            <aside class="lg:col-span-4 space-y-8">
                <!-- Telegram Box -->
                <div class="bg-gradient-to-br from-blue-500 via-indigo-500 to-indigo-600 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-xl border border-indigo-400/20">
                    <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/10 rounded-full opacity-40"></div>
                    <div class="relative z-10 space-y-4 text-center sm:text-right">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/20 text-white text-2xl mb-2">
                            <i class="fa-brands fa-telegram"></i>
                        </span>
                        <h3 class="text-xl font-black">قناتنا على تليجرام</h3>
                        <p class="text-xs text-white/90 leading-relaxed font-semibold">
                            اشترك الآن في قناتنا لتصلك نتائج الامتحانات والأخبار التعليمية العاجلة فور صدورها مباشرة على هاتفك.
                        </p>
                        <a href="https://t.me/YOUR_TELEGRAM_CHANNEL" target="_blank" class="block w-full text-center bg-white text-indigo-700 font-extrabold text-sm py-3 rounded-2xl hover:bg-blue-50 transition-all shadow-md">
                            انضم الآن مجاناً
                        </a>
                    </div>
                </div>

                <!-- Recent News Widget -->
                @if($recentPosts->count() > 0)
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-lg shadow-slate-200/40">
                        <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2.5 pb-3 border-b border-slate-50">
                            <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                            أحدث الأخبار التعليمية
                        </h3>
                        <div class="space-y-6">
                            @foreach($recentPosts as $recent)
                                <a href="{{ route('blog.show', $recent) }}" class="flex gap-4 group">
                                    @if($recent->image_path)
                                        <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0 border border-slate-50 bg-slate-100 aspect-square">
                                            <img src="{{ asset($recent->image_path) }}" alt="{{ $recent->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        </div>
                                    @else
                                        <div class="w-20 h-20 rounded-xl bg-blue-50 text-blue-500 shrink-0 flex items-center justify-center text-xl aspect-square">
                                            <i class="fa-solid fa-graduation-cap"></i>
                                        </div>
                                    @endif
                                    <div class="space-y-1">
                                        <h4 class="text-sm font-bold text-slate-700 leading-snug group-hover:text-blue-600 transition-colors duration-200 line-clamp-2">
                                            {{ $recent->title }}
                                        </h4>
                                        <span class="text-[10px] text-slate-400 font-semibold block">
                                            {{ $recent->published_at ? $recent->published_at->format('Y-m-d') : $recent->created_at->format('Y-m-d') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>

        </div>
    </div>
</div>

<style>
/* Rich Content Prose Styles - TipTap rendering support */
.prose h1, .prose h2, .prose h3, .prose h4 { font-family: 'Tajawal', sans-serif; font-weight: 900; color: #1e293b; margin-top: 1.75em; margin-bottom: 0.5em; }
.prose h2 { font-size: 1.6rem; border-right: 4px solid #3b82f6; padding-right: 0.75rem; }
.prose h3 { font-size: 1.3rem; color: #1e3a8a; }
.prose p { margin-bottom: 1.25em; line-height: 1.9; }
.prose ul, .prose ol { margin: 1em 0; padding-right: 1.5em; }
.prose ul { list-style-type: disc; }
.prose ol { list-style-type: decimal; }
.prose li { margin-bottom: 0.5em; line-height: 1.8; }
.prose strong { color: #0f172a; font-weight: 800; }
.prose table { width: 100%; border-collapse: collapse; margin: 1.5em 0; font-size: 0.95em; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
.prose th { background-color: #f1f5f9; padding: 12px; font-weight: 800; text-align: right; border-bottom: 2px solid #cbd5e1; }
.prose td { padding: 12px; border-bottom: 1px solid #e2e8f0; }
.prose tr:hover { background-color: #f8fafc; }
</style>
@endsection
