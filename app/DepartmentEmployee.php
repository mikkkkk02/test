<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepartmentEmployee extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'department_id', 'team_id', 'position_id'
    ];
    
    protected $guarded = [];


    public function employee() {
        return $this->belongsTo(User::class, 'employee_id')->withTrashed();
    }

    public function team() {
        return $this->belongsTo(Team::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }
    
    public function position() {
        return $this->belongsTo(Position::class);
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function setDepartment($department) {
        $this->department_id = $department;
        $this->save();
    }   

    public function setPosition($position) {
        $this->position_id = $position;
        $this->save();
    } 

    public function setTeam($team) {
        $this->team_id = $team;
        $this->save();
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
   public function removeDepartment() {
        $this->department_id = null;
        $this->save();
    }

    public function removePosition() {
        $this->position_id = null;
        $this->save();
    } 

    public function removeTeam() {
        $this->team_id = null;
        $this->save();
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
    //    
}
