<?php

namespace Xiaoshu\Admin\Controllers\Backend;

use Xiaoshu\Foundation\Result\Result;
use Xiaoshu\Admin\Services\AdminService;
use Xiaoshu\Admin\Services\Util\CaptchaService;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/***
 * 
 * @author qzhang
 *
 */
class PasswordController extends Controller
{
    const CAPTCHA_TYPE = 'admin_reset_password';

    public function getResetPassword(CaptchaService $captchaService)
    {
        $refreshCaptcha = $this->request->input('refresh_captcha');
        if ($refreshCaptcha){
            return $captchaService->generate(static::CAPTCHA_TYPE);
        }
        return view('backend.auth.reset-password',[
            'captchaType'   =>  static::CAPTCHA_TYPE
        ]);
    }
    
    public function postResetPassword(Request $request, AdminService $adminService, CaptchaService $captcha)
    {
        //验证验证码
        if(!$captcha->check($request->captcha , static::CAPTCHA_TYPE) && !app()->isLocal())
        {
            return back()->withInput($request->only(['name','email']))->withErrors('验证码错误');
        }

        $res = $adminService->resetPassword($request->input('name'), $request->input('email'));
        if ($res->isFailed()){
            return back()->withInput($request->all())->withErrors($res->getMsgAttr());
        }

        return redirect(route('backend::auth.login'));
    }
}
