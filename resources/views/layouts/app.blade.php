<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SecureLab | Smart Access</title>

    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <meta name="theme-color" content="#4f46e5">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Custom styles para sa SecureLab */
        .bg-secure-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        }
    </style>

    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
      window.OneSignalDeferred = window.OneSignalDeferred || [];
      OneSignalDeferred.push(async function(OneSignal) {
        await OneSignal.init({
          appId: "90eda13e-689e-47c3-98de-17e594bd915c",
        });
        
        /* ITO ANG UTOS NA MAGPAPALABAS NG NOTIFICATION PROMPT */
        OneSignal.Slidedown.promptPush();
      });
    </script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <nav class="bg-white border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <span class="text-xl font-bold text-indigo-600">SecureLab</span>
                    </div>
                    <div class="flex items-center space-6">
                        @auth
                            <span class="text-sm text-gray-600 mr-4">{{ Auth::user()->name }}</span>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('Service Worker registered with scope: ', registration.scope);
                }, function(err) {
                    console.log('Service Worker registration failed: ', err);
                });
            });
        }
    </script>
</body>
</html>