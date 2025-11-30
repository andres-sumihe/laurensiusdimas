@php
    use Illuminate\Support\Facades\Storage;
    
    $slots = $getLayoutSlots();
    $mediaItems = $getState() ?? [];
    $slotCount = count($slots);
    
    // Ensure mediaItems is an array indexed from 0
    $mediaItems = array_values((array) $mediaItems);
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div 
        x-data="{
            activeSlot: null,
            mediaItems: @js($mediaItems),
            slots: @js($slots),
            slotCount: {{ $slotCount }},
            uploading: false,
            uploadProgress: 0,
            
            // Drag and drop state
            draggedIndex: null,
            dragOverIndex: null,
            
            init() {
                // Listen for media-uploaded event from Livewire 3
                this.$wire.$on('media-uploaded', (data) => {
                    console.log('Received media-uploaded event:', data);
                    if (this.activeSlot !== null && data?.path) {
                        this.setMedia(this.activeSlot, 'url', data.path);
                        this.uploading = false;
                        this.uploadProgress = 0;
                    }
                });
            },
            
            openSlot(index) {
                // Don't open if we're in the middle of a drag
                if (this.draggedIndex !== null) return;
                this.activeSlot = index;
                document.body.style.overflow = 'hidden';
            },
            
            closeSlot() {
                this.activeSlot = null;
                this.uploading = false;
                this.uploadProgress = 0;
                document.body.style.overflow = '';
            },
            
            getMedia(index) {
                return this.mediaItems[index] || { type: 'image', url: null, thumbnailUrl: null };
            },
            
            setMedia(index, field, value) {
                if (!this.mediaItems[index]) {
                    this.mediaItems[index] = { type: 'image', url: null, thumbnailUrl: null };
                }
                this.mediaItems[index][field] = value;
                
                // Auto-detect YouTube URL and set type + thumbnail
                if (field === 'url' && value) {
                    const youtubeId = this.extractYouTubeId(value);
                    if (youtubeId) {
                        this.mediaItems[index].type = 'youtube';
                        // Auto-set thumbnail from YouTube if not already set
                        if (!this.mediaItems[index].thumbnailUrl) {
                            this.mediaItems[index].thumbnailUrl = 'https://img.youtube.com/vi/' + youtubeId + '/maxresdefault.jpg';
                        }
                    }
                }
                
                this.updateState();
            },
            
            // Extract YouTube video ID from various URL formats
            extractYouTubeId(url) {
                if (!url) return null;
                
                // Match various YouTube URL formats
                const patterns = [
                    /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/,
                    /^([a-zA-Z0-9_-]{11})$/ // Just the ID
                ];
                
                for (const pattern of patterns) {
                    const match = url.match(pattern);
                    if (match) return match[1];
                }
                
                return null;
            },
            
            // Get YouTube embed URL from video ID or URL
            getYouTubeEmbedUrl(url) {
                const id = this.extractYouTubeId(url);
                return id ? 'https://www.youtube.com/embed/' + id : null;
            },
            
            // Check if media is YouTube
            isYouTube(index) {
                const media = this.mediaItems[index];
                return media?.type === 'youtube' || (media?.url && this.extractYouTubeId(media.url));
            },
            
            // Get YouTube thumbnail
            getYouTubeThumbnail(index) {
                const media = this.mediaItems[index];
                if (!media?.url) return null;
                
                const id = this.extractYouTubeId(media.url);
                if (id) {
                    return media.thumbnailUrl || 'https://img.youtube.com/vi/' + id + '/maxresdefault.jpg';
                }
                return null;
            },
            
            updateState() {
                $wire.set('{{ $getStatePath() }}', this.mediaItems);
            },
            
            hasMedia(index) {
                return this.mediaItems[index]?.url;
            },
            
            getPreviewUrl(index) {
                const media = this.mediaItems[index];
                if (!media?.url) return null;
                
                // For YouTube, return thumbnail
                if (this.isYouTube(index)) {
                    return this.getYouTubeThumbnail(index);
                }
                
                if (media.url.startsWith('http')) {
                    return media.url;
                }
                
                return '/storage/' + media.url;
            },
            
            removeMedia(index) {
                if (this.mediaItems[index]) {
                    this.mediaItems[index] = { type: 'image', url: null, thumbnailUrl: null };
                    this.updateState();
                }
            },
            
            getColWidth(cols) {
                return (cols / 12 * 100) + '%';
            },
            
            getFilledCount() {
                return this.mediaItems.filter(m => m && m.url).length;
            },
            
            isValid() {
                return this.getFilledCount() >= this.slotCount;
            },
            
            uploadFile(file) {
                if (!file) return;
                
                this.uploading = true;
                this.uploadProgress = 0;
                
                $wire.upload('mediaUpload', file, 
                    (uploadedFilename) => {
                        // Success - the event listener will handle setting the path
                        console.log('Upload success, waiting for event...', uploadedFilename);
                    },
                    (error) => {
                        // Error
                        this.uploading = false;
                        this.uploadProgress = 0;
                        console.error('Upload failed:', error);
                        alert('Upload failed. Please try again.');
                    },
                    (event) => {
                        // Progress
                        this.uploadProgress = event.detail.progress;
                    }
                );
            },
            
            // Drag and drop methods
            handleDragStart(index, event) {
                // Only allow dragging items that have media
                if (!this.hasMedia(index)) {
                    event.preventDefault();
                    return;
                }
                this.draggedIndex = index;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', index);
                // Add slight delay to allow drag image to form
                setTimeout(() => {
                    event.target.style.opacity = '0.5';
                }, 0);
            },
            
            handleDragEnd(event) {
                event.target.style.opacity = '1';
                this.draggedIndex = null;
                this.dragOverIndex = null;
            },
            
            handleDragOver(index, event) {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
                if (this.draggedIndex !== null && this.draggedIndex !== index) {
                    this.dragOverIndex = index;
                }
            },
            
            handleDragLeave(index, event) {
                // Only clear if we're actually leaving the element
                if (!event.currentTarget.contains(event.relatedTarget)) {
                    if (this.dragOverIndex === index) {
                        this.dragOverIndex = null;
                    }
                }
            },
            
            handleDrop(index, event) {
                event.preventDefault();
                
                if (this.draggedIndex === null || this.draggedIndex === index) {
                    this.dragOverIndex = null;
                    return;
                }
                
                // Swap the media items
                this.swapMedia(this.draggedIndex, index);
                
                this.draggedIndex = null;
                this.dragOverIndex = null;
            },
            
            swapMedia(fromIndex, toIndex) {
                // Ensure both indices have objects
                if (!this.mediaItems[fromIndex]) {
                    this.mediaItems[fromIndex] = { type: 'image', url: null, thumbnailUrl: null };
                }
                if (!this.mediaItems[toIndex]) {
                    this.mediaItems[toIndex] = { type: 'image', url: null, thumbnailUrl: null };
                }
                
                // Swap the items
                const temp = { ...this.mediaItems[fromIndex] };
                this.mediaItems[fromIndex] = { ...this.mediaItems[toIndex] };
                this.mediaItems[toIndex] = temp;
                
                // Clear IDs since they're moving to new slot positions
                delete this.mediaItems[fromIndex].id;
                delete this.mediaItems[toIndex].id;
                
                this.updateState();
            }
        }"
        class="space-y-4"
    >
        {{-- Validation Warning --}}
        <div 
            x-show="!isValid()"
            style="padding: 12px 16px; background: #FEF3C7; border: 1px solid #F59E0B; border-radius: 8px; display: flex; align-items: center; gap: 12px;"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px; color: #D97706; flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <div style="flex: 1;">
                <p style="font-size: 14px; font-weight: 600; color: #92400E; margin: 0;">Media Required</p>
                <p style="font-size: 13px; color: #B45309; margin: 2px 0 0;">
                    <span x-text="getFilledCount()"></span> of <span x-text="slotCount"></span> slots filled. 
                    Please add media to all <span x-text="slotCount"></span> slots before saving.
                </p>
            </div>
        </div>

        {{-- Visual Grid Layout using flexbox for reliability --}}
        <div style="display: flex; flex-wrap: wrap; gap: 4px; padding: 16px; background: #111827; border-radius: 8px; position: relative;">
            {{-- Loading Overlay --}}
            <div 
                wire:loading.flex
                wire:target="data.layout"
                style="position: absolute; inset: 0; background: rgba(17, 24, 39, 0.85); backdrop-filter: blur(2px); display: none; align-items: center; justify-content: center; border-radius: 8px; z-index: 50;"
            >
                <div style="text-align: center;">
                    <svg class="animate-spin" style="width: 40px; height: 40px; color: #F59E0B; margin: 0 auto;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <div style="margin-top: 12px; color: #F59E0B; font-size: 14px; font-weight: 500;">Updating layout...</div>
                </div>
            </div>
            
            @foreach($slots as $index => $slot)
                <div 
                    style="width: calc({{ ($slot['cols'] / 12) * 100 }}% - 3px); aspect-ratio: 16/9; cursor: pointer;"
                    @click="openSlot({{ $index }})"
                    class="group relative"
                    :class="{
                        'ring-2 ring-primary-500 ring-offset-2 ring-offset-gray-900': dragOverIndex === {{ $index }},
                        'opacity-50': draggedIndex === {{ $index }}
                    }"
                    draggable="true"
                    @dragstart="handleDragStart({{ $index }}, $event)"
                    @dragend="handleDragEnd($event)"
                    @dragover="handleDragOver({{ $index }}, $event)"
                    @dragleave="handleDragLeave({{ $index }}, $event)"
                    @drop="handleDrop({{ $index }}, $event)"
                >
                    {{-- Empty State --}}
                    <div 
                        x-show="!hasMedia({{ $index }})"
                        style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; border: 2px dashed #4B5563; border-radius: 8px;"
                        class="hover:border-primary-500 hover:bg-gray-800 transition-all"
                    >
                        <div class="text-center">
                            <div style="font-size: 24px; font-weight: bold; color: #6B7280;" class="group-hover:text-primary-400">{{ $slot['label'] }}</div>
                            <div style="font-size: 11px; color: #4B5563;" class="group-hover:text-gray-400">Click to add</div>
                        </div>
                    </div>
                    
                    {{-- Filled State with Preview --}}
                    <div 
                        x-show="hasMedia({{ $index }})"
                        style="width: 100%; height: 100%; border-radius: 8px; overflow: hidden; position: relative;"
                    >
                        <img 
                            x-bind:src="getPreviewUrl({{ $index }})"
                            style="width: 100%; height: 100%; object-fit: cover;"
                            onerror="this.style.display='none'"
                        />
                        <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; gap: 8px; opacity: 0;" class="group-hover:opacity-100 transition-opacity">
                            <button 
                                type="button"
                                @click.stop="openSlot({{ $index }})"
                                style="padding: 8px; background: rgba(255,255,255,0.2); border-radius: 8px;"
                                class="hover:bg-white/30"
                            >
                                <x-heroicon-o-pencil class="w-5 h-5 text-white" />
                            </button>
                            <button 
                                type="button"
                                @click.stop="removeMedia({{ $index }})"
                                style="padding: 8px; background: rgba(239,68,68,0.5); border-radius: 8px;"
                                class="hover:bg-red-500/70"
                            >
                                <x-heroicon-o-trash class="w-5 h-5 text-white" />
                            </button>
                        </div>
                        <div style="position: absolute; top: 8px; left: 8px; background: rgba(0,0,0,0.7); color: white; font-size: 12px; padding: 4px 8px; border-radius: 4px; display: flex; align-items: center; gap: 6px;">
                            {{ $slot['label'] }}
                            {{-- YouTube badge --}}
                            <template x-if="isYouTube({{ $index }})">
                                <svg style="width: 16px; height: 16px; color: #FF0000;" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </template>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Progress indicator --}}
        <div style="font-size: 13px; color: #9CA3AF; text-align: center;">
            <span x-text="getFilledCount()"></span> / <span x-text="slotCount"></span> media slots filled
            <span style="margin-left: 12px; color: #6B7280;">•</span>
            <span style="margin-left: 12px; color: #6B7280;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 14px; height: 14px; display: inline-block; vertical-align: -2px; margin-right: 4px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
                Drag to reorder
            </span>
        </div>

        {{-- Modal for editing slot --}}
        <template x-teleport="body">
            <div
                x-show="activeSlot !== null"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click.self="closeSlot()"
                @keydown.escape.window="closeSlot()"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                style="background-color: rgba(0, 0, 0, 0.7);"
                x-cloak
            >
                <div 
                    class="bg-gray-900 rounded-xl shadow-2xl w-full max-w-lg relative"
                    style="background-color: #111827;"
                    @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                >
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding: 24px 24px 0 24px;">
                    <h3 style="font-size: 20px; font-weight: 600; color: white;">
                        Edit Slot <span x-text="activeSlot !== null ? activeSlot + 1 : ''"></span>
                    </h3>
                    <button 
                        type="button"
                        @click="closeSlot()"
                        style="color: #9CA3AF; background: none; border: none; cursor: pointer; padding: 8px; margin: -8px;"
                        class="hover:text-white transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 24px; padding: 0 24px 24px 24px;">
                    {{-- Type Select --}}
                    <div>
                        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Type
                            </span>
                        </label>
                        <div class="relative mt-2">
                            <select 
                                x-model="mediaItems[activeSlot] ? mediaItems[activeSlot].type : 'image'"
                                @change="setMedia(activeSlot, 'type', $event.target.value)"
                                class="fi-select-input block w-full border-none bg-white py-1.5 pe-8 ps-3 text-base text-gray-950 transition duration-75 focus:ring-2 focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:bg-white/5 dark:text-white dark:focus:ring-primary-500 sm:text-sm sm:leading-6 [&_optgroup]:bg-white [&_optgroup]:dark:bg-gray-900 [&_option]:bg-white [&_option]:dark:bg-gray-900 rounded-lg shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20"
                                style="appearance: none; padding-right: 2.5rem; background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em;"
                            >
                                <option value="image">Image</option>
                                <option value="video">Video (MP4/WebM)</option>
                                <option value="youtube">YouTube Video</option>
                            </select>
                        </div>
                        <p x-show="getMedia(activeSlot).type === 'youtube'" style="font-size: 12px; color: #9CA3AF; margin-top: 8px;">
                            Paste a YouTube URL below (e.g., youtube.com/watch?v=... or youtu.be/...)
                        </p>
                    </div>

                    {{-- File Upload (hidden for YouTube) --}}
                    <div x-show="getMedia(activeSlot).type !== 'youtube'">
                        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Upload File
                            </span>
                        </label>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            {{-- Drag and drop area --}}
                            <div 
                                x-show="!uploading && !getMedia(activeSlot).url"
                                style="border: 2px dashed #374151; border-radius: 12px; padding: 32px; text-align: center; cursor: pointer; transition: all 0.2s;"
                                class="hover:border-primary-500 hover:bg-gray-800/50 group"
                                @click="$refs.fileInput.click()"
                                @dragover.prevent="$event.target.style.borderColor = '#F59E0B'"
                                @dragleave.prevent="$event.target.style.borderColor = '#374151'"
                                @drop.prevent="
                                    $event.target.style.borderColor = '#374151';
                                    if ($event.dataTransfer.files.length) {
                                        uploadFile($event.dataTransfer.files[0]);
                                    }
                                "
                            >
                                <div class="bg-gray-800 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-gray-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 32px; height: 32px; color: #9CA3AF;" class="group-hover:text-white transition-colors">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <p style="font-size: 15px; color: #D1D5DB; margin: 0; font-weight: 500;">
                                    <span style="color: #F59E0B;">Click to upload</span> or drag and drop
                                </p>
                                <p style="font-size: 13px; color: #6B7280; margin: 8px 0 0;">
                                    PNG, JPG, GIF, MP4, WEBM up to 25MB
                                </p>
                            </div>
                            
                            {{-- Hidden file input --}}
                            <input 
                                x-ref="fileInput"
                                type="file"
                                accept="image/*,video/*"
                                @change="uploadFile($event.target.files[0])"
                                style="display: none;"
                            />
                            
                            {{-- Upload progress --}}
                            <div x-show="uploading" style="padding: 32px; background: #1F2937; border-radius: 12px; text-align: center;">
                                <svg class="animate-spin" style="width: 40px; height: 40px; color: #F59E0B; margin: 0 auto 16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p style="font-size: 15px; color: #F59E0B; margin: 0; font-weight: 500;">Uploading...</p>
                                <div style="margin-top: 16px; background: #374151; border-radius: 4px; height: 8px; overflow: hidden;">
                                    <div 
                                        style="background: #F59E0B; height: 100%; transition: width 0.2s;"
                                        x-bind:style="'width: ' + uploadProgress + '%'"
                                    ></div>
                                </div>
                            </div>

                            {{-- Current file preview --}}
                            <template x-if="getMedia(activeSlot).url && !uploading">
                                <div style="display: flex; align-items: center; gap: 16px; padding: 16px; background: #1F2937; border-radius: 12px; border: 1px solid #374151;">
                                    <img 
                                        x-bind:src="getPreviewUrl(activeSlot)"
                                        style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                                        onerror="this.style.background='#374151'"
                                    />
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-size: 15px; color: white; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin: 0;" x-text="getMedia(activeSlot).url?.split('/').pop() || 'No file'"></p>
                                        <p style="font-size: 13px; color: #9CA3AF; margin: 4px 0 0; text-transform: capitalize;" x-text="getMedia(activeSlot).type"></p>
                                    </div>
                                    <button 
                                        type="button"
                                        @click="removeMedia(activeSlot)"
                                        style="padding: 10px; color: #F87171; background: rgba(248, 113, 113, 0.1); border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s;"
                                        class="hover:bg-red-500/20 hover:text-red-400"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            {{-- URL input as alternative --}}
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="flex: 1; height: 1px; background: #374151;"></div>
                                <span style="font-size: 13px; color: #6B7280; font-weight: 500;">or enter URL</span>
                                <div style="flex: 1; height: 1px; background: #374151;"></div>
                            </div>
                            <input 
                                type="text"
                                placeholder="https://example.com/image.jpg"
                                x-bind:value="getMedia(activeSlot).url || ''"
                                @input="setMedia(activeSlot, 'url', $event.target.value)"
                                class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500 dark:focus:ring-primary-500 sm:text-sm sm:leading-6 bg-white shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20 rounded-lg px-3"
                            />
                        </div>
                    </div>

                    {{-- YouTube URL Input (shown only for YouTube type) --}}
                    <div x-show="getMedia(activeSlot).type === 'youtube'">
                        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                YouTube URL
                            </span>
                        </label>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            <input 
                                type="text"
                                placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/..."
                                x-bind:value="getMedia(activeSlot).url || ''"
                                @input="setMedia(activeSlot, 'url', $event.target.value)"
                                class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500 dark:focus:ring-primary-500 sm:text-sm sm:leading-6 bg-white shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20 rounded-lg px-3"
                            />
                            
                            {{-- YouTube Preview --}}
                            <template x-if="getMedia(activeSlot).url && extractYouTubeId(getMedia(activeSlot).url)">
                                <div style="display: flex; flex-direction: column; gap: 12px;">
                                    <div style="position: relative; border-radius: 12px; overflow: hidden; aspect-ratio: 16/9; background: #1F2937;">
                                        <img 
                                            x-bind:src="getYouTubeThumbnail(activeSlot)"
                                            style="width: 100%; height: 100%; object-fit: cover;"
                                            onerror="this.style.display='none'"
                                        />
                                        {{-- YouTube Play Icon Overlay --}}
                                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
                                            <div style="width: 68px; height: 48px; background: rgba(255, 0, 0, 0.9); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                                <svg style="width: 24px; height: 24px; color: white; margin-left: 4px;" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #1F2937; border-radius: 8px; border: 1px solid #374151;">
                                        <svg style="width: 24px; height: 24px; color: #FF0000; flex-shrink: 0;" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        <div style="flex: 1; min-width: 0;">
                                            <p style="font-size: 14px; color: white; font-weight: 500; margin: 0;">YouTube Video</p>
                                            <p style="font-size: 12px; color: #9CA3AF; margin: 2px 0 0;">
                                                ID: <span x-text="extractYouTubeId(getMedia(activeSlot).url)"></span>
                                            </p>
                                        </div>
                                        <button 
                                            type="button"
                                            @click="removeMedia(activeSlot)"
                                            style="padding: 8px; color: #F87171; background: rgba(248, 113, 113, 0.1); border-radius: 6px; border: none; cursor: pointer;"
                                            class="hover:bg-red-500/20"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            
                            {{-- Invalid YouTube URL warning --}}
                            <template x-if="getMedia(activeSlot).url && !extractYouTubeId(getMedia(activeSlot).url)">
                                <div style="padding: 12px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; color: #F87171; flex-shrink: 0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                    <p style="font-size: 13px; color: #F87171; margin: 0;">Invalid YouTube URL. Please enter a valid YouTube link.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Thumbnail (for videos and YouTube) --}}
                    <div x-show="getMedia(activeSlot).type === 'video' || getMedia(activeSlot).type === 'youtube'">
                        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                                Thumbnail URL <span x-show="getMedia(activeSlot).type === 'youtube'" style="font-weight: normal; color: #9CA3AF;">(auto-fetched from YouTube)</span>
                            </span>
                        </label>
                        <div class="mt-2">
                            <input 
                                type="text"
                                placeholder="https://example.com/thumbnail.jpg"
                                x-bind:value="getMedia(activeSlot).thumbnailUrl || ''"
                                @input="setMedia(activeSlot, 'thumbnailUrl', $event.target.value)"
                                class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500 dark:focus:ring-primary-500 sm:text-sm sm:leading-6 bg-white shadow-sm ring-1 ring-gray-950/10 dark:ring-white/20 rounded-lg px-3"
                            />
                        </div>
                    </div>
                </div>

                <div style="margin-top: 8px; display: flex; justify-content: flex-end; gap: 12px; padding: 0 24px 24px 24px;">
                    <button 
                        type="button"
                        @click="removeMedia(activeSlot); closeSlot();"
                        style="padding: 12px 20px; font-size: 15px; font-weight: 500; color: #F87171; background: none; border: none; cursor: pointer; border-radius: 8px;"
                        class="hover:bg-red-500/10 transition-colors"
                    >
                        Remove
                    </button>
                    <button 
                        type="button"
                        @click="closeSlot()"
                        style="padding: 12px 24px; font-size: 15px; font-weight: 600; background: #F59E0B; color: white; border: none; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);"
                        class="hover:bg-amber-400 hover:shadow-lg transition-all transform active:scale-95"
                    >
                        Done
                    </button>
                </div>
            </div>
        </div>
        </template>
    </div>
</x-dynamic-component>
