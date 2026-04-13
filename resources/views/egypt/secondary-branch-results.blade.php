@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    @if(isset($breadcrumbs))
    <nav class="mb-6 text-sm">
        <ol class="flex items-center gap-2 text-gray-600">
            @foreach($breadcrumbs as $index => $crumb)
                @if($index > 0)
                    <li><i class="fa-solid fa-chevron-left text-xs mx-2"></i></li>
                @endif
                <li>
                    @if(isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:text-blue-600">{{ $crumb['name'] }}</a>
                    @else
                        <span class="text-gray-800 font-semibold">{{ $crumb['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
    @endif

    <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl md:text-3xl font-black text-gray-800">
                {{ $title }}
            </h1>
            
            <!-- Branch Info -->
            <div class="flex items-center gap-3 px-4 py-2 bg-indigo-100 rounded-xl">
                <i class="fa-solid fa-graduation-cap text-indigo-600 text-xl"></i>
                <div>
                    <span class="text-sm text-indigo-600 font-medium">شعبة</span>
                    <p class="font-black text-indigo-800">{{ $branch->name_ar }}</p>
                </div>
                @if($branch->total_score)
                <div class="border-r border-indigo-200 pr-3 mr-3">
                    <span class="text-sm text-indigo-600 font-medium">المجموع الكلي</span>
                    <p class="font-black text-indigo-800">{{ $branch->total_score }} درجة</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Search -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <form method="GET" action="{{ route('egypt.secondary.branch.all-results', $branch->code) }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $search }}" placeholder="الاسم أو رقم الجلوس"
                           class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors">
                    <i class="fa-solid fa-search ml-2"></i>
                    بحث
                </button>
            </form>
        </div>

        <!-- Results Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                        <th class="px-4 py-3 text-right rounded-tr-xl">الترتيب</th>
                        <th class="px-4 py-3 text-right">رقم الجلوس</th>
                        <th class="px-4 py-3 text-right">الاسم</th>
                        <th class="px-4 py-3 text-right">المحافظة</th>
                        <th class="px-4 py-3 text-center">المجموع</th>
                        <th class="px-4 py-3 text-center rounded-tl-xl">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $index => $result)
                        <tr class="border-b border-gray-100 hover:bg-indigo-50 transition-colors">
                            <td class="px-4 py-3">
                                @php $rank = ($results->currentPage() - 1) * $results->perPage() + $index + 1; @endphp
                                @if($rank <= 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                        {{ $rank == 1 ? 'bg-yellow-400 text-yellow-900' : ($rank == 2 ? 'bg-gray-300 text-gray-700' : 'bg-amber-600 text-white') }}
                                        font-black text-sm">
                                        {{ $rank }}
                                    </span>
                                @else
                                    <span class="font-bold text-gray-500">{{ $rank }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-bold text-blue-600">
                                {{ $result->seat_number }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('egypt.secondary.student', $result->seat_number) }}" 
                                   class="font-bold text-gray-800 hover:text-indigo-600 hover:underline transition-colors">
                                    {{ $result->student_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $result->governorate?->name_ar ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center min-w-[60px] px-3 py-1 bg-green-100 text-green-700 rounded-lg font-black">
                                    {{ $result->total_score }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold
                                    {{ $result->status === 'ناجح' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $result->status ?? 'غير محدد' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fa-solid fa-inbox text-4xl mb-3 block"></i>
                                لا توجد نتائج
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $results->withQueryString()->links() }}
        </div>
        
        <!-- تنويه -->
        <div class="mt-6 p-4 bg-amber-50 border-2 border-amber-300 rounded-xl text-center">
            <p class="text-amber-800 font-bold">
                <i class="fa-solid fa-triangle-exclamation ml-2"></i>
                تنبيه: هذا الكشف غير رسمي - قم بمراجعة مدرستك للتأكد من النتيجة
            </p>
        </div>
        
        {{-- Content Section for SEO --}}
        @if(isset($examType) && $examType->show_content_section && ($examType->content_title || $examType->content_body))
        <div class="w-full max-w-6xl mx-auto mt-12">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 border border-gray-100">
                @if($examType->content_title)
                <h2 class="text-xl md:text-2xl lg:text-3xl font-black text-gray-800 mb-5 pb-3 border-b-2 border-gray-100">{{ $examType->content_title }}</h2>
                @endif
                @if($examType->content_intro)
                <p class="text-gray-600 mb-6 text-base md:text-lg leading-relaxed">{{ $examType->content_intro }}</p>
                @endif
                @if($examType->content_body)
                <div class="prose prose-base md:prose-lg max-w-none text-gray-700 leading-loose
                            prose-headings:font-bold prose-headings:text-gray-800 prose-headings:mt-6 prose-headings:mb-3
                            prose-h2:text-xl prose-h2:md:text-2xl prose-h2:border-r-4 prose-h2:border-emerald-500 prose-h2:pr-4 prose-h2:py-1
                            prose-h3:text-lg prose-h3:md:text-xl prose-h3:text-emerald-700
                            prose-p:mb-4 prose-p:text-base prose-p:md:text-lg
                            prose-ul:my-4 prose-ul:pr-6 prose-li:mb-2 prose-li:text-base prose-li:md:text-lg
                            prose-a:text-emerald-600 prose-a:hover:text-emerald-700">
                    {!! $examType->getFormattedContentBody() !!}
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
