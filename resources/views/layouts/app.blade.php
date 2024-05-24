<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta property="og:title" content="SES交流会申込アプリ {{ config('app.name') }}｜株式会社LEGAREA">
        <meta property="og:description" content="SES交流会の申し込みが簡単にできるアプリです。イベント情報の確認から参加登録まで、シンプルで使いやすいインターフェースを提供します。今すぐ登録して、業界の最新情報をキャッチアップしましょう！">
        <meta property="og:image" content="{{ url('images/og-image.jpg') }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">

@if (isset($title))
        <title>{{ $title }}</title>
@else
        <title>{{ (($header) ? $header.'｜' : '').config('app.name') }}</title>
@endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

@yield('scripts')

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            
<!--ナビゲーション-->
@if (isset($navigation))
            @livewire('navigation-menu')
@endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-semibold text-base md:text-xl text-gray-800 leading-tight">
                            {{ $header }}
                        </h2>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
