<?php
/**
 * Date: 16/5/19
 * Time: 下午1:38
 */

namespace Xiaoshu\Admin\Services\System\Routing;

use Xiaoshu\Admin\Services\System\AdminNodeService;

class AdminNode
{
    protected $name;

    protected static $service;

    protected $attr = [];

    public function __construct($name , $prefix = '', AdminRoute $route = null , array $sons = [])
    {
        $this->name     = $prefix ? $prefix.'.'.$name : $name;
        $this->route    = $route;
        $this->sons     = array_map(function($son){
            return $this->name.'.'.$son;
        },$sons);

        if($route) {
            $route->setNode($this);
        }
    }

    public static function setService(AdminNodeService $service)
    {
        static::$service = $service;
    }

    /**
     * @return AdminNodeService
     */
    public function getService()
    {
        if(static::$service) {
            return static::$service;
        }

        return static::$service = app(AdminNodeService::class);
    }

    public function callRoute($method,$parameters = [])
    {
        if(is_object($this->route) && method_exists($this->route,$method)){
            return call_user_func_array([$this->route,$method],$parameters);
        }
        return null;
    }

    public function activate($bool = true)
    {
        $this->attr['active'] = $bool;
    }

    public function url($parameter = [])
    {
        if($this->route){
            return $this->route->url($parameter);
        }
        return '';
    }




    public function getRouteNameAttr()
    {
        return $this->callRoute('getName',[]);
    }

    public function getParentsAttr()
    {
        $parents    = [];
        $sections   = $this->getSections();
        for($i = 1 ; $i<count($sections) ; $i ++) {
            $parents[] = implode('.',array_slice($sections,0,$i));
        }

        return $parents;
    }

    public function getPrefixAttr()
    {
        $parents = $this->parents;
        return end($parents) ? : '';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSons()
    {
        return $this->sons;
    }

    public function getNodeSons()
    {
        $nodes = $this->getService()->getNodes();
        return array_reduce($this->getSons() ,function ($res , $son) use ($nodes){
            if(isset($nodes[$son])){
                $res[$son] = $nodes[$son];
            }
            return $res;
        },[]);
    }

    public function getTitleAttr()
    {
        $sections = $this->sections;
        return end($sections);
    }

    public function getSections()
    {
        return explode('.', $this->name);
    }

    public function getActiveAttr()
    {
        $nodes = $this->getService()->getActiveNodes();
        return in_array($this->name ,$nodes);
    }

    /*--------------------------
     * bool attr
     -------------------------*/


    public function getVisibleAttr()
    {
        return $this->getService()->isNodeVisible($this);
    }


    public function getIsRoute()
    {
        return !empty($this->route);
    }


    /*------------------------
     * magic
     -----------------------*/

    public function __get($name)
    {
        if(array_key_exists($name,$this->attr)){
            return $this->attr[$name];
        }

        $method = 'get'.ucfirst($name).'Attr';
        if(method_exists($this,$method)){
            return $this->attr[$name] = call_user_func_array([$this,$method],[]);
        }

        $method = 'get'.ucfirst($name);
        if(method_exists($this,$method)){
            return call_user_func_array([$this,$method],[]);
        }
    }

}