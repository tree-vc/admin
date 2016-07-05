<?php

namespace Xiaoshu\Admin\Controllers\Backend;

use Xiaoshu\Foundation\Result\Result;
use Xiaoshu\Admin\Services\Util\CaptchaService;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    const CAPTCHA_TYPE = 'admin_login';

    public function getLogin(Request $request, CaptchaService $captchaService)
    {
        $refreshCaptcha = $request->input('refresh_captcha');
        if ($refreshCaptcha){
            return $captchaService->generate(static::CAPTCHA_TYPE);
        }
        return response()->view('xiaoshu::backend.auth.login',[
            'captchaType'   =>  static::CAPTCHA_TYPE
        ]);
    }

    public function postLogin(Request $request , CaptchaService $captcha)
    {
        $this->validate($request,[
            'name'      =>  'required',
            'password'  =>  'required',
            'captcha'   =>  'required',
        ]);

        //验证验证码
        if(!$captcha->check($request->captcha , static::CAPTCHA_TYPE) && !app()->isLocal())
        {
            return back()->withInput($request->only(['name']))->withErrors('验证码错误');
        }

        $attempt = Auth::guard('backend')->attempt([
            'name'      =>  $request->name,
            'password'  =>  $request->password,
        ],true);

        if($attempt) {
            return redirect(route('backend::system.index'));
        }

        return back()->withInput($request->only(['name']))->withErrors('身份认证失败');
    }

    public function getLogout(Request $request)
    {
        Auth::guard('backend')->logout();
        if($request->wantsJson()){
            return Result::success('您已登出');
        }
        return redirect(route('backend::auth.login'));
    }
}
