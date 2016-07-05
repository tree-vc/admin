@extends('backend.layout')
@inject('captcha','App\Services\Util\CaptchaService')
@section('header')
@endsection
@section('content')
<div id="wapper">
    <div id="header" class="bg-dark-blue clearfix bg">
        <div class="logo"></div>
        <div class="login_header">
            <span class="login_header_title color-white">找回密码</span>
        </div>
        <div class="dologin"><a href="{{ route('backend::auth.login') }}" class="color-white">登录</a></div>
    </div>

    <form id="backend_reset_password" method="post" name="backend_reset_password_form">
    <div class="password_container">
        {{ csrf_field() }}
        <div class="password_div" >
            <span class="color-grey">用户名：</span><input name="name" type="text" class="login_input" style="padding-left: 10px; width: 253px;" placeholder="请输入用户名" autocomplete="off" value="{{ old('name') }}">
        </div>

        <div class="password_div">
            <span class="color-grey" style="padding-left: 1em;">邮箱：</span><input name="email" type="text" class="login_input" style="padding-left: 10px; width: 253px;" placeholder="请输入邮箱" autocomplete="off" value="{{ old('email') }}">
        </div>

        <div id="captcha">
           <span class="color-grey"> 验证码：</span><input type="text" name="captcha" class="login_input login_yzm" placeholder="请输入验证码">
            <img id="img" src="{!! $captcha->generate($captchaType) !!}" width="80" height="34" title="点击刷新验证码" class="pointer" style="margin-left: 5px;" data-refresh-url="{{ route('backend::auth.reset-password') }}?refresh_captcha=yes">
        </div>

        <div class="login_error" style=" padding-left: 71px;">
            <span id="errormsg" class="hide">用户名或邮箱错误</span>
            @if(count($errors)>0)
                <span class="color-red">
                @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
            </span>
            @endif
        </div>
        <div style=" padding-left: 71px;">
            <input id="submit" type="button" value="确&nbsp;&nbsp;定" class="login_button">
            <input id="submit_real" type="submit" style="display: none;">
        </div>
    </div>
    </form>
</div>
<script src="/js/admin/find_password.js"></script>
@endsection
