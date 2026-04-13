{{-- Governorate Table Result View --}}
<div class="max-w-6xl mx-auto">
    @php
        // Get governorates for this country (default to Egypt if not specified)
        $countryId = $examType->country_id ?? 1;
        $allGovernorates = \App\Models\Governorate::where('country_id', $countryId)
            ->orderBy('name_ar')
            ->get();
        
        // Split into declared and pending
        $declaredGovernorates = $allGovernorates->where('is_declared', true)->values();
        $pendingGovernorates = $allGovernorates->where('is_declared', false)->values();
    @endphp

    <!-- Professional Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg mb-4">
            <i class="fa-solid fa-table-list text-white text-4xl"></i>
        </div>
        <h1 class="text-2xl md:text-3xl font-black text-gray-800 mb-2">
            {{ $title }}
        </h1>
        <p class="text-gray-600 text-base max-w-2xl mx-auto">
            اختر محافظتك من الجدول أدناه لتحميل ملف النتيجة
        </p>
    </div>

    @if($allGovernorates->count() > 0)
        <!-- Status Legend -->
        <div class="mb-6 flex flex-wrap items-center justify-center gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="text-gray-600">متاحة للتحميل</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                <span class="text-gray-600">معتمدة (جاري الرفع)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                <span class="text-gray-600">قريباً</span>
            </div>
        </div>

        <!-- Governorates Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-blue-700">
                            <th class="px-6 py-4 text-right text-white font-bold text-sm">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                    المحافظة
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-white font-bold text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-circle-info"></i>
                                    الحالة
                                </div>
                            </th>
                            <th class="px-6 py-4 text-center text-white font-bold text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-download"></i>
                                    تحميل النتيجة
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        {{-- Declared Governorates Section --}}
                        @if($declaredGovernorates->count() > 0)
                            <tr class="bg-gradient-to-r from-green-500 to-green-600">
                                <td colspan="3" class="px-6 py-3">
                                    <div class="flex items-center gap-2 text-white font-bold">
                                        <i class="fa-solid fa-circle-check"></i>
                                        <span>المحافظات المعتمدة ({{ $declaredGovernorates->count() }})</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach($declaredGovernorates as $index => $governorate)
                                <tr class="{{ $index % 2 == 0 ? 'bg-green-50/50' : 'bg-white' }} hover:bg-green-100/50 transition-colors">
                                    <!-- Governorate Name -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($governorate->logo_path)
                                                <img src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                                                     alt="{{ $governorate->name_ar }}"
                                                     class="w-10 h-10 object-contain rounded-lg border border-gray-200">
                                            @else
                                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fa-solid fa-city text-gray-400"></i>
                                                </div>
                                            @endif
                                            <span class="font-bold text-gray-800">{{ $governorate->name_ar }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        @if($governorate->hasResultPdf())
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                                                <i class="fa-solid fa-circle-check"></i>
                                                متاحة للتحميل
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-sm font-bold">
                                                <i class="fa-solid fa-clock"></i>
                                                معتمدة
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <!-- Download Button -->
                                    <td class="px-6 py-4 text-center">
                                        @if($governorate->hasResultPdf())
                                            <a href="{{ $governorate->getResultPdfUrl() }}" 
                                               target="_blank"
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                                <i class="fa-solid fa-file-pdf"></i>
                                                عرض النتيجة
                                            </a>
                                        @else
                                            <button disabled 
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-200 text-amber-600 font-bold rounded-lg cursor-not-allowed">
                                                <i class="fa-solid fa-spinner fa-spin"></i>
                                                جاري الرفع
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        {{-- Pending Governorates Section --}}
                        @if($pendingGovernorates->count() > 0)
                            <tr class="bg-gradient-to-r from-gray-400 to-gray-500">
                                <td colspan="3" class="px-6 py-3">
                                    <div class="flex items-center gap-2 text-white font-bold">
                                        <i class="fa-solid fa-hourglass-half"></i>
                                        <span>ما زلنا في الانتظار ({{ $pendingGovernorates->count() }})</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach($pendingGovernorates as $index => $governorate)
                                <tr class="{{ $index % 2 == 0 ? 'bg-gray-50/50' : 'bg-white' }} hover:bg-gray-100 transition-colors">
                                    <!-- Governorate Name -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($governorate->logo_path)
                                                <img src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                                                     alt="{{ $governorate->name_ar }}"
                                                     class="w-10 h-10 object-contain rounded-lg border border-gray-200 opacity-60">
                                            @else
                                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fa-solid fa-city text-gray-400"></i>
                                                </div>
                                            @endif
                                            <span class="font-bold text-gray-500">{{ $governorate->name_ar }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-500 rounded-full text-sm font-bold">
                                            <i class="fa-solid fa-hourglass-half"></i>
                                            قريباً
                                        </span>
                                    </td>
                                    
                                    <!-- Download Button -->
                                    <td class="px-6 py-4 text-center">
                                        <button disabled 
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-400 font-bold rounded-lg cursor-not-allowed">
                                            <i class="fa-solid fa-lock"></i>
                                            غير متاحة
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards View -->
            <div class="md:hidden">
                {{-- Declared Governorates Section (Mobile) --}}
                @if($declaredGovernorates->count() > 0)
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-4 py-3">
                        <div class="flex items-center gap-2 text-white font-bold">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>المحافظات المعتمدة ({{ $declaredGovernorates->count() }})</span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($declaredGovernorates as $governorate)
                            <div class="p-4 bg-green-50/30 hover:bg-green-50 transition-colors">
                                <div class="flex items-start gap-3 mb-3">
                                    @if($governorate->logo_path)
                                        <img src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                                             alt="{{ $governorate->name_ar }}"
                                             class="w-12 h-12 object-contain rounded-lg border border-gray-200 flex-shrink-0">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-city text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-800 text-lg">{{ $governorate->name_ar }}</h3>
                                        <div class="mt-1">
                                            @if($governorate->hasResultPdf())
                                                <span class="inline-flex items-center gap-1 text-green-600 text-sm font-medium">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    متاحة للتحميل
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-amber-600 text-sm font-medium">
                                                    <i class="fa-solid fa-clock"></i>
                                                    معتمدة
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mr-15">
                                    @if($governorate->hasResultPdf())
                                        <a href="{{ $governorate->getResultPdfUrl() }}" 
                                           target="_blank"
                                           class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-xl transition-all shadow-md">
                                            <i class="fa-solid fa-file-pdf"></i>
                                            عرض النتيجة
                                        </a>
                                    @else
                                        <button disabled 
                                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-amber-200 text-amber-600 font-bold rounded-xl cursor-not-allowed">
                                            <i class="fa-solid fa-spinner fa-spin"></i>
                                            جاري الرفع
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Pending Governorates Section (Mobile) --}}
                @if($pendingGovernorates->count() > 0)
                    <div class="bg-gradient-to-r from-gray-400 to-gray-500 px-4 py-3">
                        <div class="flex items-center gap-2 text-white font-bold">
                            <i class="fa-solid fa-hourglass-half"></i>
                            <span>ما زلنا في الانتظار ({{ $pendingGovernorates->count() }})</span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($pendingGovernorates as $governorate)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start gap-3 mb-3">
                                    @if($governorate->logo_path)
                                        <img src="{{ asset('uploads/' . $governorate->logo_path) }}" 
                                             alt="{{ $governorate->name_ar }}"
                                             class="w-12 h-12 object-contain rounded-lg border border-gray-200 flex-shrink-0 opacity-60">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-city text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-500 text-lg">{{ $governorate->name_ar }}</h3>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 text-gray-500 text-sm font-medium">
                                                <i class="fa-solid fa-hourglass-half"></i>
                                                قريباً
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mr-15">
                                    <button disabled 
                                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-200 text-gray-400 font-bold rounded-xl cursor-not-allowed">
                                        <i class="fa-solid fa-lock"></i>
                                        غير متاحة حالياً
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Summary Stats -->
        @php
            $availableCount = $declaredGovernorates->filter(fn($g) => $g->hasResultPdf())->count();
            $declaredCount = $declaredGovernorates->count();
            $totalCount = $allGovernorates->count();
        @endphp
        
        <div class="mt-6 grid grid-cols-3 gap-4">
            <div class="bg-green-50 rounded-xl p-4 text-center border border-green-100">
                <div class="text-3xl font-black text-green-600 mb-1">{{ $availableCount }}</div>
                <div class="text-sm text-green-700 font-medium">متاحة للتحميل</div>
            </div>
            <div class="bg-amber-50 rounded-xl p-4 text-center border border-amber-100">
                <div class="text-3xl font-black text-amber-600 mb-1">{{ $declaredCount - $availableCount }}</div>
                <div class="text-sm text-amber-700 font-medium">جاري الرفع</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-200">
                <div class="text-3xl font-black text-gray-600 mb-1">{{ $totalCount - $declaredCount }}</div>
                <div class="text-sm text-gray-700 font-medium">قريباً</div>
            </div>
        </div>

        <!-- Info Note -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-blue-500 text-xl mt-0.5"></i>
                <div>
                    <h4 class="font-bold text-blue-800 mb-1">ملاحظة هامة</h4>
                    <p class="text-blue-700 text-sm leading-relaxed">
                        يتم تحديث النتائج فور صدورها رسمياً من الجهات المختصة. 
                        يُرجى متابعة الصفحة بشكل دوري للاطلاع على آخر التحديثات.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- No governorates found --}}
        <div class="text-center py-20">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-2xl mb-4">
                <i class="fa-solid fa-city text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">لا توجد محافظات</h3>
            <p class="text-gray-500 text-sm">لم يتم إضافة محافظات لهذه الدولة بعد</p>
        </div>
    @endif

    <!-- Popular Searches -->

</div>
