<?php
/**
 * Date: 16/5/19
 * Time: 上午11:12
 */

namespace Xiaoshu\Admin\Services\System;

use Xiaoshu\Admin\Models\BackendRole;
use Xiaoshu\Admin\Services\System\Routing\AdminNode;
use Xiaoshu\Admin\Services\System\Routing\AdminRoute;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router as LaravelRouter;

use App\Models\Admin;

class AdminNodeService
{
    const ADMIN_ROUTER_REQUEST = 'backend';

    protected $router;

    protected $request;

    protected $adminRoutes;

    protected $nodes = [];

    protected $activeNodes = [];

    protected $visibleNodes = [];

    protected $currentUser;

    protected $currentRoute;

    protected $currentRouteName;

    protected $currentAuthors;

    public function __construct(LaravelRouter $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;

        $this->boot();
    }

    protected function boot()
    {
        AdminRoute::setService($this);
        AdminNode::setService($this);
    }



    /*--------------------------------------
     *  routes
     -------------------------------------*/

    public function getAdminRoutes()
    {
        return $this->adminRoutes = $this->buildAdminRoutes();
    }

    public function getRoute($name)
    {
        $adminRoutes = $this->getAdminRoutes();
        return isset($adminRoutes[$name]) ? $adminRoutes[$name] : null;
    }

    public function getRouteNames()
    {
        $routes = $this->getAdminRoutes();
        return array_keys($routes);
    }

    protected function buildAdminRoutes()
    {
        $routes = $this->router->getRoutes()->getRoutes();

        $adminRouters = array_reduce($routes, function ($adminRouters, $route) {

            $router = $this->makeAdminRoute($route);

            if (is_null($router)) {
                return $adminRouters;
            }

            $adminRouters[$router->getRouteName()] = $router;

            return $adminRouters;
        }, []);

        return $adminRouters;
    }

    protected function makeAdminRoute(Route $route)
    {
        $name = $route->getName();

        if ($this->checkRouteType($name)) {
            return $this->constructAdminRoute($name);
        }

        return null;
    }

    protected function checkRouteType($name)
    {
        return strpos($name, static::ADMIN_ROUTER_REQUEST.'::') !== false;
    }

    protected function constructAdminRoute($name)
    {
        return new AdminRoute($name);
    }

    /*------------------------------------
     * current
     -----------------------------------*/

    /**
     * @return false | Admin
     */
    public function currUser()
    {
        if (!is_null($this->currentUser)) {
            return $this->currentUser;
        }
        return $this->currentUser = $this->request->user(static::ADMIN_ROUTER_REQUEST) ?: false;
    }

    public function currRouteName()
    {
        if ($this->currentRouteName) {
            return $this->currentRouteName;
        }

        $route = $this->request->route();

        if($route){
            return $this->currentRouteName = $route->getName();
        } else {
            return $this->currentRouteName = null;
        }

    }

    public function currRoute()
    {
        if (is_null($this->currentRoute) && $this->currRouteName()) {
            $this->currentRoute = $this->constructAdminRoute($this->currRouteName());
        }
        return $this->currentRoute;
    }

    public function currNode()
    {
        return $this->currRoute()->getNode();
    }

    public function currRootNode()
    {
        $node = $this->currNode();
        if(!$node){
            return null;
        }
        $parents = $node->parents;

        if(isset($parents[0])){
            return $this->getNode($parents[0]);
        }

        return $node;
    }

    /*-----------------------------------
     * node
     -----------------------------------*/

    public function getNodes()
    {
        if($this->nodes){
            return $this->nodes;
        }
        return $this->nodes = $this->buildNodes();
    }

    /**
     * @param $name
     * @return AdminNode | null
     */
    public function getNode($name)
    {
        $nodes = $this->getNodes();

        return isset($nodes[$name]) ? $nodes[$name] : null;
    }

    protected function buildNodes()
    {
        $tree = $this->loadMenuTree();

        $nodes = $this->insertNodeFromTree([],$tree,'');

        return $nodes;
    }

    protected function insertNodeFromTree($nodes, $tree , $prefix = '')
    {
        foreach($tree as $name => $data) {
            $routeName      = isset($data['router'])?$data['router']:'';
            $route          = $this->getRoute($routeName);

            unset($data['router']);
            $sonTree = $data;
            $sons    = array_keys($sonTree);

            $node  =  $this->constructNode($name , $prefix , $route , $sons);
            $nodes[$node->getName()] = $node;

            $nodes = $this->insertNodeFromTree($nodes , $sonTree ,$node->getName());
        }

        return $nodes;
    }

    protected function constructNode($nodeName , $prefix ,$route , $sons)
    {
        return new AdminNode($nodeName , $prefix, $route, $sons);
    }


    /*-----------------------------------
     * active
     -----------------------------------*/

    /**
     * @return array
     */
    public function getActiveNodes()
    {
        if($this->activeNodes){
            return $this->activeNodes;
        }
        $currRoute = $this->currRoute();

        $nodes     = $currRoute->getParentNodeNames();
        $nodeName  = $currRoute->getNodeName();

        array_unshift($nodes,$nodeName);
        return $this->activeNodes = $nodes;
    }


    protected function activateOneNode($name)
    {
        if(isset($this->nodes[$name])){
            call_user_func_array([$this->nodes[$name],'activate'],[true]);
        }
    }

    /*-----------------------------------
     * authorize
     -----------------------------------*/

