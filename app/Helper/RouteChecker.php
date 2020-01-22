<?php

namespace App\Helper;

use Illuminate\Routing\Route as Route;

use App\Group;

class RouteChecker
{
	protected $route;
    protected $url;

    protected $user;


    public function __construct(Route $route) {
        $this->route = $route;
        $this->user = \Auth::user();
    }

    /**
     * Check if current route is equal to the given route
     *
     * @param  string  $routeName
     * @param  string  $output
     * @return string
     */
    public function isOnRoute($routeName, $output = "active") {
    	
        if($this->route->getName() == $routeName)
    		return $output;

        return null;
    }

    /**
     * Check if current route is equal to the given array of route
     *
     * @param  array  $routeNames
     * @param  string  $output
     * @return string
     */
    public function areOnRoutes(array $routeNames, $output = "active") {

        foreach($routeNames as $routeName) {

            if($this->isOnRoute($routeName, true))
                return $output;
        }

        return null;
    }

    /**
     * Check if current user has permission to view the given module
     *
     * @return boolean
     */    
    public function hasModulePermission($category) {

        $collection = collect($category);


        /* Check authenticattion */
        if(!$this->user)
            return false;


        /* Check user groups */
        foreach ($this->user->groups as $key => $group) {
            
            /* Check role categories if it exist */
            if($collection->intersect($group->roles->pluck('role_category_id'))->count())
                return true;
        }

        return false;
    }

    /**
     * Check if current user has permission to view the given module
     *
     * @return boolean
     */    
    public function hasModuleRoles($roles) {

        /* Check authenticattion */
        if(!$this->user)
            return false;


        /* Check user roles */
        foreach ($this->user->groups as $key => $group) {
            
            /* Check role categories if it exist */
            foreach ($roles as $key => $role) {
                if($group->hasRole($role))
                    return true;
            }
        }

        return false;
    }    
}