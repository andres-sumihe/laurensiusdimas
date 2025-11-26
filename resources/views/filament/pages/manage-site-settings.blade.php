<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </x-filament-panels::form>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const wrapper = document.querySelector('[data-hero-upload]');
            if (!wrapper) return;

            const fileInput = wrapper.querySelector('input[type=file]');
            if (!fileInput) return;

            const MAX_BYTES = 25 * 1024 * 1024; // 25MB
            const SERVER_MAX_BYTES = {{ $phpUploadMaxKb ?? 0 }} * 1024 || MAX_BYTES;

            fileInput.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                // Save the original filename for error messaging
                window.heroUploadFilename = file.name;

                // Validate type
                const allowedTypes = ['image/gif', 'video/mp4'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please use GIF or MP4.');
                    fileInput.value = '';
                    return;
                }

                // Validate size and stop upload if bigger than server limit or app limit
                if (file.size > SERVER_MAX_BYTES) {
                    const serverMB = (SERVER_MAX_BYTES / (1024*1024)).toFixed(2);
                    alert('File is too large for this server configuration. Maximum allowed upload is ' + serverMB + 'MB. Your file is ' + (file.size / (1024*1024)).toFixed(2) + 'MB.');
                    fileInput.value = '';
                    showInlineError('File is too large for server (max ' + serverMB + "MB).");
                    return;
                }
                if (file.size > MAX_BYTES) {
                    alert('File is too large. Maximum allowed size is 25MB.');
                    fileInput.value = '';
                }
            });

            function showInlineError(message) {
                // find or create a helper element to show error under the upload field
                const existing = wrapper.querySelector('.hero-upload-error');
                if (existing) {
                    existing.textContent = message;
                    existing.style.display = 'block';
                    return;
                }
                const p = document.createElement('p');
                p.className = 'hero-upload-error text-sm';
                p.style.color = '#F87171';
                p.style.marginTop = '8px';
                p.textContent = message;
                wrapper.appendChild(p);
            }

            // Remove Filament's inline upload error messages if present
            function clearFilamentError() {
                const errorElements = wrapper.querySelectorAll('.filament-forms-field-wrapper__error, .filament-forms-field-wrapper__message');
                errorElements.forEach((el) => {
                    try { el.remove(); } catch (e) { /* noop */ }
                });
            }

            // Listen for Livewire upload errors and show friendly messages
            window.addEventListener('livewire-upload-error', (event) => {
                try {
                    const name = event.detail?.name || '';
                    const message = event.detail?.message || '';
                    if (name && name.includes('hero_video_url')) {
                        // Map common server-side errors to friendly messages
                        let friendlyMsg;
                        if (message.toLowerCase().includes('exceed') || message.toLowerCase().includes('size')) {
                            friendlyMsg = 'Upload failed: File too large ("' + (window.heroUploadFilename || 'file') + '"). Maximum allowed size is 25MB.';
                        } else {
                            friendlyMsg = 'Upload failed for "' + (window.heroUploadFilename || 'file') + '": ' + (message || 'Unknown error.');
                        }
                        // Replace the native Filament message with our friendly message
                        clearFilamentError();
                        showInlineError(friendlyMsg);
                        // Clear the file input to allow re-selection
                        fileInput.value = '';
                    }
                } catch (err) {
                    console.error(err);
                }
            });
        });
    </script>
</x-filament-panels::page>
