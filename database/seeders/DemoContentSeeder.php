<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        // Site Settings
        SiteSetting::updateOrCreate(['id' => 1], [
            'site_title' => 'Laurensius Dimas - Creative Director & Motion Designer',
            'site_description' => 'Award-winning creative director specializing in motion design, visual storytelling, and brand experiences.',
            'hero_headline' => 'Laurensius Dimas',
            'hero_subheadline' => 'Creative Director & Motion Designer',
            'bio_short' => 'I craft visual stories that move. With over 10 years of experience in motion design and creative direction, I help brands tell their stories through compelling visuals.',
            'bio_long' => '<p>I\'m a creative director and motion designer based in Jakarta, Indonesia. My passion lies in creating visual experiences that not only look beautiful but also communicate effectively.</p><p>I\'ve had the privilege of working with global brands, startups, and agencies to bring their visions to life through motion graphics, brand identity, and creative strategy.</p>',
            'email' => 'hello@laurensiusdimas.com',
            'social_links' => [
                ['platform' => 'Instagram', 'url' => 'https://instagram.com/laurensiusdimas', 'icon' => 'heroicon-o-camera'],
                ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/laurensiusdimas', 'icon' => 'heroicon-o-briefcase'],
                ['platform' => 'Behance', 'url' => 'https://behance.net/laurensiusdimas', 'icon' => 'heroicon-o-paint-brush'],
                ['platform' => 'Email', 'url' => 'mailto:hello@laurensiusdimas.com', 'icon' => 'heroicon-o-envelope'],
            ],
        ]);

        // Projects
        $projects = [
            // Layout: three_two (5-up)
            [
                'title' => 'Hardy Motor',
                'slug' => 'hardy-motor',
                'subtitle' => 'Freelance Project | CGI 3D Compositing',
                'layout' => 'three_two',
                'description' => '<p>Five-shot mosaic demonstrating the 3/2 layout.</p>',
                'is_visible' => true,
                'sort_order' => 1,
                'meta_title' => 'Hardy Motor - 3/2 Layout',
                'meta_description' => 'Five-shot mosaic layout example',
                'media_items' => [
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=900&h=500&fit=crop'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=900&h=500&fit=crop'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=900&h=500&fit=crop&sat=-20'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=1200&h=500&fit=crop&sat=-30'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=1200&h=500&fit=crop&sat=-40'],
                ],
            ],
            // Layout: three_three (6-up)
            [
                'title' => 'Citroen DVC',
                'slug' => 'citroen-dvc',
                'subtitle' => 'Freelance Project | Motion Graphic, Compositing',
                'layout' => 'three_three',
                'description' => '<p>Six-shot mosaic demonstrating the 3/3 layout.</p>',
                'is_visible' => true,
                'sort_order' => 2,
                'meta_title' => 'Citroen DVC - 3/3 Layout',
                'meta_description' => 'Six-shot mosaic layout example',
                'media_items' => [
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=900&h=500&fit=crop&sat=-10'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=900&h=500&fit=crop'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=900&h=500&fit=crop&sat=-5'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=900&h=500&fit=crop&sat=-25'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=900&h=500&fit=crop&sat=-35'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=900&h=500&fit=crop&sat=-45'],
                ],
            ],
            // Layout: two (2-up split)
            [
                'title' => 'Virtual Wedding Background',
                'slug' => 'virtual-wedding-background',
                'subtitle' => 'Freelance Project | CGI 3D Animation',
                'layout' => 'two',
                'description' => '<p>Two-wide split to show the 2-up layout.</p>',
                'is_visible' => true,
                'sort_order' => 3,
                'meta_title' => 'Virtual Wedding Background - 2-up Layout',
                'meta_description' => 'Two-up split layout example',
                'media_items' => [
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503389152951-9f343605f61e?w=1200&h=600&fit=crop&sat=-10'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503389152951-9f343605f61e?w=1200&h=600&fit=crop&sat=-20'],
                ],
            ],
            // Layout: four_one (5-up variation)
            [
                'title' => 'Order Sekarang',
                'slug' => 'order-sekarang',
                'subtitle' => 'Freelance Project | Motion Graphic, Compositing',
                'layout' => 'four_one',
                'description' => '<p>Four top, one bottom layout.</p>',
                'is_visible' => true,
                'sort_order' => 4,
                'meta_title' => 'Order Sekarang - 4/1 Layout',
                'meta_description' => 'Four-one layout example',
                'media_items' => [
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=600&h=400&fit=crop&sat=-15'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=600&h=400&fit=crop&sat=-25'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=600&h=400&fit=crop&sat=-35'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=600&h=400&fit=crop&sat=-45'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=1200&h=600&fit=crop&sat=-55'],
                ],
            ],
            // Layout: four_two (6-up variation)
            [
                'title' => 'Project Alpha',
                'slug' => 'project-alpha',
                'subtitle' => 'Freelance Project | 3D Design',
                'layout' => 'four_two',
                'description' => '<p>Four top, two bottom layout.</p>',
                'is_visible' => true,
                'sort_order' => 5,
                'meta_title' => 'Project Alpha - 4/2 Layout',
                'meta_description' => 'Four-two layout example',
                'media_items' => [
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=600&h=400&fit=crop&sat=-15'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=600&h=400&fit=crop&sat=-25'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=600&h=400&fit=crop&sat=-35'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=600&h=400&fit=crop&sat=-45'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=900&h=500&fit=crop&sat=-55'],
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=900&h=500&fit=crop&sat=-65'],
                ],
            ],
            // Layout: single (hero)
            [
                'title' => 'Project Beta',
                'slug' => 'project-beta',
                'subtitle' => 'Freelance Project | Animation',
                'layout' => 'single',
                'description' => '<p>Single hero layout.</p>',
                'is_visible' => true,
                'sort_order' => 6,
                'meta_title' => 'Project Beta - Single Layout',
                'meta_description' => 'Single layout example',
                'media_items' => [
                    ['type' => 'image', 'url' => 'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?w=1200&h=650&fit=crop&sat=-15'],
                ],
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        // Clients
        $clients = [
            ['name' => 'Nike', 'logo_url' => 'clients/nike.png', 'website_url' => 'https://nike.com', 'sort_order' => 1, 'is_visible' => true],
            ['name' => 'Apple', 'logo_url' => 'clients/apple.png', 'website_url' => 'https://apple.com', 'sort_order' => 2, 'is_visible' => true],
            ['name' => 'Google', 'logo_url' => 'clients/google.png', 'website_url' => 'https://google.com', 'sort_order' => 3, 'is_visible' => true],
            ['name' => 'Spotify', 'logo_url' => 'clients/spotify.png', 'website_url' => 'https://spotify.com', 'sort_order' => 4, 'is_visible' => true],
            ['name' => 'Netflix', 'logo_url' => 'clients/netflix.png', 'website_url' => 'https://netflix.com', 'sort_order' => 5, 'is_visible' => true],
            ['name' => 'Adidas', 'logo_url' => 'clients/adidas.png', 'website_url' => 'https://adidas.com', 'sort_order' => 6, 'is_visible' => true],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }

        $this->command->info('Demo content seeded successfully!');
    }
}