    /**
     * 当前用户的权限节点
     * @return array
     */
    public function getAuthorizedRoutes()
    {
        if (isset($this->currentAuthors['routes'])) {
            return $this->currentAuthors['routes'];
        }

        $user = $this->currUser();

        if ($user) {
            return $this->currentAuthors['routes'] = $user->authors;
        }

        return [];
    }


    public function isRouteAuthorized(AdminRoute $route)
    {
        if(isset($this->currentAuthors['all'])) {
            return $this->currentAuthors['all'];
        }

        $user = $this->currUser();

        if(!$user || $user->isLocked() || $user->isDeleted()){
            return $this->currentAuthors['all'] = false;
        }

        if($user->isSupervisor()){
            return $this->currentAuthors['all'] = true;
        }

        $excepts = $this->getExceptsRoutes();

        $name = $route->getName();
        if(in_array($name , $excepts)){
            return true;
        }

        $authors = $this->getAuthorizedRoutes();
        return in_array($route->getName(),$authors);
    }

    protected function getExceptsRoutes()
    {
        return config('backend.except_admin_routes');
    }

    public function authorizeRoutes(array $routeNames)
    {
        $result = [];
        foreach($routeNames as $name){
            $route = $this->getRoute($name);
            if(!$route){
                continue;
            }
            $names = $this->authorizeOneRoute($route);
            $result = array_unique(array_merge($result,$names));
        }
        return $result;
    }

    protected function authorizeOneRoute(AdminRoute $route)
    {
        //$sons = $route->index ? $this->authorizeRouteSons($route) : [];
        $routes = $this->authorizeRouteParents($route);
        return $routes;
    }

    protected function authorizeRouteParents(AdminRoute $route)
    {
        $names[] = $route->name;
        $nodeNames = $route->getParentNodeNames();

        return array_reduce($nodeNames ,function($routeNames , $nodeName){
            $node  = $this->getNode($nodeName);
            if(!is_object($node)) {
                return $routeNames;
            }

            if($node->routeName){
                $routeNames[] = $node->routeName;
            }

            return $routeNames;

        }, $names);
    }

    protected function authorizeRouteSons(AdminRoute $route)
    {
        $node = $route->getNode();

        if(!$node){
            return [];
        }

        $names  = $this->addNodeSonsRouteNames([] , $node);

        return $names;
    }

    public function addNodeSonsRouteNames(array $names , AdminNode $node)
    {
        $name       = $node->routeName;
        if($name){
            $names[]    = $name;
        }

        foreach($node->getNodeSons() as $node){
            $names = $this->addNodeSonsRouteNames($names , $node);
        }

        return $names;
    }

    /*-----------------------------------
     * visible
     -----------------------------------*/

    public function getVisibleNodes()
    {
        if($this->visibleNodes){
            return $this->visibleNodes;
        }

        $nodeNames = array_reduce($this->getAdminRoutes(),function($nodeNames , $route){

            if(!$route->isAuthorized){ //关键
                return $nodeNames;
            }

            $nodeNames = array_merge($nodeNames , $route->parentNodeNames);
            $nodeNames[] = $route->nodeName;

            return array_unique($nodeNames);
        },[]);

        return $this->visibleNodes = $nodeNames;
    }

    public function isNodeVisible(AdminNode $node)
    {
        $nodeNames = $this->getVisibleNodes();
        return in_array($node->getName() , $nodeNames);
    }
    /*-----------------------------------
     * tree
     -----------------------------------*/

    protected function loadMenuTree()
    {
        return config('adminmenu',[]);
    }

    public function buildNodeTree()
    {
        $tops = array_keys($this->loadMenuTree());

        return array_map(function($name){
            return $this->getNode($name);
        },$tops);
    }

    public function buildRoutesTree()
    {
        $tree = [];

        foreach($this->getAdminRoutes() as $route){
            $tree = $this->insertRouteToTree($tree , $route , 0);
        }

        return $tree;
    }

    protected function insertRouteToTree(array $tree , AdminRoute $route , $level = 0)
    {
        $groups = $route->groups;
        $sec    = isset($groups[$level]) ? $groups[$level] : false;
        if($sec){
            $sonTree = isset($tree[$sec]) ? $tree[$sec] : [];
            $tree[$sec] = $this->insertRouteToTree($sonTree , $route , $level +1);
        } elseif($route->index){
            $tree['router'] = $route->getName();
        } else {
            $tree[$route->getName()] = ['router' => $route->getName()];
        }
        return $tree;
    }

    public function buildNodeTreeArray()
    {
        $tree = $this->loadMenuTree();

        $tree['others'] = array_reduce($this->getAdminRoutes(),function($others,$route){
            if(!$route->node){
                $others[] = $route->name;
            }

            return $others;
        });

        return $tree;
    }

    public function buildRoleAuthorsTree(BackendRole $role)
    {
        $authors = $role->nodes;

        $tree    = $this->buildNodeTree();

        return $this->insertRoleAuthorsToTree($tree,$authors);
    }

    protected function insertRoleAuthorsToTree(array $nodeTree , array $authors)
    {
        $result = [];
        foreach($nodeTree as $node){
            $routeName = $node->routeName;
            if(!$routeName || in_array($routeName,$authors)){
                $result[$node->title] = $this->insertRoleAuthorsToTree($node->nodeSons , $authors );
            }
        }
        return $result;
    }

    /*--------------------------
     * generate
     --------------------------*/

    public function generateAdminRouters()
    {
        return $this->buildRoutesTree();
    }

    public function generateAdminNodeTree()
    {
        return $this->buildNodeTreeArray();
    }


}