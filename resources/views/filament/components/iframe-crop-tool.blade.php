@php
    // Get data from viewData
    $embedUrl = $embedUrl ?? '';
    $embedCode = $embedCode ?? '';
    $inputType = $inputType ?? 'url';
    $cropEnabled = $cropEnabled ?? false;
    
    // Get URL
    $url = '';
    if ($inputType === 'url' && $embedUrl) {
        $url = $embedUrl;
    } elseif ($inputType === 'code' && $embedCode) {
        if (preg_match('/src=["\']([^"\']+)["\']/', $embedCode, $matches)) {
            $url = $matches[1];
        }
    }
@endphp

<div 
    x-data="{
        url: '{{ $url }}',
        cropEnabled: {{ $cropEnabled ? 'true' : 'false' }},
        
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
        
        init() {
            this.$nextTick(() => {
                this.updateContainerSize();
            });
        },
        
        updateContainerSize() {
            if (this.$refs.cropContainer) {
                this.containerWidth = this.$refs.cropContainer.offsetWidth;
                this.containerHeight = this.$refs.cropContainer.offsetHeight;
            }
        },
        
        getMousePos(e) {
            let rect = this.$refs.cropOverlay.getBoundingClientRect();
            return {
                x: Math.max(0, Math.min(e.clientX - rect.left, this.containerWidth)),
                y: Math.max(0, Math.min(e.clientY - rect.top, this.containerHeight))
            };
        },
        
        startSelection(e) {
            if (!this.cropEnabled) return;
            if (this.isDragging || this.isResizing) return;
            
            let pos = this.getMousePos(e);
            this.startX = pos.x;
            this.startY = pos.y;
            this.isSelecting = true;
            this.selectionX = pos.x;
            this.selectionY = pos.y;
            this.selectionWidth = 0;
            this.selectionHeight = 0;
        },
        
        updateSelection(e) {
            let pos = this.getMousePos(e);
            
            if (this.isSelecting) {
                if (pos.x >= this.startX) {
                    this.selectionX = this.startX;
                    this.selectionWidth = pos.x - this.startX;
                } else {
                    this.selectionX = pos.x;
                    this.selectionWidth = this.startX - pos.x;
                }
                
                if (pos.y >= this.startY) {
                    this.selectionY = this.startY;
                    this.selectionHeight = pos.y - this.startY;
                } else {
                    this.selectionY = pos.y;
                    this.selectionHeight = this.startY - pos.y;
                }
            } else if (this.isDragging) {
                let newX = pos.x - this.dragOffsetX;
                let newY = pos.y - this.dragOffsetY;
                newX = Math.max(0, Math.min(newX, this.containerWidth - this.selectionWidth));
                newY = Math.max(0, Math.min(newY, this.containerHeight - this.selectionHeight));
                this.selectionX = newX;
                this.selectionY = newY;
            } else if (this.isResizing) {
                this.handleResize(pos.x, pos.y);
            }
        },
        
        endSelection() {
            if (this.isSelecting && this.selectionWidth > 10 && this.selectionHeight > 10) {
                this.hasSelection = true;
                this.updateCropValues();
            }
            this.isSelecting = false;
            this.isDragging = false;
            this.isResizing = false;
            this.resizeHandle = null;
        },
        
        startDrag(e) {
            e.preventDefault();
            e.stopPropagation();
            let pos = this.getMousePos(e);
            this.dragOffsetX = pos.x - this.selectionX;
            this.dragOffsetY = pos.y - this.selectionY;
            this.isDragging = true;
        },
        
        startResize(e, handle) {
            e.preventDefault();
            e.stopPropagation();
            this.isResizing = true;
            this.resizeHandle = handle;
        },
        
        handleResize(x, y) {
            let minSize = 30;
            let newX = this.selectionX;
            let newY = this.selectionY;
            let newW = this.selectionWidth;
            let newH = this.selectionHeight;
            
            if (this.resizeHandle && this.resizeHandle.includes('w')) {
                let diff = this.selectionX - x;
                if (this.selectionWidth + diff > minSize) {
                    newX = x;
                    newW = this.selectionWidth + diff;
                }
            }
            if (this.resizeHandle && this.resizeHandle.includes('e')) {
                newW = Math.max(minSize, x - this.selectionX);
            }
            if (this.resizeHandle && this.resizeHandle.includes('n')) {
                let diff = this.selectionY - y;
                if (this.selectionHeight + diff > minSize) {
                    newY = y;
                    newH = this.selectionHeight + diff;
                }
            }
            if (this.resizeHandle && this.resizeHandle.includes('s')) {
                newH = Math.max(minSize, y - this.selectionY);
            }
            
            this.selectionX = Math.max(0, newX);
            this.selectionY = Math.max(0, newY);
            this.selectionWidth = Math.min(newW, this.containerWidth - this.selectionX);
            this.selectionHeight = Math.min(newH, this.containerHeight - this.selectionY);
            
            this.updateCropValues();
        },
        
        updateCropValues() {
            let scaleX = this.iframeWidth / this.containerWidth;
            let scaleY = this.iframeHeight / this.containerHeight;
            
            let cropTop = Math.round(this.selectionY * scaleY);
            let cropLeft = Math.round(this.selectionX * scaleX);
            
            // Update Livewire - without 'data.' prefix for UploadResult page
            $wire.set('data.iframe_crop_top', String(cropTop));
            $wire.set('data.iframe_crop_left', String(cropLeft));
        },
        
        clearSelection() {
            this.selectionX = 0;
            this.selectionY = 0;
            this.selectionWidth = 0;
            this.selectionHeight = 0;
            this.hasSelection = false;
            
            $wire.set('data.iframe_crop_top', '0');
            $wire.set('data.iframe_crop_left', '0');
        },
        
        selectFullArea() {
            this.selectionX = 0;
            this.selectionY = 0;
            this.selectionWidth = this.containerWidth;
            this.selectionHeight = this.containerHeight;
            this.hasSelection = true;
            
            $wire.set('data.iframe_crop_top', '0');
            $wire.set('data.iframe_crop_left', '0');
        },
        
        getClipPath() {
            if (!this.hasSelection && !this.isSelecting) return 'none';
            if (this.selectionWidth < 5 || this.selectionHeight < 5) return 'none';
            
            let x1 = this.selectionX;
            let y1 = this.selectionY;
            let x2 = this.selectionX + this.selectionWidth;
            let y2 = this.selectionY + this.selectionHeight;
            
            return 'polygon(0 0, 100% 0, 100% 100%, 0 100%, 0 0, ' + x1 + 'px ' + y1 + 'px, ' + x1 + 'px ' + y2 + 'px, ' + x2 + 'px ' + y2 + 'px, ' + x2 + 'px ' + y1 + 'px, ' + x1 + 'px ' + y1 + 'px)';
        }
    }"
    x-init="init()"
    @resize.window.debounce="updateContainerSize()"
    class="space-y-4"
