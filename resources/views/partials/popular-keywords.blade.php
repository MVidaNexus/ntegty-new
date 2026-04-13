{{-- كلمات البحث الشائعة --}}
<div class="mt-10 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-magnifying-glass text-blue-500"></i>
        كلمات بحث شائعة
    </h2>
    <div class="flex flex-wrap gap-2">
        @php
            $keywords = [
                'نتيجة الثانوية العامة' => route('egypt.secondary'),
                'نتيجة الشهادة الإعدادية' => route('egypt.preparatory'),
                'نتيجة الدبلومات الفنية' => route('egypt.diplomas.index'),
                'نتيجة الأزهر الشريف' => route('egypt.azhar.secondary'),
                'أوائل الثانوية العامة' => route('egypt.secondary.all-results') . '?sort=total_desc',
                'نتيجة الثانوية بالاسم' => route('egypt.secondary'),
                'نتيجة الإعدادية برقم الجلوس' => route('egypt.preparatory'),
                'نتائج مصر' => route('egypt.index'),
                'نتيجة الصف الثالث الإعدادي' => route('egypt.preparatory'),
                'نتيجة 3 إعدادي' => route('egypt.preparatory'),
                'نتيجة تالتة إعدادي' => route('egypt.preparatory'),
                'نتيجة الثانوية العامة برقم الجلوس' => route('egypt.secondary'),
                'بوابة نتائج التعليم الأساسي' => route('egypt.preparatory'),
                'نتيجة الدبلوم التجاري' => route('egypt.diplomas', 'commercial'),
                'نتيجة الدبلوم الصناعي' => route('egypt.diplomas', 'industrial'),
            ];
        @endphp
        
        @foreach($keywords as $keyword => $url)
        <a href="{{ $url }}" 
           class="inline-flex items-center gap-1 px-3 py-1.5 bg-white text-gray-700 rounded-full text-sm border border-gray-200 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 transition-all shadow-sm">
            <i class="fa-solid fa-search text-gray-400 text-xs"></i>
            {{ $keyword }}
        </a>
        @endforeach
    </div>
</div>
