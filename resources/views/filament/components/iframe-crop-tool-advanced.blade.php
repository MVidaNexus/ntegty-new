<div>
    <!-- Instructions -->
    <div class="p-3 mb-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
        <p class="text-sm text-blue-800 dark:text-blue-300">
            <span class="font-bold"><i class="fa-solid fa-ruler-combined text-slate-500"></i> أداة القص:</span>
            استخدم أزرار الضبط أو أدخل القيم يدوياً في الحقول أدناه. القيم بالبكسل.
        </p>
    </div>
    
    <!-- Quick Crop Presets -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><i class="fa-solid fa-bolt text-yellow-500"></i> ضبط سريع:</label>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="setCropPreset(0, 0, 0, 0)" class="px-3 py-1.5 text-xs bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                <i class="fa-solid fa-rotate text-blue-500"></i> بدون قص
            </button>
            <button type="button" onclick="setCropPreset(100, 0, 0, 0)" class="px-3 py-1.5 text-xs bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 rounded-lg transition-colors">
                ⬆️ هيدر 100px
            </button>
            <button type="button" onclick="setCropPreset(150, 0, 0, 0)" class="px-3 py-1.5 text-xs bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 rounded-lg transition-colors">
                ⬆️ هيدر 150px
            </button>
            <button type="button" onclick="setCropPreset(200, 0, 0, 0)" class="px-3 py-1.5 text-xs bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 rounded-lg transition-colors">
                ⬆️ هيدر 200px
            </button>
            <button type="button" onclick="setCropPreset(0, 0, 100, 0)" class="px-3 py-1.5 text-xs bg-green-100 dark:bg-green-900/30 hover:bg-green-200 rounded-lg transition-colors">
                ⬇️ فوتر 100px
            </button>
            <button type="button" onclick="setCropPreset(100, 0, 50, 0)" class="px-3 py-1.5 text-xs bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 rounded-lg transition-colors">
                ↕️ هيدر + فوتر
            </button>
            <button type="button" onclick="setCropPreset(150, 50, 100, 50)" class="px-3 py-1.5 text-xs bg-amber-100 dark:bg-amber-900/30 hover:bg-amber-200 rounded-lg transition-colors">
                <i class="fa-solid fa-box text-slate-500"></i> كل الجوانب
            </button>
        </div>
    </div>
    
    <!-- Live Preview -->
    <div class="mb-4">
        <div class="flex items-center justify-between mb-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300"><i class="fa-solid fa-eye text-emerald-500"></i>️ معاينة مباشرة:</label>
            <button type="button" onclick="loadPreview()" class="px-3 py-1 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">
                <i class="fa-solid fa-rotate text-blue-500"></i> تحميل/تحديث
            </button>
        </div>
        <div id="cropPreviewContainer" class="relative bg-gray-900 rounded-lg overflow-hidden shadow-inner" style="height: 350px;">
            <div id="cropPreviewMessage" class="absolute inset-0 flex items-center justify-center text-gray-400">
                <div class="text-center">
                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">اضغط "تحميل/تحديث" لعرض المعاينة</p>
                </div>
            </div>
            <iframe id="cropPreviewIframe" class="absolute border-0 hidden" loading="lazy"></iframe>
            
            <!-- Crop indicators -->
            <div id="cropIndicatorTop" class="absolute top-0 left-0 right-0 bg-red-500/30 border-b-2 border-red-500 hidden z-10">
                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-full bg-red-500 text-white text-xs px-2 py-0.5 rounded-b">أعلى</span>
            </div>
            <div id="cropIndicatorBottom" class="absolute bottom-0 left-0 right-0 bg-red-500/30 border-t-2 border-red-500 hidden z-10">
                <span class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-full bg-red-500 text-white text-xs px-2 py-0.5 rounded-t">أسفل</span>
            </div>
            <div id="cropIndicatorLeft" class="absolute top-0 bottom-0 left-0 bg-red-500/30 border-r-2 border-red-500 hidden z-10"></div>
            <div id="cropIndicatorRight" class="absolute top-0 bottom-0 right-0 bg-red-500/30 border-l-2 border-red-500 hidden z-10"></div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            <i class="fa-solid fa-lightbulb text-yellow-500"></i> المنطقة الحمراء ستُقص من العرض النهائي
        </p>
    </div>
    
    <!-- Crop Adjustment Controls -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg text-center">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">⬆️ من الأعلى</label>
            <div class="flex items-center justify-center gap-1">
                <button type="button" onclick="adjustCrop('top', -50)" class="w-8 h-8 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 rounded text-sm font-bold">-50</button>
                <button type="button" onclick="adjustCrop('top', -10)" class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 rounded text-sm">-10</button>
                <span id="cropTopDisplay" class="w-14 text-center font-bold text-lg text-blue-600">0</span>
                <button type="button" onclick="adjustCrop('top', 10)" class="w-8 h-8 bg-green-100 dark:bg-green-900/30 hover:bg-green-200 rounded text-sm">+10</button>
                <button type="button" onclick="adjustCrop('top', 50)" class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 rounded text-sm font-bold">+50</button>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg text-center">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2"><i class="fa-solid fa-arrow-right text-emerald-500"></i>️ من اليمين</label>
            <div class="flex items-center justify-center gap-1">
                <button type="button" onclick="adjustCrop('right', -50)" class="w-8 h-8 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 rounded text-sm font-bold">-50</button>
                <button type="button" onclick="adjustCrop('right', -10)" class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 rounded text-sm">-10</button>
                <span id="cropRightDisplay" class="w-14 text-center font-bold text-lg text-blue-600">0</span>
                <button type="button" onclick="adjustCrop('right', 10)" class="w-8 h-8 bg-green-100 dark:bg-green-900/30 hover:bg-green-200 rounded text-sm">+10</button>
                <button type="button" onclick="adjustCrop('right', 50)" class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 rounded text-sm font-bold">+50</button>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg text-center">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">⬇️ من الأسفل</label>
            <div class="flex items-center justify-center gap-1">
                <button type="button" onclick="adjustCrop('bottom', -50)" class="w-8 h-8 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 rounded text-sm font-bold">-50</button>
                <button type="button" onclick="adjustCrop('bottom', -10)" class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 rounded text-sm">-10</button>
                <span id="cropBottomDisplay" class="w-14 text-center font-bold text-lg text-blue-600">0</span>
                <button type="button" onclick="adjustCrop('bottom', 10)" class="w-8 h-8 bg-green-100 dark:bg-green-900/30 hover:bg-green-200 rounded text-sm">+10</button>
                <button type="button" onclick="adjustCrop('bottom', 50)" class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 rounded text-sm font-bold">+50</button>
            </div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg text-center">
            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-2">⬅️ من اليسار</label>
            <div class="flex items-center justify-center gap-1">
                <button type="button" onclick="adjustCrop('left', -50)" class="w-8 h-8 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 rounded text-sm font-bold">-50</button>
                <button type="button" onclick="adjustCrop('left', -10)" class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 rounded text-sm">-10</button>
                <span id="cropLeftDisplay" class="w-14 text-center font-bold text-lg text-blue-600">0</span>
                <button type="button" onclick="adjustCrop('left', 10)" class="w-8 h-8 bg-green-100 dark:bg-green-900/30 hover:bg-green-200 rounded text-sm">+10</button>
                <button type="button" onclick="adjustCrop('left', 50)" class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 rounded text-sm font-bold">+50</button>
            </div>
        </div>
    </div>
    
    <!-- Summary -->
    <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                <i class="fa-solid fa-chart-column text-blue-500"></i> ملخص القص:
                <span id="cropSummary" class="font-mono text-blue-600 dark:text-blue-400">أعلى: 0 | يمين: 0 | أسفل: 0 | يسار: 0</span>
            </span>
            <button type="button" onclick="resetAllCrops()" class="px-3 py-1 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 rounded text-xs">
                <i class="fa-solid fa-rotate text-blue-500"></i> إعادة تعيين الكل
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    // Helper to get input by partial ID
    function getInput(name) {
        return document.querySelector('input[id*="' + name + '"][id*="extra_data"]') || 
               document.querySelector('input[id*="' + name + '"]');
    }
    
    // Helper to get textarea by partial ID
    function getTextarea(name) {
        return document.querySelector('textarea[id*="' + name + '"]');
    }
    
    // Get URL from form
    function getEmbedUrl() {
        var urlInput = getInput('embed_url');
        if (urlInput && urlInput.value) {
            return urlInput.value;
        }
        var codeInput = getTextarea('embed_code');
        if (codeInput && codeInput.value) {
            var match = codeInput.value.match(/src=["']([^"']+)["']/);
            if (match) return match[1];
            // If it's just a URL
            if (codeInput.value.startsWith('http')) {
                return codeInput.value.trim();
            }
        }
        return '';
    }
    
    // Get crop values
    function getCropValues() {
        return {
            top: parseInt(getInput('crop_top')?.value) || 0,
            right: parseInt(getInput('crop_right')?.value) || 0,
            bottom: parseInt(getInput('crop_bottom')?.value) || 0,
            left: parseInt(getInput('crop_left')?.value) || 0
        };
    }
    
    // Update display values
    function updateDisplays() {
        var crops = getCropValues();
        
        var topEl = document.getElementById('cropTopDisplay');
        var rightEl = document.getElementById('cropRightDisplay');
        var bottomEl = document.getElementById('cropBottomDisplay');
        var leftEl = document.getElementById('cropLeftDisplay');
        var summaryEl = document.getElementById('cropSummary');
        
        if (topEl) topEl.textContent = crops.top;
        if (rightEl) rightEl.textContent = crops.right;
        if (bottomEl) bottomEl.textContent = crops.bottom;
        if (leftEl) leftEl.textContent = crops.left;
        if (summaryEl) summaryEl.textContent = 'أعلى: ' + crops.top + ' | يمين: ' + crops.right + ' | أسفل: ' + crops.bottom + ' | يسار: ' + crops.left;
        
        updateCropIndicators(crops);
    }
    
    // Update crop indicators on preview
    function updateCropIndicators(crops) {
        var container = document.getElementById('cropPreviewContainer');
        if (!container) return;
        
        var containerHeight = container.offsetHeight;
        var containerWidth = container.offsetWidth;
        
        // Scale factors (assuming original 1920x1080)
        var scaleY = containerHeight / 1080;
        var scaleX = containerWidth / 1920;
        
        var topIndicator = document.getElementById('cropIndicatorTop');
        var bottomIndicator = document.getElementById('cropIndicatorBottom');
        var leftIndicator = document.getElementById('cropIndicatorLeft');
        var rightIndicator = document.getElementById('cropIndicatorRight');
        
        if (topIndicator) {
            if (crops.top > 0) {
                topIndicator.style.height = Math.min(crops.top * scaleY, containerHeight / 2) + 'px';
                topIndicator.classList.remove('hidden');
            } else {
                topIndicator.classList.add('hidden');
            }
        }
        
        if (bottomIndicator) {
            if (crops.bottom > 0) {
                bottomIndicator.style.height = Math.min(crops.bottom * scaleY, containerHeight / 2) + 'px';
                bottomIndicator.classList.remove('hidden');
            } else {
                bottomIndicator.classList.add('hidden');
            }
        }
        
        if (leftIndicator) {
            if (crops.left > 0) {
                leftIndicator.style.width = Math.min(crops.left * scaleX, containerWidth / 2) + 'px';
                leftIndicator.classList.remove('hidden');
            } else {
                leftIndicator.classList.add('hidden');
            }
        }
        
        if (rightIndicator) {
            if (crops.right > 0) {
                rightIndicator.style.width = Math.min(crops.right * scaleX, containerWidth / 2) + 'px';
                rightIndicator.classList.remove('hidden');
            } else {
                rightIndicator.classList.add('hidden');
            }
        }
    }
    
    // Adjust crop value
    window.adjustCrop = function(side, delta) {
        var input = getInput('crop_' + side);
        if (input) {
            var current = parseInt(input.value) || 0;
            var newValue = Math.max(0, current + delta);
            input.value = newValue;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            updateDisplays();
        }
    };
    
    // Reset all crops
    window.resetAllCrops = function() {
        ['top', 'right', 'bottom', 'left'].forEach(function(side) {
            var input = getInput('crop_' + side);
            if (input) {
                input.value = 0;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        updateDisplays();
    };
    
    // Set crop preset
    window.setCropPreset = function(top, right, bottom, left) {
        var inputs = {
            'crop_top': top,
            'crop_right': right,
            'crop_bottom': bottom,
            'crop_left': left
        };
        
        for (var name in inputs) {
            var input = getInput(name);
            if (input) {
                input.value = inputs[name];
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        updateDisplays();
    };
    
    // Load preview
    window.loadPreview = function() {
        var url = getEmbedUrl();
        if (!url) {
            alert('أدخل رابط الـ iframe أولاً في حقل "رابط الصفحة" أعلاه');
            return;
        }
        
        var iframe = document.getElementById('cropPreviewIframe');
        var message = document.getElementById('cropPreviewMessage');
        var container = document.getElementById('cropPreviewContainer');
        
        if (iframe && message && container) {
            iframe.src = url;
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            iframe.style.position = 'absolute';
            iframe.style.top = '0';
            iframe.style.left = '0';
            iframe.classList.remove('hidden');
            message.classList.add('hidden');
            
            updateDisplays();
        }
    };
    
    // Initialize on load
    setTimeout(function() {
        updateDisplays();
        
        // Watch for input changes
        ['crop_top', 'crop_right', 'crop_bottom', 'crop_left'].forEach(function(name) {
            var input = getInput(name);
            if (input) {
                input.addEventListener('input', updateDisplays);
                input.addEventListener('change', updateDisplays);
            }
        });
    }, 500);
})();
</script>
