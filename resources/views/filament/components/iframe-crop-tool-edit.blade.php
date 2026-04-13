<div>
    <!-- Instructions -->
    <div class="p-3 mb-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
        <p class="text-sm text-blue-800 dark:text-blue-300">
            <span class="font-bold">🖱️ أداة القص التفاعلية:</span>
            فعّل "تحديد المنطقة" ثم اسحب بالماوس على المعاينة لتحديد الجزء المراد عرضه
        </p>
    </div>
    
    <!-- Crop Tool Container -->
    <div 
        id="cropContainerEdit"
        class="relative bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden shadow-inner mb-4"
        style="height: 400px;"
    >
        <!-- Iframe will be loaded here -->
        <div id="iframeWrapperEdit" class="absolute inset-0">
            <div id="noUrlMessageEdit" class="absolute inset-0 flex items-center justify-center text-gray-400 dark:text-gray-500">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/>
                    </svg>
                    <p class="text-sm">أدخل رابط الـ iframe أولاً</p>
                </div>
            </div>
        </div>
        
        <!-- Crop Overlay -->
        <div 
            id="cropOverlayEdit"
            class="absolute inset-0 z-10 hidden cursor-crosshair"
        >
            <!-- Dark overlay -->
            <div id="darkOverlayEdit" class="absolute inset-0 bg-black/50 pointer-events-none"></div>
            
            <!-- Selection Box -->
            <div 
                id="selectionBoxEdit"
                class="absolute border-2 border-blue-500 bg-transparent cursor-move shadow-lg hidden"
            >
                <div class="absolute inset-0 border border-dashed border-white/50 pointer-events-none"></div>
                
                <!-- Corners -->
                <div data-handle="nw" class="resize-handle-edit absolute -left-2 -top-2 w-4 h-4 bg-white border-2 border-blue-500 rounded-sm cursor-nw-resize shadow"></div>
                <div data-handle="ne" class="resize-handle-edit absolute -right-2 -top-2 w-4 h-4 bg-white border-2 border-blue-500 rounded-sm cursor-ne-resize shadow"></div>
                <div data-handle="sw" class="resize-handle-edit absolute -left-2 -bottom-2 w-4 h-4 bg-white border-2 border-blue-500 rounded-sm cursor-sw-resize shadow"></div>
                <div data-handle="se" class="resize-handle-edit absolute -right-2 -bottom-2 w-4 h-4 bg-white border-2 border-blue-500 rounded-sm cursor-se-resize shadow"></div>
                
                <!-- Edges -->
                <div data-handle="n" class="resize-handle-edit absolute left-1/2 -translate-x-1/2 -top-1.5 w-8 h-3 bg-white border border-blue-500 rounded-sm cursor-n-resize"></div>
                <div data-handle="s" class="resize-handle-edit absolute left-1/2 -translate-x-1/2 -bottom-1.5 w-8 h-3 bg-white border border-blue-500 rounded-sm cursor-s-resize"></div>
                <div data-handle="w" class="resize-handle-edit absolute top-1/2 -translate-y-1/2 -left-1.5 w-3 h-8 bg-white border border-blue-500 rounded-sm cursor-w-resize"></div>
                <div data-handle="e" class="resize-handle-edit absolute top-1/2 -translate-y-1/2 -right-1.5 w-3 h-8 bg-white border border-blue-500 rounded-sm cursor-e-resize"></div>
                
                <!-- Size label -->
                <div id="sizeLabelEdit" class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-black/80 text-white text-xs px-3 py-1 rounded-full whitespace-nowrap"></div>
            </div>
            
            <!-- Instructions overlay -->
            <div id="instructionsOverlayEdit" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="bg-black/60 text-white px-6 py-4 rounded-xl text-center">
                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672z"/>
                    </svg>
                    <p class="text-sm font-medium">اسحب هنا لتحديد المنطقة</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Controls -->
    <div id="cropControlsEdit" class="hidden flex flex-wrap items-center gap-3">
        <button 
            type="button"
            id="btnResetEdit"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
        >
            🔄 إعادة تعيين
        </button>
        
        <button 
            type="button"
            id="btnSelectAllEdit"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
        >
            ⬜ تحديد الكل
        </button>
        
        <div class="flex-1"></div>
        
        <div 
            id="statusBadgeEdit"
            class="hidden inline-flex items-center gap-2 px-3 py-1.5 text-sm bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg"
        >
            ✅ تم تحديد المنطقة
        </div>
    </div>
