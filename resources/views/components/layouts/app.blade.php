<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $siteSettings = \App\Models\SiteSetting::current();
        $metaTitle = $title ?? $siteSettings->site_title ?? config('app.name');
        $metaDescription = $description ?? $siteSettings->site_description ?? 'Portfolio';
        $metaOgImage = $ogImage
            ?? ($siteSettings->og_image_url
                ? (str_starts_with($siteSettings->og_image_url, 'http')
                    ? $siteSettings->og_image_url
                    : \Illuminate\Support\Facades\Storage::url($siteSettings->og_image_url))
                : asset('images/og-default.jpg'));
        $favicon = $siteSettings->favicon_url
            ? (str_starts_with($siteSettings->favicon_url, 'http')
                ? $siteSettings->favicon_url
                : \Illuminate\Support\Facades\Storage::url($siteSettings->favicon_url))
            : null;
    @endphp

    <title>{{ $metaTitle }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $metaOgImage }}">
    <meta name="theme-color" content="#0b0c10">

    @if($favicon)
        <link rel="icon" type="image/png" href="{{ $favicon }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Grotesk:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-neutral-950 text-white font-body">
    {{ $slot }}
    
    @livewireScripts
</body>
</html>
