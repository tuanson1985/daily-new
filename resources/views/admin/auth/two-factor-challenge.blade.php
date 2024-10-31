{{--@extends('admin.auth.layouts.app')--}}

{{--@section('content')--}}
{{--    <div class="container">--}}
{{--        <div class="row">--}}
{{--            <div class="col-md-8 col-md-offset-2">--}}
{{--                <div class="panel panel-default">--}}
{{--                    <div class="panel-heading">Two Factor Authentication</div>--}}
{{--                    <div class="panel-body">--}}
{{--                        <p>Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.</p>--}}

{{--                        @if (session('error'))--}}
{{--                            <div class="alert alert-danger">--}}
{{--                                {{ session('error') }}--}}
{{--                            </div>--}}
{{--                        @endif--}}
{{--                        @if (session('success'))--}}
{{--                            <div class="alert alert-success">--}}
{{--                                {{ session('success') }}--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        <strong>Enter the pin from Google Authenticator Enable 2FA</strong><br/><br/>--}}
{{--                        <form class="form-horizontal" action="{{ route('admin.2faVerify') }}" method="POST">--}}
{{--                            {{ csrf_field() }}--}}
{{--                            <div class="form-group{{ $errors->has('one_time_password-code') ? ' has-error' : '' }}">--}}
{{--                                <label for="one_time_password" class="col-md-4 control-label">One Time Password</label>--}}
{{--                                <div class="col-md-6">--}}
{{--                                    <input name="one_time_password" class="form-control"  type="text"/>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="form-group">--}}
{{--                                <div class="col-md-6 col-md-offset-4">--}}
{{--                                    <button class="btn btn-primary" type="submit">Authenticate</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </form>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}





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


    <div x-data="{ recovery: false }">
        <div class="mb-4 text-sm text-gray-600" >
            Please confirm access to your account by entering the authentication code provided by your authenticator application.
        </div>

        <div class="mb-4 text-sm text-gray-600" style="display: none;">
            Please confirm access to your account by entering one of your emergency recovery codes.
        </div>



        <form class="form-horizontal" action="{{ route('admin.two-factor-challenge') }}" method="POST">
            @csrf
            <div class="mt-4">
                <label class="block font-medium text-sm text-gray-700" for="code">
                    Code
                </label>
                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" id="one_time_password" type="text" inputmode="numeric" name="one_time_password" autofocus="autofocus"  autocomplete="one-time-code">
            </div>

            <div class="mt-4" style="display: none;">
                <label class="block font-medium text-sm text-gray-700" for="recovery_code">
                    Recovery Code
                </label>
                <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" id="recovery_code" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code">
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer" >
                    Use a recovery code
                </button>

                <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer" x-show="recovery"  style="display: none;">
                    Use an authentication code
                </button>

                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-4">
                    Log in
                </button>
            </div>
        </form>
    </div>

@endsection




