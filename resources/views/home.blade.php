@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div>
                        <ul class="nav">
                            <li class="menu-item">
                                <a href="/">Trang chủ</a>
                            </li>
                            <li class="menu-item">
                                <a href="/tin-tuc">Tin tức</a>
                                <ul class="sub-menu" >
                                    <li class="menu-item">
                                        <a  href="/blogs" class="\">Blogs</a>
                                    </li>
                                    <li class="menu-item">
                                        <a  href="/dich-vu-game" class="\">Dịch vụ game</a>
                                    </li>
                                    <li class="menu-item">
                                        <a  href="/dieu-khoan-su-dung" class="\">Điều khoản sử dụng</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="/lich-su-giao-dich">Tặng 100% giá trị thẻ nạp</a>
                            </li>

                        </ul>
                    </div>
                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
