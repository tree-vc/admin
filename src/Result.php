<?php
/**
 * Date: 16/5/11
 * Time: 下午2:16
 */

namespace Xiaoshu\Admin;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * 为各种方法返回通用结果的类
 *
 * Class Result
 * @package App\Core\Supports
 *
 * @author zhuming
 */
class Result implements ArrayAccess , Arrayable , JsonSerializable , Jsonable
{

    protected $status;

    protected $msg;

    protected $time;

    protected $attributes;


    public function __construct($success = true , $msg = '' )
    {
        $this->status   = (bool)$success;
        $this->msg      = $msg;
        $this->time     = date('Y-m-d H:i:s');
    }

    public static function success($msg = '' )
    {
        return new static(true , $msg);
    }

    public static function fail($msg = '' )
    {
        return new static(false, $msg );
    }

    public static function make($array)
    {
        return (new static(true,''))->with($array);
    }

    public function isSuccess()
    {
        return (bool)$this->status;
    }

    public function isFailed()
    {
        return !$this->isSuccess();
    }

    public function set($name , $value)
    {
        $this->setAttr($name , $value);
        return $this;
    }

    public function with($array)
    {
        foreach($array as $key => $value) {
            $this->setAttr($key,$value);
        }
        return $this;
    }

    public function __get($name)
    {
        return $this->getAttr($name);
    }

    public function __set($name , $value)
    {
        $this->setAttr($name , $value);
    }

    public function getAttr($name)
    {
        $method = 'get'.studly_case($name).'Attr';
        if(method_exists($this,$method)){
            return call_user_func_array([$this,$method],[]);
        }

        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function setAttr($name , $value)
    {
        $method = 'set'.studly_case($name).'Attr';
        if(method_exists($this,$method)) {
            call_user_func_array([$this,$method],[$value]);
        } else {
            $this->attributes[$name] = $value;
        }
    }


    public function getStatusAttr()
    {
        return (bool)$this->status;
    }

    public function setStatusAttr($status)
    {
        $this->status = (bool)$status;
    }

    public function getMsgAttr()
    {
        return (string)$this->msg;
    }

    public function setMsgAttr($msg)
    {
        $this->msg = (string)$msg;
    }

    public function getTimeAttr()
    {
        return $this->time;
    }

    public function setTimeAttr($time)
    {
        $this->time = $time;
    }


    public function toArray()
    {
        $array = $this->attributes;
        $array['status']    = $this->getStatusAttr();
        $array['msg']       = $this->getMsgAttr();
        $array['time']      = $this->getTimeAttr();

        return $array;
    }

    public function toJson($option = 0)
    {
        $array = $this->toArray();
        return json_encode($array , $option);
    }

    public function jsonSerialize()
    {
        return $this->toJson();
    }

    public function offsetExists($offset)
    {
        if(in_array($offset,['status','msg'])){
            return true;
        }

        return array_key_exists($offset , $this->attributes);
    }

    public function offsetGet($offset)
    {
        return $this->getAttr($offset);
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        $this->setAttr($offset,$value);
    }

}
