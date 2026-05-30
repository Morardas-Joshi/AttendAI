<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attend AI - Secure Attendance System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100">

    <!-- HEADER -->
    <header class="bg-blue-800 text-white shadow-2xl border-b border-blue-700">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">

            <!-- Logo Section -->
            <div class="flex items-center space-x-3">
                <div class="-mt-1">
                    <h1 class="text-xl font-semibold tracking-wide drop-shadow-sm">
                        Attend AI
                    </h1>
                    <p class="text-sm text-blue-200 uppercase tracking-widest">
                        Smart Attendance Platform
                    </p>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4 text-sm">
                <!-- Authority Text -->
                <span class="text-blue-200 border-r border-blue-600 pr-4">
                    Authorized Access Only
                </span>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="bg-white text-blue-900 px-4 py-2 rounded-md font-medium hover:bg-blue-100 transition">
                        Logout
                    </button>
                </form>
            </div>

        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-grow flex items-center justify-center px-4 py-12">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="bg-blue-50 shadow-[0_-15px_30px_-10px_rgba(0,0,0,0.1)] border-t border-blue-200">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row justify-between items-center text-sm text-gray-600 space-y-3 md:space-y-0">

            <div>
                © {{ date('Y') }} 
                <span class="font-semibold text-gray-800">Attend AI</span>.
                All rights reserved.
            </div>

            <div class="text-gray-500">
                Secure • Role-Based • AI Powered
            </div>

        </div>
    </footer>

</body>

</html>