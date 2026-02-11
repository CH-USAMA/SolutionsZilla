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
                <!-- Hero Section -->
                <div class="text-center py-16">
                    <span
                        class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold tracking-widest text-blue-600 bg-blue-50 uppercase mb-6 shadow-sm border border-blue-100">
                        Enterprise-Ready v2.0
                    </span>
                    <h2 class="text-5xl font-extrabold text-gray-900 tracking-tight sm:text-7xl mb-8 leading-tight">
                        The Powerhouse Behind <br />
                        <span class="text-blue-600">Modern Healthcare</span>
                    </h2>
                    <p class="mt-4 text-xl text-gray-500 max-w-3xl mx-auto leading-relaxed">
                        Scale your clinic with the world's most advanced multi-tenant SaaS.
                        Automated AI patient engagement, enterprise audit trails, and real-time
                        billing—all in one high-performance platform.
                    </p>

                    <div class="mt-12 flex flex-col sm:flex-row justify-center gap-6">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-10 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/30 transform hover:-translate-y-1">
                                Launch Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-10 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-500 transition-all shadow-xl shadow-blue-500/30 transform hover:-translate-y-1">
                                Secure Login
                            </a>
                            <a href="{{ route('register') }}"
                                class="px-10 py-4 bg-white text-gray-900 border-2 border-gray-100 font-bold rounded-xl hover:border-blue-500 transition-all transform hover:-translate-y-1">
                                Join the Network
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- System Features Grid -->
                <div class="mt-32">
                    <div class="flex flex-col items-center mb-16">
                        <h2 class="text-3xl font-extrabold text-gray-900 text-center">Engineered for Excellence</h2>
                        <div class="w-20 h-1 bg-blue-600 mt-4 rounded-full"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <!-- Feature 1: AI WhatsApp -->
                        <div
                            class="group p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300">
                            <div
                                class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">AI WhatsApp Engine</h3>
                            <p class="text-gray-600 leading-relaxed text-sm">Autonomous appointment confirmations with
                                support for English & Urdu keyword detection.</p>
                        </div>

                        <!-- Feature 2: Audit Trail -->
                        <div
                            class="group p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all duration-300">
                            <div
                                class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Enterprise Audit</h3>
                            <p class="text-gray-600 leading-relaxed text-sm">Full regulatory compliance with activity
                                snapshots and global system activity trails.</p>
                        </div>

                        <!-- Feature 3: Tenant Isolation -->
                        <div
                            class="group p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:border-purple-200 transition-all duration-300">
                            <div
                                class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Security & Scalability</h3>
                            <p class="text-gray-600 leading-relaxed text-sm">100% data isolation per clinic with
                                automated backup and disaster recovery protocols.</p>
                        </div>

                        <!-- Feature 4: Smart Billing -->
                        <div
                            class="group p-8 rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300">
                            <div
                                class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Subscription Billing</h3>
                            <p class="text-gray-600 leading-relaxed text-sm">Integrated Stripe processing with dynamic
                                plan enforcement and automated clinic invoicing.</p>
                        </div>
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