<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Laravel\Scout\Searchable;

use App\Division;

class Department extends Model
{
    use Searchable;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'division_id',
        'name',
    ];
    const TABLE_COLUMNS = [
        'id', 'division_id',
        'name', 'description',
    ];    

    protected $guarded = [];
    protected $appends = ['extra'];


    // @Not in use!
    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function division() {
        return $this->belongsTo(Division::class)->withTrashed();
    }

    public function teams() {
        return $this->hasMany(Team::class);
    }

    public function positions() {
        return $this->hasMany(Position::class);
    }    

    public function employees() {
        return $this->hasMany(DepartmentEmployee::class, 'department_id', 'id');
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
            'description' => $this->description,
            'division' => $this->division ? $this->division->name : null,
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
            'view' => $this->renderViewURL(),
            'teams' => $this->renderTeamCount(),
            'fetchpositionsteams' => $this->renderFetchPositionsTeamsURL(),
            'fetchpositions' => $this->renderFetchPositionsURL(),
            'fetchteams' => $this->renderFetchTeamsURL(),
        ];
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function addEmployees($employees) {

        /* Loop employee IDs */
        foreach($employees as $employee) {

            /* Check if employee is already included */
            if(!$this->ifEmployeeExists($employee)) {

                $employee = User::find($employee);


                /* Assign department */
                $employee->assignDepartment($this->id);
            }
        }
    }

    public function removeEmployees($employees) {
        
        /* Loop employee IDs */
        foreach($employees as $employee) {

            /* Fetch pivot table */
            $departmentEmp = $this->employees()->where('employee_id', $employee)->get();


            /* Check if it exists */
            if($departmentEmp->count() > 0)
                $departmentEmp->first()->delete();
        }
    }

    public function assignDivision($division) {
        $this->division()->associate($division);
        $this->save();
    }

    public function unassignDivision() {
        $this->division()->dissociate();
        $this->save();        
    }

    public function addPositions($positions) {
        foreach ($positions as $position) {
            $position = Position::find($position);

            $position->assignDepartment($this);
        }
    }

    public function removePositions($positions) {
        foreach ($positions as $position) {
            $position = Position::find($position);

            $position->unassignDepartment();
        }
    }

    public function addTeams($teams) {
        foreach ($teams as $team) {
            $team = Team::find($team);

            $team->assignDepartment($this);
        }
    }

    public function removeTeams($teams) {
        foreach ($teams as $team) {
            $team = Team::find($team);

            $team->unassignDepartment();
        }
    }    


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */   
    public function ifEmployeeExists($userID) {
        return $this->employees()->where('employee_id', $userID)->exists();
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public static function renderFilterArray() {
        $categories = Division::select(Division::MINIMAL_COLUMNS)->get();
        $array = [];

        /* Store each object */
        foreach ($categories as $key => $category) {
            $array[$key] = [
                'id' => $category->id,
                'label' => $category->name,
            ];
        }


        return [[
            'label' => 'group',
            'options' => $array,
        ]];
    }  

    public function renderTeamCount() {
        return $this->teams()->count();
    }

    public function renderFetchPositionsTeamsURL() {
        return route('department.fetchpositionsteams', $this->id); 
    }   

    public function renderFetchPositionsURL() {
        return route('department.fetchpositions', $this->id); 
    }     

    public function renderFetchTeamsURL() {
        return route('department.fetchteams', $this->id); 
    }

    public function renderViewURL() {
        return route('department.show', $this->id); 
    }
}