</div>

<script>
(function() {
    // Elements
    var container = document.getElementById('cropContainerEdit');
    var overlay = document.getElementById('cropOverlayEdit');
    var selectionBox = document.getElementById('selectionBoxEdit');
    var darkOverlay = document.getElementById('darkOverlayEdit');
    var sizeLabel = document.getElementById('sizeLabelEdit');
    var instructionsOverlay = document.getElementById('instructionsOverlayEdit');
    var controls = document.getElementById('cropControlsEdit');
    var btnReset = document.getElementById('btnResetEdit');
    var btnSelectAll = document.getElementById('btnSelectAllEdit');
    var statusBadge = document.getElementById('statusBadgeEdit');
    var iframeWrapper = document.getElementById('iframeWrapperEdit');
    var noUrlMessage = document.getElementById('noUrlMessageEdit');
    
    // State
    var state = {
        isSelecting: false,
        isDragging: false,
        isResizing: false,
        resizeHandle: null,
        startX: 0,
        startY: 0,
        dragOffsetX: 0,
        dragOffsetY: 0,
        selectionX: 0,
        selectionY: 0,
        selectionWidth: 0,
        selectionHeight: 0,
        hasSelection: false,
        containerWidth: 0,
        containerHeight: 0,
        iframeWidth: 1920,
        iframeHeight: 1080,
        url: '',
        cropEnabled: false,
        iframe: null
    };
    
    function updateContainerSize() {
        if (container) {
            state.containerWidth = container.offsetWidth;
            state.containerHeight = container.offsetHeight;
        }
    }
    
    function getUrl() {
        // For edit page, fields are under extra_data
        var urlInput = document.querySelector('input[id*="embed_url"]');
        if (urlInput && urlInput.value) {
            return urlInput.value;
        }
        
        var codeInput = document.querySelector('textarea[id*="embed_code"]');
        if (codeInput && codeInput.value) {
            var match = codeInput.value.match(/src=["']([^"']+)["']/);
            if (match) return match[1];
        }
        
        return '';
    }
    
    function getCropEnabled() {
        var toggle = document.querySelector('input[id*="crop_enabled"]');
        if (toggle) {
            return toggle.checked;
        }
        return false;
    }
    
    function updateIframe() {
        var url = getUrl();
        state.cropEnabled = getCropEnabled();
        
        if (url && url !== state.url) {
            state.url = url;
            
            if (state.iframe) {
                state.iframe.remove();
            }
            
            state.iframe = document.createElement('iframe');
            state.iframe.src = url;
            state.iframe.className = 'absolute inset-0 w-full h-full border-0';
            state.iframe.loading = 'lazy';
            iframeWrapper.insertBefore(state.iframe, noUrlMessage);
            noUrlMessage.classList.add('hidden');
        } else if (!url) {
            if (state.iframe) {
                state.iframe.remove();
                state.iframe = null;
            }
            noUrlMessage.classList.remove('hidden');
        }
        
        if (state.cropEnabled && state.url) {
            overlay.classList.remove('hidden');
            controls.classList.remove('hidden');
            controls.classList.add('flex');
            container.classList.add('ring-2', 'ring-blue-500');
        } else {
            overlay.classList.add('hidden');
            controls.classList.add('hidden');
            controls.classList.remove('flex');
            container.classList.remove('ring-2', 'ring-blue-500');
        }
    }
    
    function getMousePos(e) {
        var rect = overlay.getBoundingClientRect();
        return {
            x: Math.max(0, Math.min(e.clientX - rect.left, state.containerWidth)),
            y: Math.max(0, Math.min(e.clientY - rect.top, state.containerHeight))
        };
    }
    
    function updateSelectionBox() {
        if (state.selectionWidth > 5 && state.selectionHeight > 5 && (state.hasSelection || state.isSelecting)) {
            selectionBox.style.left = state.selectionX + 'px';
            selectionBox.style.top = state.selectionY + 'px';
            selectionBox.style.width = state.selectionWidth + 'px';
            selectionBox.style.height = state.selectionHeight + 'px';
            selectionBox.classList.remove('hidden');
            
            sizeLabel.textContent = Math.round(state.selectionWidth) + ' × ' + Math.round(state.selectionHeight) + ' px';
            
            var x1 = state.selectionX;
            var y1 = state.selectionY;
            var x2 = state.selectionX + state.selectionWidth;
            var y2 = state.selectionY + state.selectionHeight;
            
            darkOverlay.style.clipPath = 'polygon(0 0, 100% 0, 100% 100%, 0 100%, 0 0, ' + 
                x1 + 'px ' + y1 + 'px, ' + 
                x1 + 'px ' + y2 + 'px, ' + 
                x2 + 'px ' + y2 + 'px, ' + 
                x2 + 'px ' + y1 + 'px, ' + 
                x1 + 'px ' + y1 + 'px)';
            
            instructionsOverlay.classList.add('hidden');
        } else {
            selectionBox.classList.add('hidden');
            darkOverlay.style.clipPath = 'none';
            instructionsOverlay.classList.remove('hidden');
        }
        
        if (state.hasSelection) {
            statusBadge.classList.remove('hidden');
            statusBadge.classList.add('inline-flex');
        } else {
            statusBadge.classList.add('hidden');
            statusBadge.classList.remove('inline-flex');
        }
    }
    
    function updateCropValues() {
        var scaleX = state.iframeWidth / state.containerWidth;
        var scaleY = state.iframeHeight / state.containerHeight;
        
        var cropTop = Math.round(state.selectionY * scaleY);
        var cropLeft = Math.round(state.selectionX * scaleX);
        
        var selectedWidthRatio = state.selectionWidth / state.containerWidth;
        var zoom = selectedWidthRatio > 0 ? Math.round(100 / selectedWidthRatio) : 100;
        zoom = Math.min(200, Math.max(50, zoom));
        
        // Update form fields (for edit page with extra_data prefix)
        var topInput = document.querySelector('input[id*="crop_top"]');
        var leftInput = document.querySelector('input[id*="crop_left"]');
        var zoomInput = document.querySelector('input[id*="zoom"]');
        
        if (topInput) {
            topInput.value = cropTop;
            topInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (leftInput) {
            leftInput.value = cropLeft;
            leftInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
        if (zoomInput) {
            zoomInput.value = zoom;
            zoomInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
    
    function clearSelection() {
        state.selectionX = 0;
        state.selectionY = 0;
        state.selectionWidth = 0;
        state.selectionHeight = 0;
        state.hasSelection = false;
        updateSelectionBox();
        updateCropValues();
    }
    
    function selectFullArea() {
        state.selectionX = 0;
        state.selectionY = 0;
        state.selectionWidth = state.containerWidth;
        state.selectionHeight = state.containerHeight;
        state.hasSelection = true;
        updateSelectionBox();
        updateCropValues();
    }
    
    // Event Handlers
    overlay.addEventListener('mousedown', function(e) {
        if (e.target.classList.contains('resize-handle-edit')) return;
        if (e.target === selectionBox || selectionBox.contains(e.target)) return;
        
        var pos = getMousePos(e);
        state.startX = pos.x;
        state.startY = pos.y;
        state.isSelecting = true;
        state.selectionX = pos.x;
        state.selectionY = pos.y;
        state.selectionWidth = 0;
        state.selectionHeight = 0;
        e.preventDefault();
    });
    
    overlay.addEventListener('mousemove', function(e) {
        var pos = getMousePos(e);
        
        if (state.isSelecting) {
            if (pos.x >= state.startX) {
                state.selectionX = state.startX;
                state.selectionWidth = pos.x - state.startX;
            } else {
                state.selectionX = pos.x;
                state.selectionWidth = state.startX - pos.x;
            }
            
            if (pos.y >= state.startY) {
                state.selectionY = state.startY;
                state.selectionHeight = pos.y - state.startY;
            } else {
                state.selectionY = pos.y;
                state.selectionHeight = state.startY - pos.y;
            }
            updateSelectionBox();
        } else if (state.isDragging) {
            var newX = pos.x - state.dragOffsetX;
            var newY = pos.y - state.dragOffsetY;
            newX = Math.max(0, Math.min(newX, state.containerWidth - state.selectionWidth));
            newY = Math.max(0, Math.min(newY, state.containerHeight - state.selectionHeight));
            state.selectionX = newX;
            state.selectionY = newY;
            updateSelectionBox();
        } else if (state.isResizing) {
            handleResize(pos.x, pos.y);
        }
    });
    
    overlay.addEventListener('mouseup', function() {
        if (state.isSelecting && state.selectionWidth > 10 && state.selectionHeight > 10) {
            state.hasSelection = true;
            updateCropValues();
        }
        state.isSelecting = false;
        state.isDragging = false;
        state.isResizing = false;
        state.resizeHandle = null;
        updateSelectionBox();
    });
    
    overlay.addEventListener('mouseleave', function() {
        if (state.isSelecting && state.selectionWidth > 10 && state.selectionHeight > 10) {
            state.hasSelection = true;
            updateCropValues();
        }
        state.isSelecting = false;
        state.isDragging = false;
        state.isResizing = false;
        state.resizeHandle = null;
    });
    
    selectionBox.addEventListener('mousedown', function(e) {
        if (e.target.classList.contains('resize-handle-edit')) return;
        
        var pos = getMousePos(e);
        state.dragOffsetX = pos.x - state.selectionX;
        state.dragOffsetY = pos.y - state.selectionY;
        state.isDragging = true;
        e.preventDefault();
        e.stopPropagation();
    });
    
    // Resize handles
    document.querySelectorAll('.resize-handle-edit').forEach(function(handle) {
        handle.addEventListener('mousedown', function(e) {
            state.isResizing = true;
            state.resizeHandle = handle.dataset.handle;
            e.preventDefault();
            e.stopPropagation();
        });
    });
    
    function handleResize(x, y) {
        var minSize = 30;
        var newX = state.selectionX;
        var newY = state.selectionY;
        var newW = state.selectionWidth;
        var newH = state.selectionHeight;
        
        if (state.resizeHandle.includes('w')) {
            var diff = state.selectionX - x;
            if (state.selectionWidth + diff > minSize) {
                newX = x;
                newW = state.selectionWidth + diff;
            }
        }
        if (state.resizeHandle.includes('e')) {
            newW = Math.max(minSize, x - state.selectionX);
        }
        if (state.resizeHandle.includes('n')) {
            var diff = state.selectionY - y;
            if (state.selectionHeight + diff > minSize) {
                newY = y;
                newH = state.selectionHeight + diff;
            }
        }
        if (state.resizeHandle.includes('s')) {
            newH = Math.max(minSize, y - state.selectionY);
        }
        
        state.selectionX = Math.max(0, newX);
        state.selectionY = Math.max(0, newY);
        state.selectionWidth = Math.min(newW, state.containerWidth - state.selectionX);
        state.selectionHeight = Math.min(newH, state.containerHeight - state.selectionY);
        
        updateSelectionBox();
        updateCropValues();
    }
    
    // Button handlers
    btnReset.addEventListener('click', clearSelection);
    btnSelectAll.addEventListener('click', selectFullArea);
    
    // Initialize
    updateContainerSize();
    updateIframe();
    
    // Watch for changes
    setInterval(function() {
        updateIframe();
    }, 500);
    
    window.addEventListener('resize', function() {
        updateContainerSize();
    });
})();
</script>
