<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solution Zilla</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-white">
    <div
        class="relative min-h-screen flex flex-col items-center justify-center selection:bg-blue-500 selection:text-white">
        <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
            <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                <div class="flex lg:justify-center lg:col-start-2">
                    <h1 class="text-4xl font-bold text-blue-600 tracking-tight">Solution Zilla</h1>
                </div>
                <nav class="-mx-3 flex flex-1 justify-end">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]">
                            Log in
                        </a>

                        <a href="{{ route('register') }}"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]">
                            Register Clinic
                        </a>
                    @endauth
                </nav>
            </header>

            <main class="mt-6">
                <div class="text-center">
                    <h2 class="text-5xl font-extrabold text-gray-900 tracking-tight sm:text-6xl mb-6">
                        Modern Management for <br />
                        <span class="text-blue-600">Pakistani Clinics</span>
                    </h2>
                    <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto">
                        Streamline your medical practice with automated WhatsApp reminders, patient history, and smart
                        scheduling. Designed for simplicity and speed.
                    </p>

                    <div class="mt-10 flex justify-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-500 transition shadow-lg shadow-blue-500/30">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-500 transition shadow-lg shadow-blue-500/30">
                                Login
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition">
                                Register Clinic
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="p-6 rounded-2xl bg-gray-50">
                        <div
                            class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Smart Scheduling</h3>
                        <p class="text-gray-600">Prevent double bookings and manage doctor availability with ease.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-gray-50">
                        <div
                            class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">WhatsApp Reminders</h3>
                        <p class="text-gray-600">Reduce no-shows by 40% with automated 24h & 2h appointment reminders.
                        </p>
                    </div>
                    <div class="p-6 rounded-2xl bg-gray-50">
                        <div
                            class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Clinic Reports</h3>
                        <p class="text-gray-600">Track patients, revenue, and clinic performance at a glance.</p>
                    </div>
                </div>
            </main>

            <footer class="py-16 text-center text-sm text-gray-500">
                Solution Zilla v1.0 &copy; {{ date('Y') }}
            </footer>
        </div>
    </div>
</body>

</html>