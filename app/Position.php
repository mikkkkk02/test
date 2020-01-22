<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Laravel\Scout\Searchable;

use App\DepartmentEmployee;

class Position extends Model
{
    use Searchable;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'department_id',
        'title',
    ];
    const TABLE_COLUMNS = [
        'id', 'department_id',
        'title', 'description',
    ];


    protected $guarded = [];
    protected $appends = ['extra'];


    public function department() {
    	return $this->belongsTo(Department::class);
    }

    public function employees() {
        return $this->hasMany(DepartmentEmployee::class, 'position_id');
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
            'title' => $this->title,
            'description' => $this->description,
            'department' => $this->department ? $this->department->name : null,
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
            'employees' => $this->renderEmployeeCount(),
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

            $departmentEmp = DepartmentEmployee::where([
                                'department_id' => $this->department->id,
                                'employee_id' => $employee,
                            ]);


            /* Check if employee is already included */
            if($departmentEmp->exists()) {

                $departmentEmp = $departmentEmp->first();


                /* Set position */
                $departmentEmp->setPosition($this->id);

            } else {


                $user = User::findOrFail($employee);


                /* Create department connection */
                $user->assignDepartment(
                    $this->department->id,
                    $this->id,
                    null
                );
            }
        }
    }

    public function removeEmployees($employees) {
        
        /* Loop employee IDs */
        foreach($employees as $employee) {

            /* Fetch pivot table */
            $departmentEmp = DepartmentEmployee::where([
                                'department_id' => $this->department->id,
                                'employee_id' => $employee,
                                'position_id' => $this->id,
                            ]);


            /* Check if it exists */
            if($departmentEmp->exists()) {
                $departmentEmp->first()->removePosition();
            }
        }
    }

    public function assignDepartment($department) {
        $this->department()->associate($department);
        $this->save();
    }

    public function unassignDepartment() {
        $this->department()->dissociate();
        $this->save();        
    }     


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function ifEmployeeExists($userID) {
        return $this->employees()->where('user_id', $userID)->exists();
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public function renderEmployeeCount() {
        return $this->employees()->count();
    }

    public function renderViewURL() {
        return route('position.show', $this->id);
    }
}
