@extends('layouts.layout')

@section('structured_data')
{!! $structuredData !!}
@endsection

@section('content')
<div class="min-h-screen bg-slate-50 py-12 md:py-16 font-tajawal">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Hero Header -->
        <div class="text-center mb-12 space-y-4">
            <span class="inline-block px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-sm font-black border border-blue-100 shadow-sm">
                <i class="fa-solid fa-book-open text-emerald-600"></i> مدونة نتيجتي التعليمية
            </span>
            <h1 class="text-3xl md:text-5xl font-black text-slate-800 tracking-tight leading-relaxed">
                آخر الأخبار و <span class="text-blue-600">المستجدات التعليمية</span>
            </h1>
            <p class="text-base md:text-lg text-slate-500 max-w-2xl mx-auto leading-relaxed font-medium">
                تغطية شاملة وحصرية لنتائج الامتحانات، شروط القبول ببدائل الإعدادية، اختبارات القدرات وتوزيع درجات المواد المعتمد.
            </p>
        </div>

        <!-- Categories Filter Bar -->
        <div class="flex flex-wrap justify-center gap-3 mb-12">
            <a href="{{ route('blog.index') }}" 
               class="px-5 py-2.5 rounded-2xl text-sm font-bold border transition-all duration-300 {{ is_null($category) ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600' }}">
                الكل
            </a>
            <a href="{{ route('blog.index', ['category' => 'results']) }}" 
               class="px-5 py-2.5 rounded-2xl text-sm font-bold border transition-all duration-300 {{ $category === 'results' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600' }}">
                أخبار النتائج
            </a>
            <a href="{{ route('blog.index', ['category' => 'alternatives']) }}" 
               class="px-5 py-2.5 rounded-2xl text-sm font-bold border transition-all duration-300 {{ $category === 'alternatives' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600' }}">
                بدائل الإعدادية
            </a>
            <a href="{{ route('blog.index', ['category' => 'capabilities']) }}" 
               class="px-5 py-2.5 rounded-2xl text-sm font-bold border transition-all duration-300 {{ $category === 'capabilities' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600' }}">
                اختبارات القدرات
            </a>
            <a href="{{ route('blog.index', ['category' => 'grades']) }}" 
               class="px-5 py-2.5 rounded-2xl text-sm font-bold border transition-all duration-300 {{ $category === 'grades' ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-500/20 scale-105' : 'bg-white text-slate-600 border-slate-200 hover:border-blue-300 hover:text-blue-600' }}">
                توزيع الدرجات
            </a>
        </div>

        @if($posts->count() > 0)
            <!-- Posts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($posts as $post)
                    <article class="group bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-lg shadow-slate-200/40 hover:shadow-xl hover:shadow-slate-300/40 hover:-translate-y-1.5 transition-all duration-300 flex flex-col h-full">
                        <!-- Featured Image Wrapper -->
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                            @if($post->image_path)
                                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-indigo-600 text-white">
                                    <i class="fa-solid fa-graduation-cap text-5xl opacity-30"></i>
                                </div>
                            @endif
                            <!-- Category Badge -->
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1.5 rounded-xl text-xs font-black text-white shadow-md shadow-black/10 {{ match($post->category) {
                                    'results' => 'bg-blue-600',
                                    'alternatives' => 'bg-emerald-600',
                                    'capabilities' => 'bg-amber-600',
                                    'grades' => 'bg-purple-600',
                                    default => 'bg-slate-600'
                                } }}">
                                    {{ $post->category_name_ar }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6 md:p-8 flex-1 flex flex-col justify-between">
                            <div class="space-y-3">
                                <!-- Meta (Date) -->
                                <div class="flex items-center gap-2 text-xs text-slate-400 font-semibold">
                                    <i class="fa-regular fa-user text-sm text-blue-500"></i>
                                    <span>{{ $post->user ? $post->user->name : 'إدارة نتيجتي' }}</span>
                                    <span class="mx-1">•</span>
                                    <i class="fa-regular fa-calendar-check text-sm text-blue-500"></i>
                                    <span>{{ $post->published_at ? $post->published_at->format('Y-m-d') : $post->created_at->format('Y-m-d') }}</span>
                                </div>

                                <!-- Title -->
                                <h2 class="text-xl font-extrabold text-slate-800 leading-snug group-hover:text-blue-600 transition-colors duration-200">
                                    <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
                                </h2>

                                <!-- Summary -->
                                <p class="text-sm text-slate-500 font-medium leading-relaxed line-clamp-3">
                                    {{ $post->summary }}
                                </p>
                            </div>

                            <!-- Action Button -->
                            <div class="pt-6 mt-6 border-t border-slate-50 flex items-center justify-between">
                                <a href="{{ route('blog.show', $post) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-black text-sm group/btn transition-colors">
                                    <span>اقرأ التفاصيل</span>
                                    <i class="fa-solid fa-arrow-left text-xs group-hover/btn:-translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-16 flex justify-center">
                {{ $posts->links() }}
            </div>
        @else
            <!-- No Posts Found -->
            <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center max-w-xl mx-auto shadow-sm">
                <span class="inline-block p-4 rounded-full bg-slate-50 text-slate-400 mb-4 text-3xl">
                    <i class="fa-regular fa-folder-open"></i>
                </span>
                <h3 class="text-xl font-bold text-slate-800 mb-2">لا توجد أخبار حالياً</h3>
                <p class="text-slate-500 text-sm leading-relaxed">
                    لم يتم نشر أي مقالات أو أخبار في هذا التصنيف بعد. يرجى مراجعة الموقع لاحقاً.
                </p>
                <a href="{{ route('blog.index') }}" class="mt-6 inline-block bg-blue-600 text-white font-bold text-sm px-6 py-2.5 rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-500/10">
                    العودة لجميع المقالات
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
