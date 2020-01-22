<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Scout\Searchable;

class Group extends Model
{
    use SoftDeletes;    
    use Searchable;

    protected $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */    
    const MINIMAL_COLUMNS = [
        'id', 'company_id',
        'name',
    ];

    const TABLE_COLUMNS = [
        'id', 'company_id',
        'name', 'description',
    ];

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const NA = 0;
    const ADMIN = 1;
    const HR = 2;
    const OD = 3;

    protected $guarded = [];
    protected $appends = ['extra'];


    public function company() {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function users() {
    	return $this->belongsToMany(User::class);
    }

    public function roles() {
    	return $this->belongsToMany(Role::class);
    }    


    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = [
            'id' => $this->id,
            'name' => $this->name,
            'company' => $this->company ? $this->company->name : '',
            'description' => $this->description,      
        ];

        return $searchable;
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'employees' => $this->renderEmployeeCount(),
            'view' => $this->renderViewURL(),
        ];
    }

    public static function getType() {
        return [
            ['label' => 'NA', 'value' => Group::NA],
            ['label' => 'Admin', 'value' => Group::ADMIN],
            ['label' => 'HR', 'value' => Group::HR],
            ['label' => 'OD', 'value' => Group::OD],
        ];
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function addUsers($user) {
        $this->users()->attach($user);
    }

    public function removeUsers($user) {
        $this->users()->detach($user);
    }

    public function updateUsers($users) {
        $this->roles()->sync($users);
    }

    public function addRole($role) {
        $this->roles()->save($role);
    }

    public function removeRole($role) {
        $this->roles()->detach($role);
    }

    public function updateRoles($roles) {
        $this->roles()->sync($roles);
    }

    public function assignRole($role) {
        $this->roles()->save($role);
    }

    public static function getAvailableGroups($role) {
        $user = \Auth::user();


        /* Add in employee service role */
        $groups = Group::where('id', 2)->get();

        /* Add in super user only roles */
        if($user->isSuperUser())
            $groups = $groups->merge(Group::where('company_id', null)->get());

        /* Add in company groups */
        foreach($user->getHandledCompanies($role) as $key => $company) {
            $groups = $groups->merge(Group::where('company_id', $company->id)->get());
        }


        return $groups->toArray();
    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */        
    public function hasRole($role) {
        if(is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return !! $role->intersect($this->roles)->Count();
    } 


     /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public function renderType() {
        return $this->renderConstants(Group::getType(), $this->type);
    }  

    public function renderConstants($array, $value) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if($obj['value'] == $value)
                return $obj['label'];
        }
    }    

    public function renderEmployeeCount() {
        return $this->users()->count();
    }

    public function renderViewURL() {
        return route('group.show', $this->id);
    }
}