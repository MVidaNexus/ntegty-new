// Iframe Crop Tool - Alpine.js Component
document.addEventListener('alpine:init', () => {
    Alpine.data('iframeCropTool', () => ({
        // State
        isSelecting: false,
        isDragging: false,
        isResizing: false,
        resizeHandle: null,
        startX: 0,
        startY: 0,
        dragOffsetX: 0,
        dragOffsetY: 0,
        
        // Selection box (screen coordinates)
        selectionX: 0,
        selectionY: 0,
        selectionWidth: 0,
        selectionHeight: 0,
        hasSelection: false,
        
        // Container dimensions
        containerWidth: 0,
        containerHeight: 0,
        
        // Iframe assumed dimensions
        iframeWidth: 1920,
        iframeHeight: 1080,
        
        // Current URL
        url: '',
        
        init() {
            this.$nextTick(() => {
                this.updateContainerSize();
                this.watchInputs();
                this.loadExistingValues();
            });
            
            // Re-check on window resize
            window.addEventListener('resize', () => {
                this.updateContainerSize();
                this.loadExistingValues();
            });
        },
        
        watchInputs() {
            try {
                const inputType = this.$wire.get('data.embed_input_type') || 'url';
                if (inputType === 'url') {
                    this.url = this.$wire.get('data.embed_url') || '';
                } else {
                    const code = this.$wire.get('data.embed_code') || '';
                    const match = code.match(/src=["']([^"']+)["']/);
                    if (match) this.url = match[1];
                }
            } catch (e) {
                console.log('Waiting for Livewire...');
            }
        },
        
        updateContainerSize() {
            const container = this.$refs.cropContainer;
            if (container) {
                this.containerWidth = container.offsetWidth;
                this.containerHeight = container.offsetHeight;
            }
        },
        
        loadExistingValues() {
            try {
                const top = parseInt(this.$wire.get('data.iframe_crop_top')) || 0;
                const left = parseInt(this.$wire.get('data.iframe_crop_left')) || 0;
                
                if ((top > 0 || left > 0) && this.containerWidth > 0) {
                    const scaleX = this.containerWidth / this.iframeWidth;
                    const scaleY = this.containerHeight / this.iframeHeight;
                    
                    this.selectionX = left * scaleX;
                    this.selectionY = top * scaleY;
                    this.selectionWidth = this.containerWidth - this.selectionX;
                    this.selectionHeight = this.containerHeight - this.selectionY;
                    this.hasSelection = true;
                }
            } catch (e) {}
        },
        
        getCropEnabled() {
            try {
                return this.$wire.get('data.iframe_crop_enabled') ?? false;
            } catch (e) {
                return false;
            }
        },
        
        getMousePos(e) {
            const rect = this.$refs.cropOverlay.getBoundingClientRect();
            return {
                x: Math.max(0, Math.min(e.clientX - rect.left, this.containerWidth)),
                y: Math.max(0, Math.min(e.clientY - rect.top, this.containerHeight))
            };
        },
        
        startSelection(e) {
            if (!this.getCropEnabled()) return;
            if (this.isDragging || this.isResizing) return;
            
            const pos = this.getMousePos(e);
            this.startX = pos.x;
            this.startY = pos.y;
            this.isSelecting = true;
            this.selectionX = pos.x;
            this.selectionY = pos.y;
            this.selectionWidth = 0;
            this.selectionHeight = 0;
        },
        
        updateSelection(e) {
            const pos = this.getMousePos(e);
            
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
            const pos = this.getMousePos(e);
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
            const minSize = 30;
            let newX = this.selectionX;
            let newY = this.selectionY;
            let newW = this.selectionWidth;
            let newH = this.selectionHeight;
            
            if (this.resizeHandle.includes('w')) {
                const diff = this.selectionX - x;
                if (this.selectionWidth + diff > minSize) {
                    newX = x;
                    newW = this.selectionWidth + diff;
                }
            }
            if (this.resizeHandle.includes('e')) {
                newW = Math.max(minSize, x - this.selectionX);
            }
            if (this.resizeHandle.includes('n')) {
                const diff = this.selectionY - y;
                if (this.selectionHeight + diff > minSize) {
                    newY = y;
                    newH = this.selectionHeight + diff;
                }
            }
            if (this.resizeHandle.includes('s')) {
                newH = Math.max(minSize, y - this.selectionY);
            }
            
            this.selectionX = Math.max(0, newX);
            this.selectionY = Math.max(0, newY);
            this.selectionWidth = Math.min(newW, this.containerWidth - this.selectionX);
            this.selectionHeight = Math.min(newH, this.containerHeight - this.selectionY);
            
            this.updateCropValues();
        },
        
        updateCropValues() {
            const scaleX = this.iframeWidth / this.containerWidth;
            const scaleY = this.iframeHeight / this.containerHeight;
            
            const cropTop = Math.round(this.selectionY * scaleY);
            const cropLeft = Math.round(this.selectionX * scaleX);
            
            const selectedWidthRatio = this.selectionWidth / this.containerWidth;
            const zoom = selectedWidthRatio > 0 ? Math.round(100 / selectedWidthRatio) : 100;
            
            try {
                this.$wire.set('data.iframe_crop_top', cropTop);
                this.$wire.set('data.iframe_crop_left', cropLeft);
                this.$wire.set('data.iframe_zoom', Math.min(200, Math.max(50, zoom)));
            } catch (e) {}
        },
        
        clearSelection() {
            this.selectionX = 0;
            this.selectionY = 0;
            this.selectionWidth = 0;
            this.selectionHeight = 0;
            this.hasSelection = false;
            
            try {
                this.$wire.set('data.iframe_crop_top', 0);
                this.$wire.set('data.iframe_crop_left', 0);
                this.$wire.set('data.iframe_zoom', 100);
            } catch (e) {}
        },
        
        selectFullArea() {
            this.selectionX = 0;
            this.selectionY = 0;
            this.selectionWidth = this.containerWidth;
            this.selectionHeight = this.containerHeight;
            this.hasSelection = true;
            
            try {
                this.$wire.set('data.iframe_crop_top', 0);
                this.$wire.set('data.iframe_crop_left', 0);
                this.$wire.set('data.iframe_zoom', 100);
            } catch (e) {}
        },
        
        getClipPath() {
            if (!this.hasSelection && !this.isSelecting) return 'none';
            if (this.selectionWidth < 5 || this.selectionHeight < 5) return 'none';
            
            const x1 = this.selectionX;
            const y1 = this.selectionY;
            const x2 = this.selectionX + this.selectionWidth;
            const y2 = this.selectionY + this.selectionHeight;
            
            return `polygon(0 0, 100% 0, 100% 100%, 0 100%, 0 0, ${x1}px ${y1}px, ${x1}px ${y2}px, ${x2}px ${y2}px, ${x2}px ${y1}px, ${x1}px ${y1}px)`;
        },
        
        getSelectionStyle() {
            return `left: ${this.selectionX}px; top: ${this.selectionY}px; width: ${this.selectionWidth}px; height: ${this.selectionHeight}px;`;
        }
    }));
});
