@php
    use Illuminate\Support\Facades\Storage;
    
    $slots = $getLayoutSlots();
    $mediaItems = $getState() ?? [];
    
    // Ensure mediaItems is an array indexed from 0
    $mediaItems = array_values((array) $mediaItems);
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div 
        x-data="{
            activeSlot: null,
            mediaItems: @js($mediaItems),
            slots: @js($slots),
            
            openSlot(index) {
                this.activeSlot = index;
            },
            
            closeSlot() {
                this.activeSlot = null;
            },
            
            getMedia(index) {
                return this.mediaItems[index] || { type: 'image', url: null, thumbnailUrl: null };
            },
            
            setMedia(index, field, value) {
                if (!this.mediaItems[index]) {
                    this.mediaItems[index] = { type: 'image', url: null, thumbnailUrl: null };
                }
                this.mediaItems[index][field] = value;
                this.updateState();
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
            }
        }"
        class="space-y-4"
    >
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
                        <div style="position: absolute; top: 8px; left: 8px; background: rgba(0,0,0,0.7); color: white; font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                            {{ $slot['label'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Modal for editing slot --}}
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
            style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center;"
            x-bind:style="activeSlot !== null ? 'display: flex' : 'display: none'"
        >
            <div 
                style="background: #111827; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); width: 100%; max-width: 420px; margin: 16px; padding: 24px;"
                @click.stop
            >
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; color: white;">
                        Edit Slot <span x-text="activeSlot !== null ? activeSlot + 1 : ''"></span>
                    </h3>
                    <button 
                        type="button"
                        @click="closeSlot()"
                        style="color: #9CA3AF; background: none; border: none; cursor: pointer; padding: 4px;"
                        onmouseover="this.style.color='white'"
                        onmouseout="this.style.color='#9CA3AF'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    {{-- Type Select --}}
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #D1D5DB; margin-bottom: 8px;">Type</label>
                        <select 
                            x-model="mediaItems[activeSlot] ? mediaItems[activeSlot].type : 'image'"
                            @change="setMedia(activeSlot, 'type', $event.target.value)"
                            style="width: 100%; padding: 10px 12px; border-radius: 8px; background: #1F2937; border: 1px solid #374151; color: white; font-size: 14px;"
                        >
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                    </div>

                    {{-- File Upload --}}
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #D1D5DB; margin-bottom: 8px;">File</label>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            {{-- Current file preview --}}
                            <template x-if="getMedia(activeSlot).url">
                                <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #1F2937; border-radius: 8px;">
                                    <img 
                                        x-bind:src="getPreviewUrl(activeSlot)"
                                        style="width: 64px; height: 48px; object-fit: cover; border-radius: 4px;"
                                        onerror="this.style.background='#374151'"
                                    />
                                    <div style="flex: 1; min-width: 0;">
                                        <p style="font-size: 14px; color: white; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="getMedia(activeSlot).url?.split('/').pop() || 'No file'"></p>
                                        <p style="font-size: 12px; color: #9CA3AF;" x-text="getMedia(activeSlot).type"></p>
                                    </div>
                                </div>
                            </template>

                            {{-- File input --}}
                            <input 
                                type="file"
                                accept="image/*,video/*"
                                @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        $wire.upload('mediaUpload', file, 
                                            (uploadedFilename) => {
                                                setMedia(activeSlot, 'url', uploadedFilename);
                                            },
                                            () => {
                                                console.error('Upload failed');
                                            }
                                        );
                                    }
                                "
                                style="width: 100%; font-size: 14px; color: #9CA3AF;"
                            />
                            <p style="font-size: 12px; color: #6B7280;">Or enter URL directly:</p>
                            <input 
                                type="text"
                                placeholder="https://example.com/image.jpg"
                                x-bind:value="getMedia(activeSlot).url || ''"
                                @input="setMedia(activeSlot, 'url', $event.target.value)"
                                style="width: 100%; padding: 10px 12px; border-radius: 8px; background: #1F2937; border: 1px solid #374151; color: white; font-size: 14px;"
                            />
                        </div>
                    </div>

                    {{-- Thumbnail (for videos) --}}
                    <div x-show="getMedia(activeSlot).type === 'video'">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #D1D5DB; margin-bottom: 8px;">Thumbnail URL (optional)</label>
                        <input 
                            type="text"
                            placeholder="https://example.com/thumbnail.jpg"
                            x-bind:value="getMedia(activeSlot).thumbnailUrl || ''"
                            @input="setMedia(activeSlot, 'thumbnailUrl', $event.target.value)"
                            style="width: 100%; padding: 10px 12px; border-radius: 8px; background: #1F2937; border: 1px solid #374151; color: white; font-size: 14px;"
                        />
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button 
                        type="button"
                        @click="removeMedia(activeSlot); closeSlot();"
                        style="padding: 8px 16px; font-size: 14px; font-weight: 500; color: #F87171; background: none; border: none; cursor: pointer;"
                    >
                        Remove
                    </button>
                    <button 
                        type="button"
                        @click="closeSlot()"
                        style="padding: 8px 16px; font-size: 14px; font-weight: 500; background: #F59E0B; color: white; border: none; border-radius: 8px; cursor: pointer;"
                    >
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
