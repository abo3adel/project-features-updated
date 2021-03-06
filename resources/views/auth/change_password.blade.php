<x-app-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <div x-data="{
            updating: false,
            submit: function() {
                if ($refs.form.checkValidity()) {
                    this.updating = true;
                }
            }
        }">
            <form x-ref='form' x-on:submit="submit" method="POST" action="{{ route('change-password.update') }}">
                @csrf
                @method('put')

                @if (Auth::user()->changed_password)
                    <!-- old password -->
                    <div class="mt-4">
                        <x-label for="old-password" :value="__('auth.old-Password')" />

                        <x-input id="old-password" class="form-input w-full" type="password" name="old-password" required />
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                            @error('old-password')
                                {{ $message }}
                            @enderror
                        </p>
                    </div>
                @endif

                <!-- new Password -->
                <div class="mt-4">
                    <x-label for="password" :value="__('auth.new-password')" />

                    <x-input id="password" class="form-input w-full" type="password" name="password"
                        required autocomplete="password" />
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </p>
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-label for="password_confirmation" :value="__('auth.Confirm-Password')" />

                    <x-input id="password_confirmation" class="form-input w-full" type="password"
                        name="password_confirmation" required />
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                        @error('password_confirmation')
                            {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <x-btn-with-spinner type='submit' class="green" icon="fas-user-plus" desc='change password'
                        busy='updating'>
                        {{ __('auth.update') }}
                    </x-btn-with-spinner>
                </div>
            </form>
        </div>
    </x-auth-card>
</x-app-layout>
