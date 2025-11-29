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
        
        // Curated projects (main portfolio section)
        $curatedProjects = Project::where('is_visible', true)
            ->where('section', 'curated')
            ->orderBy('sort_order')
            ->get();
        
        // Corporate projects - grouped by client
        $corporateProjects = Project::where('section', 'corporate')
            ->where('is_visible', true)
            ->whereNotNull('client_id') // Only show if connected to client
            ->with(['client', 'corporateMedia'])
            ->orderBy('sort_order')
            ->get()
            ->groupBy('client_id'); // Group by client
        
        // Older projects (archive section)
        $olderProjects = Project::where('is_visible', true)
            ->where('section', 'older')
            ->orderBy('sort_order')
            ->get();
        
        $clients = Client::where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.home', [
                'settings' => $settings,
                'curatedProjects' => $curatedProjects,
                'olderProjects' => $olderProjects,
                'clients' => $clients,
                'corporateProjects' => $corporateProjects,
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
