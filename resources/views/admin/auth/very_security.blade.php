@extends('admin.auth.layouts.app')
@section('content')
@if ($errors->any())
<div class="mb-4">
    <div class="font-medium text-red-600">{{ __('Lỗi !') }}</div>

    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form method="POST" action="{{route('admin.security-2fa.very')}}">
   @csrf
   <div>
      <label class="block font-medium text-sm text-gray-700" for="email">
        Nhập mã bảo mật Google Authenticator:
      </label>
      <br>
      <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" id="email" type="text" name="code" value="" required="required"  autofocus="autofocus">
   </div>
   <div class="flex items-center justify-end mt-4">
    <button onclick="event.preventDefault();document.getElementById('logout-form').submit();" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-4" style="background:#f64e60">
    Thoát
    </button>
    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ml-4" style="background:#1bc5bd">
    Xác nhận
    </button>
   </div>
</form>
<form id="logout-form" action="{{route('admin.logout')}}" method="POST" class="d-none">
    @csrf
</form>
@endsection