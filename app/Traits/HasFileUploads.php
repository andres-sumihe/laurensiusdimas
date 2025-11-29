<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * Trait for models that have file upload fields.
 * Automatically cleans up old files when they are replaced or when records are deleted.
 */
trait HasFileUploads
{
    /**
     * Boot the trait and register model events.
     */
    public static function bootHasFileUploads(): void
    {
        // Clean up old files when updating
        static::updating(function ($model) {
            foreach ($model->getFileUploadFields() as $field) {
                $originalValue = $model->getOriginal($field);
                $newValue = $model->getAttribute($field);
                
                // If the field value changed and original was a local file, delete it
                if ($originalValue && $originalValue !== $newValue) {
                    self::deleteFileIfLocal($originalValue);
                }
            }
        });

        // Clean up files when deleting
        static::deleting(function ($model) {
            foreach ($model->getFileUploadFields() as $field) {
                $value = $model->getAttribute($field);
                if ($value) {
                    self::deleteFileIfLocal($value);
                }
            }
        });
    }

    /**
     * Get the list of file upload field names.
     * Override this in your model to specify which fields contain file paths.
     */
    abstract public function getFileUploadFields(): array;

    /**
     * Delete a file if it's a local storage path (not an external URL).
     */
    protected static function deleteFileIfLocal(string $path): void
    {
        // Skip external URLs
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        // Try to delete from public disk
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
