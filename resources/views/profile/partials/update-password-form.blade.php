@extends('layouts.auth')

@section('content')

<div class="w-full flex justify-center px-4">
    <div class="bg-white border border-gray-200 shadow-2xl rounded-xl p-10 w-full max-w-md">

        <!-- Header -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Update Password</h2>
            <p class="mt-2 text-sm text-gray-600">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </div>

        <!-- Update Password Form -->
        <form method="post" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            @method('put')

            <!-- Current Password -->
            <div>
                <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Current Password
                </label>
                <input id="update_password_current_password" name="current_password" type="password"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-700 focus:border-blue-700 focus:outline-none transition"
                       autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <!-- New Password -->
            <div>
                <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">
                    New Password
                </label>
                <input id="update_password_password" name="password" type="password"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-700 focus:border-blue-700 focus:outline-none transition"
                       autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password
                </label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-700 focus:border-blue-700 focus:outline-none transition"
                       autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between gap-4">
                <button type="submit"
                        class="w-full bg-blue-900 text-white py-3 rounded-lg hover:bg-blue-800 transition font-semibold shadow-md">
                    Save
                </button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600"
                    >Saved.</p>
                @endif
            </div>

        </form>
    </div>
</div>

@endsection