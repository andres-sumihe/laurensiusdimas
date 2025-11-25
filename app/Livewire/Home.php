<?php

namespace App\Livewire;

use App\Models\SiteSetting;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public function render()
    {
        $settings = SiteSetting::current();
        $projects = Project::where('is_visible', true)
            ->orderBy('sort_order')
            ->get();
        $clients = Client::where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.home', [
                'settings' => $settings,
                'projects' => $projects,
                'clients' => $clients,
            ])
            ->layoutData([
                'title' => $settings->site_title ?? 'Laurensius Dimas',
                'description' => $settings->site_description ?? 'Portfolio',
                'ogImage' => $settings->og_image_url
                    ? (str_starts_with($settings->og_image_url, 'http')
                        ? $settings->og_image_url
                        : Storage::url($settings->og_image_url))
                    : null,
            ]);
    }
}
