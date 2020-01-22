<?php

namespace App\Helper;

use App\User;
use App\Group;
use App\Role;

class RoleChecker
{
    protected $user;

    public function __construct() {
        $this->user = \Auth::user();
    }


    /**
     * Check if current user has admin rights for the current module & role 
     *
     * @return boolean
     */    
    public function isCompanyAdmin($role, $object) {

        $companyID = null;

        /* Check the objects company ID */
        switch(get_class($object)) {
            case 'App\User':

                /* Check if company is on the user object */         
                if(!$object->department)
                    return false;

                $companyID = $object->department->department->division->company->id;

            break;            
            case 'App\Company':
                
                $companyID = $object->id;

            break;
            case 'App\Division':
                
                /* Check if company is on the division object */         
                if(!$object->company)
                    return false;

                $companyID = $object->company->id;

            break; 
            case 'App\Department':
                
                /* Check if company is on the department object */         
                if(!$object->division && !$object->division->company)
                    return false;

                $companyID = $object->division->company->id;

            break; 
            case 'App\Position': case 'App\Team':
                
                /* Check if company is on the position object */         
                if(!$object->department && !$object->department->division && !$object->department->division->company)
                    return false;

                $companyID = $object->department->division->company->id;

            break;
            case 'App\Idp': case 'App\TempIdp':
                
                /* Check if company is on the user object */         
                if(!$object->employee->department)
                    return false;

                $companyID = $object->employee->department->department->division->company->id;

            break;
            case 'App\Ticket':
                
                /* Check if company is on the user object */         
                if(!$object->owner->department)
                    return false;

                $companyID = $object->owner->department->department->division->company->id;

            break;                           
        }


        /* Check if there is no company ID */
        if(!$companyID)
            return false;


        /* Fetch employee company */
        $company = collect([$companyID]);


        return $company->intersect($this->getCompanyIDs($role))->count();
    }  

    /**
     * Fetch all company IDs the current user has permission to 
     *
     * @return array
     */
    protected function getCompanyIDs($role) {
        $companies = [];

        foreach ($this->user->groups as $key => $group) {
            
            if($group->hasRole($role))
                array_push($companies, $group->company_id);
        }

        return $companies;
    }

    /**
     * Fetch all user IDs the has permission to 
     *
     * @return array
     */
    public function getUserIDs($role) {
        $users = [];

        foreach ($role->groups as $key => $group) {
            
            /* Get users from group */
            $users = array_merge($users, $group->users()->pluck('id')->toArray());
        }

        return array_unique($users);
    }    
}