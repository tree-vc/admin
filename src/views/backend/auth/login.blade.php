@extends('xiaoshu::backend.layout')
@inject('captcha','Xiaoshu\Admin\Services\Util\CaptchaService')
@section('header','')
@section('content')
<div id="wapper">
    <div id="header" class="bg-white clearfix bg">
        <div class="login_logo"></div>
        <div class="login_header">
            <span class="login_header_title">账号登录</span>
        </div>
    </div>
    <div class="login_bg">
        <div class="login_container">
            <div class="login">
                <form id="login" action="{{ route('backend::auth.post-login') }}" method="POST">
                    {!! csrf_field() !!}
                    <div class="login_div" >
                        <input name="name"
                               value="{{ old('name') }}"
                               required required-msg="请输入用户名" data-error="error_box" type="text" class="login_input" placeholder="用户名" autocomplete="off"><img src="/xiaoshu.admin/images/login_03.jpg" class="img_position">
                    </div>

                    <div class="login_div">
                        <input name="password"
                               required required-msg="请输入密码" data-error="error_box" type="password" class="login_input" placeholder="密码" autocomplete="off"><img src="/xiaoshu.admin/images/login_05.jpg" class="img_position">
                    </div>

                    <div id="captcha" class="clearfix">
                        <img  id="img" src="{!! $captcha->generate($captchaType) !!}" width="80" height="34" title="点击刷新验证码" class="pointer f-right" data-refresh-url="{{ route('backend::auth.login') }}?refresh_captcha=yes" /><input name="captcha" required required-msg="请输入验证码" data-error="error_box" type="text" class="login_input login_yzm" placeholder="验证码">
                    </div>

                    @if(count($errors)>0)
                    <div class="login_error">
                        @foreach ($errors->all() as $error)
                            <div data-name="error_box">{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif
                    <div>
                        <input id="login" type="submit" value="登&nbsp;&nbsp;录" class="login_button">
                    </div>
                </form>
                <div class="wjmm"><a href="{{ route('backend::auth.reset-password') }}" class="">忘记密码？</a></div>
            </div>
        </div>
        <div class="copyright" style="vertical-align: bottom;">&copy;2015&nbsp;&nbsp;SuperLk.com&nbsp;&nbsp;浙ICP备15038305号-1 </div>
    </div>
</div>
@endsection
@section('footer','')
@push('scripts')
<script>
    $(function() {
        $("#img").click(function () {
            var img = $(this);
            $.ajax({
                url: $(this).attr('data-refresh-url'),
                type: 'get',
                success: function (data) {
                    img.attr('src', data);
                }
            });
        });
    });
</script>
@endpush
