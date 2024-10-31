



@extends('admin.auth.layouts.app')
@section('content')
    @if (session()->has('error_login_gmail'))
        <div class="mb-4">
            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>
            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                <li>{{ session()->get('error_login_gmail') }}</li>
            </ul>
        </div>
    @elseif ($errors->any())
        <div class="mb-4">
            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>
            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @if (session()->has('ip_error'))
                    <li>{{ session()->get('ip_error') }}</li>
                @else
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                @endif
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <div>
            <label class="block font-medium text-sm text-gray-700" for="email">
                {{ __('Username / E-Mail Address') }}
            </label>
            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full"
                   id="email" type="text" name="email" value="{{ old('email') }}" required="required"  autofocus="autofocus">
        </div>

        <div class="mt-4 password-input-container" style="width: 100%;
            position: relative;"
        >
            <label class="block font-medium text-sm text-gray-700" for="password">
                Password
            </label>
            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" id="password" type="password" name="password" required="required" autocomplete="current-password">
            <img
                class="password-input-hide" src="/assets/backend/images/eye-show.svg" alt="" style="display: block;
                    position: absolute;
    right: 8px;
    top: 70%;
    cursor: pointer;
    transform: translateY(-50%);
" required="">
            <img class="password-input-show" src="/assets/backend/images/eye-hide.svg" alt="" style="display: none;position: absolute;
    right: 8px;
    top: 70%;
    cursor: pointer;
    transform: translateY(-50%);">
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="flex items-center">
                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="remember_me" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember Me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('admin.login.gmail') }}">
                {{ __('Login with Google?') }}
            </a>
            {{-- <a href="{{route('admin.login.gmail')}}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-4">
                Log in Gmail
            </a> --}}
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-4">
                Log in
            </button>

        </div>
    </form>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const showIcon = document.querySelector('.password-input-show');
            const hideIcon = document.querySelector('.password-input-hide');

            // Xử lý sự kiện click vào biểu tượng hide
            hideIcon.addEventListener('click', function() {
                passwordInput.type = 'text';  // Đổi input về type text để hiện mật khẩu
                hideIcon.style.display = 'none';  // Ẩn biểu tượng hide
                showIcon.style.display = 'block';  // Hiện biểu tượng show
            });

            // Xử lý sự kiện click vào biểu tượng show
            showIcon.addEventListener('click', function() {
                passwordInput.type = 'password';  // Đổi input về type password để ẩn mật khẩu
                showIcon.style.display = 'none';  // Ẩn biểu tượng show
                hideIcon.style.display = 'block';  // Hiện biểu tượng hide
            });
        });

    </script>
@endsection



