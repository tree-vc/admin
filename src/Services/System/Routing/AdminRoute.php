<?php
/**
 * Date: 16/5/19
 * Time: 上午11:20
 */

namespace Xiaoshu\Admin\Services\System\Routing;


use Xiaoshu\Admin\Services\System\AdminNodeService;
use Xiaoshu\Foundation\Routing\BaseRoute;

class AdminRoute extends BaseRoute
{

    /**
     * @var AdminNode
     */
    protected $node;

    private static $service;

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

    /*--------------------------
     * node func
     --------------------------*/

    public function setNode(AdminNode $node)
    {
        $this->node = $node;
    }

    public function callNode($method , $parameters = [])
    {
        $node = $this->getNode();

        if(is_object($node) && method_exists($node,$method)){
            return call_user_func_array([$node,$method],$parameters);
        }

        return null;
    }

    public function getNode()
    {
        if($this->node){
            return $this->node;
        }

        $nodes = $this->getService()->getNodes();

        foreach($nodes as $node){
            if($this->getRouteName() === $node->routeName ){
                return $this->node = $node;
            }
        }

        return null;
    }

    public function getParentNodeNames()
    {
        $node = $this->getNode();
        if($node){
            return $node->parents;
        }
        return [];
    }

    public function getNodeName()
    {
        return $this->callNode('getName',[]) ? : '';
    }


    /*--------------------------
     * bool attr
     -------------------------*/

    public function getIsAuthorizedAttr()
    {
        return $this->getService()->isRouteAuthorized($this);
    }

    public function getIsCurrentAttr()
    {
        $routeName = $this->getService()->currRouteName();
        return $this->routeName === $routeName;
    }

    public function getIsVisibleAttr()
    {
        return $this->isAuthorized;
    }

}