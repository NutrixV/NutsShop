<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi min-h-screen',
        'dark' => filament()->hasDarkModeForced(),
    ])
>
    <head>
        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::head.start') }}

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        
        <!-- CORS settings -->
        <meta http-equiv="Cross-Origin-Opener-Policy" content="same-origin" />
        <meta http-equiv="Cross-Origin-Resource-Policy" content="same-site" />
        <meta http-equiv="Cross-Origin-Embedder-Policy" content="require-corp" />

        @foreach (filament()->getMeta() as $tag)
            {{ $tag }}
        @endforeach

        @if ($favicon = filament()->getFavicon())
            <link rel="icon" href="{{ $favicon }}" />
        @endif

        <title>{{ filament()->getBrandName() }}{{ $title ? ' - ' . $title : null }}</title>

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::styles.before') }}

        <style>
            [x-cloak=''] {
                display: none !important;
            }
            
            /* Force image loading to use CORS proxy */
            img[src*="/storage/"] {
                /* Will be handled by our JavaScript CORS fix */
                image-rendering: auto;
            }
        </style>

        @filamentStyles

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::styles.after') }}

        <!-- CORS Fix script -->
        <script src="{{ asset('js/cors-fix.js') }}"></script>

        <script>
            // Fix CORS in Filament directly
            document.addEventListener('DOMContentLoaded', function() {
                const isLocalhost = window.location.host.includes('localhost');
                const is127 = window.location.host.includes('127.0.0.1');
                
                // Fix all storage URLs
                if (typeof window.Livewire !== 'undefined') {
                    const originalUploadImage = window.Livewire._instance.uploadImageUsing;
                    
                    if (originalUploadImage) {
                        window.Livewire._instance.uploadImageUsing = function(callback) {
                            return function(image, url) {
                                // Fix URL before passing to original handler
                                let fixedUrl = url;
                                
                                if (isLocalhost && url.includes('127.0.0.1')) {
                                    fixedUrl = url.replace('127.0.0.1', 'localhost');
                                } else if (is127 && url.includes('localhost')) {
                                    fixedUrl = url.replace('localhost', '127.0.0.1');
                                }
                                
                                return originalUploadImage(callback)(image, fixedUrl);
                            };
                        };
                        
                        console.log('CORS Fix: Patched Livewire uploadImageUsing');
                    }
                }
            });
        </script>

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::head.end') }}
    </head>

    <body class="fi-body min-h-screen bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white">
        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::body.start') }}

        {{ $slot }}

        @livewireScriptConfig

        <script>
            window.filamentData = @json(filament()->getScriptData());
        </script>

        @filamentScripts

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::scripts.before') }}

        {{ $scripts ?? '' }}

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::scripts.after') }}

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::body.end') }}
    </body>
</html> 