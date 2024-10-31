@extends('admin.auth.layouts.app')
@section('content')

    @if ($errors->any())
        <div class="mb-4">
            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>

            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ route('admin.register') }}">
        @csrf
        <div>
            <label class="block font-medium text-sm text-gray-700" for="username">
                {{ __('Tên tài khoản') }}
            </label>
            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full"
                   id="username" type="text" name="username"  value="{{ old('username') }}" required="required" autofocus="autofocus" autocomplete="username">
        </div>

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700" for="email">
                {{ __('Email') }}
            </label>
            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full"
                   id="email" type="email" name="email" value="{{ old('email') }}" required="required">
        </div>

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700" for="password">
                 {{ __('Password') }}

            </label>
            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full"
                   id="password" type="password" name="password" required="required" autocomplete="new-password">
        </div>

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700" for="password_confirmation">
                {{ __('Confirm Password') }}
            </label>
            <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full"
                   id="password_confirmation" type="password" name="password_confirmation" required="required" autocomplete="new-password">
        </div>


        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{route('admin.login')}}">
                 {{ __('Already registered?') }}
            </a>

            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-4">
                    {{ __('Register') }}
            </button>
        </div>
    </form>

@endsection

