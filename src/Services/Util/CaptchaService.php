<?php
/**
 * Date: 16/5/5
 * Time: 下午5:03
 */

namespace Xiaoshu\Admin\Services\Util;

use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Session;


/**
 * 
 * 提供验证码的生成服务，
 * 请 check 方法进行判断
 *
 * Class CaptchaService
 * @package App\Services\System
 *
 * @author qzhang
 */
class CaptchaService
{

    /**
     * 生成图片验证码
     * 返回base64转码后的图片
     *
     * @param string $type
     * @return string base64
     */
    public function generate($type = '')
    {
        //生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder;
        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();
    
        //把内容存入session
        Session::put('captcha'.$type, $phrase, 5);
        return $builder->inline();
    }
    
    public function check($str , $type = '')
    {
        return $str == Session::get('captcha'.$type);
    }
}