@php
    $record = $getRecord();
    $projectId = $record?->id;
    $section = $record?->section ?? 'corporate';
@endphp

@if($projectId)
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-content p-6">
            @livewire('project-media-table', ['projectId' => $projectId, 'section' => $section], key('project-media-' . $projectId))
        </div>
    </div>
@else
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-content p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Save the project first to manage media items.
            </p>
        </div>
    </div>
@endif
