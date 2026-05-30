@extends('layouts.auth')

@section('content')

<div class="w-full flex justify-center px-4">
    <div class="bg-white border border-gray-200 shadow-2xl rounded-xl p-10 w-full max-w-md">

        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/Attend.png') }}" 
                 alt="Attend AI Logo" 
                 class="h-24 w-auto object-contain drop-shadow-2xl">
        </div>

        <h2 class="text-2xl font-bold text-gray-900 text-center mb-4">
            Forgot Your Password?
        </h2>

        <p class="text-sm text-gray-600 text-center mb-6">
            No problem. Just enter your email and we'll send you a password reset link.
        </p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 bg-green-100 text-green-700 p-3 rounded-md text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-700 focus:border-blue-700 focus:outline-none transition">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-blue-900 text-white py-3 rounded-lg hover:bg-blue-800 transition font-semibold tracking-wide shadow-md">
                Send Password Reset Link
            </button>
        </form>

        <p class="mt-6 text-xs text-center text-gray-500">
            Remembered your password? <a href="{{ route('login') }}" class="text-blue-700 hover:text-blue-900 underline">Login</a>
        </p>

    </div>
</div>

@endsection