>
    <!-- Instructions -->
    <div class="p-3 rounded-lg" :class="cropEnabled ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800' : 'bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700'">
        <p class="text-sm">
            <span x-show="!cropEnabled" class="text-gray-500 dark:text-gray-400">
                ⬆️ فعّل "تفعيل تحديد المنطقة" أعلاه لاستخدام أداة القص
            </span>
            <span x-show="cropEnabled" class="text-primary-700 dark:text-primary-300">
                <span class="font-bold">🖱️ اسحب بالماوس</span> لتحديد المنطقة المراد عرضها
            </span>
        </p>
    </div>
    
    <!-- Crop Container -->
    <div 
        x-ref="cropContainer"
        class="relative bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden shadow-inner"
        style="height: 400px;"
        :class="{ 'ring-2 ring-primary-500': cropEnabled }"
    >
        @if($url)
        <!-- Iframe Preview -->
        <iframe 
            src="{{ $url }}"
            class="absolute inset-0 w-full h-full border-0"
            loading="lazy"
        ></iframe>
        @else
        <!-- No URL -->
        <div class="absolute inset-0 flex items-center justify-center text-gray-400">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/>
                </svg>
                <p class="text-sm">أدخل رابط الـ iframe أولاً</p>
            </div>
        </div>
        @endif
        
        <!-- Crop Overlay -->
        @if($url && $cropEnabled)
        <div 
            x-ref="cropOverlay"
            @mousedown.prevent="startSelection($event)"
            @mousemove="updateSelection($event)"
            @mouseup="endSelection()"
            @mouseleave="endSelection()"
            class="absolute inset-0 z-10 cursor-crosshair"
        >
            <!-- Dark overlay -->
            <div 
                class="absolute inset-0 bg-black/50 pointer-events-none"
                :style="'clip-path: ' + getClipPath()"
            ></div>
            
            <!-- Selection Box -->
            <div 
                x-show="(hasSelection || isSelecting) && selectionWidth > 5 && selectionHeight > 5"
                @mousedown.stop="startDrag($event)"
                class="absolute border-2 border-primary-500 bg-transparent cursor-move shadow-lg"
                :style="'left:' + selectionX + 'px;top:' + selectionY + 'px;width:' + selectionWidth + 'px;height:' + selectionHeight + 'px;'"
            >
                <div class="absolute inset-0 border border-dashed border-white/50 pointer-events-none"></div>
                
                <!-- Corners -->
                <div @mousedown.stop="startResize($event, 'nw')" class="absolute -left-2 -top-2 w-4 h-4 bg-white border-2 border-primary-500 rounded-sm cursor-nw-resize shadow"></div>
                <div @mousedown.stop="startResize($event, 'ne')" class="absolute -right-2 -top-2 w-4 h-4 bg-white border-2 border-primary-500 rounded-sm cursor-ne-resize shadow"></div>
                <div @mousedown.stop="startResize($event, 'sw')" class="absolute -left-2 -bottom-2 w-4 h-4 bg-white border-2 border-primary-500 rounded-sm cursor-sw-resize shadow"></div>
                <div @mousedown.stop="startResize($event, 'se')" class="absolute -right-2 -bottom-2 w-4 h-4 bg-white border-2 border-primary-500 rounded-sm cursor-se-resize shadow"></div>
                
                <!-- Edges -->
                <div @mousedown.stop="startResize($event, 'n')" class="absolute left-1/2 -translate-x-1/2 -top-1.5 w-8 h-3 bg-white border border-primary-500 rounded-sm cursor-n-resize"></div>
                <div @mousedown.stop="startResize($event, 's')" class="absolute left-1/2 -translate-x-1/2 -bottom-1.5 w-8 h-3 bg-white border border-primary-500 rounded-sm cursor-s-resize"></div>
                <div @mousedown.stop="startResize($event, 'w')" class="absolute top-1/2 -translate-y-1/2 -left-1.5 w-3 h-8 bg-white border border-primary-500 rounded-sm cursor-w-resize"></div>
                <div @mousedown.stop="startResize($event, 'e')" class="absolute top-1/2 -translate-y-1/2 -right-1.5 w-3 h-8 bg-white border border-primary-500 rounded-sm cursor-e-resize"></div>
                
                <!-- Size label -->
                <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-black/80 text-white text-xs px-3 py-1 rounded-full whitespace-nowrap">
                    <span x-text="Math.round(selectionWidth) + ' × ' + Math.round(selectionHeight) + ' px'"></span>
                </div>
            </div>
            
            <!-- Instructions overlay -->
            <div 
                x-show="!hasSelection && !isSelecting"
                class="absolute inset-0 flex items-center justify-center pointer-events-none"
            >
                <div class="bg-black/60 text-white px-6 py-4 rounded-xl text-center">
                    <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672z"/>
                    </svg>
                    <p class="text-sm font-medium">اسحب هنا لتحديد المنطقة</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Controls -->
    @if($cropEnabled)
    <div class="flex flex-wrap items-center gap-3">
        <button 
            type="button"
            @click="clearSelection()"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
        >
            🔄 إعادة تعيين
        </button>
        
        <button 
            type="button"
            @click="selectFullArea()"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors"
        >
            ⬜ تحديد الكل
        </button>
        
        <div class="flex-1"></div>
        
        <div 
            x-show="hasSelection"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg"
        >
            ✅ تم تحديد المنطقة
        </div>
    </div>
    @endif
</div>
