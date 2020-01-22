<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Scout\Searchable;

use App\Division;

class Company extends Model
{
    use SoftDeletes;    
    use Searchable;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'hr_id', 'od_id',
        'name', 'abbreviation',
    ];
    const TABLE_COLUMNS = [
        'id', 'hr_id', 'od_id',
        'name', 'description', 'abbreviation',
    ];    

    protected $guarded = [];
    protected $appends = ['extra'];

    public function divisions() {
    	return $this->hasMany(Division::class)->withTrashed();
    }

    // public function admin_technician() {
    //     return $this->belongsTo(User::class, 'admin_technician_id');
    // } 
 
    public function admin_technicians() {
        return $this->belongsToMany(User::class, 'company_admintechnician', 'company_id', 'technician_id');
    }    

    // public function hr_technician() {
    //     return $this->belongsTo(User::class, 'hr_technician_id');
    // }
    
    public function hr_technicians() {
        return $this->belongsToMany(User::class, 'company_hrtechnician', 'company_id', 'technician_id');
    }    

    // public function od_technician() {
    //     return $this->belongsTo(User::class, 'od_technician_id');
    // }    

    public function od_technicians() {
        return $this->belongsToMany(User::class, 'company_odtechnician', 'company_id', 'technician_id');
    }

    public function hr() {
        return $this->belongsTo(User::class, 'hr_id')->withTrashed();
    }

    public function od() {
        return $this->belongsTo(User::class, 'od_id')->withTrashed();
    }

    public function creator() {
    	return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function updater() {
    	return $this->belongsTo(User::class, 'updater_id')->withTrashed();
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
            'abbreviation' => $this->abbreviation,
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
            'name' => $this->renderAbbr(),
            'view' => $this->renderViewURL(),
            'fetchdivisions' => $this->renderFetchDivisionsURL(),
        ];
    }

    public function getAdminTechnicianID() {
        return $this->admin_technicians()->pluck('id')->toArray();
    } 

    public function getHRTechnicianID() {
        return $this->hr_technicians()->pluck('id')->toArray();
    }

    public function getODTechnicianID() {
        return $this->od_technicians()->pluck('id')->toArray();
    }

    public static function getTechnicianIds() {
        $ids = [];

        $companies = Company::all();

        foreach ($companies as $company) {
            $ids = array_merge($ids, $company->getAdminTechnicianID());
            $ids = array_merge($ids, $company->getHRTechnicianID());
            $ids = array_merge($ids, $company->getODTechnicianID());
        }

        return array_unique($ids);
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function addDivisions($divisions) {
        foreach ($divisions as $division) {
            $division = Division::find($division);

            $division->assignCompany($this);
        }
    }

    public function removeDivisions($divisions) {
        foreach ($divisions as $division) {
            $division = Division::withTrashed()->find($division);

            $division->unassignCompany();
        }
    }

    public function updateAdminTechnicians($technicians) {
        $this->admin_technicians()->sync($technicians);
    }

    public function updateHRTechnicians($technicians) {
        $this->hr_technicians()->sync($technicians);
    }

    public function updateODTechnicians($technicians) {
        $this->od_technicians()->sync($technicians);
    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */   
    //


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */   
    public function renderDepartmentTableFilter() {
        $array = [];

        /* Add in status options */
        $array[0] = [
            'label' => 'group',
            'options' => [],
        ];

        foreach ($this->divisions as $division) {
            array_push($array[0]['options'], [
                'id' => $division->id,
                'label' => $division->name,
            ]);
        }


        return $array;
    }   

    public function renderTeamTableFilter() {
        $array = [];

        /* Add in status options */
        $array[0] = [
            'label' => 'department',
            'options' => [],
        ];

        foreach ($this->divisions as $division) {
            foreach ($division->departments as $department) {
                array_push($array[0]['options'], [
                    'id' => $department->id,
                    'label' => $department->name,
                ]);
            }
        }


        return $array;
    }

    public static function renderFilterArray() {
        $categories = Company::select(Company::MINIMAL_COLUMNS)->get();
        $array = [];

        /* Store each object */
        foreach ($categories as $key => $category) {
            $array[$key] = [
                'id' => $category->id,
                'label' => $category->name,
            ];
        }


        return [[
            'label' => 'company',
            'options' => $array,
        ]];
    }         

    public function renderAbbr() {
        return $this->abbreviation ? $this->abbreviation : $this->name;
    }

    public function renderFetchDivisionsURL() {
        return route('company.fetchdivision', $this->id); 
    }

    public function renderViewURL() {
        return route('company.show', $this->id); 
    }    
}