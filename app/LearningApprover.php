<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LearningApprover extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'employee_id',
        'type',
    ];

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const IMMEDIATE_LEADER = 0;
    const NEXT_LEVEL_LEADER = 1;
    const EMPLOYEE = 2;

    protected $guarded = [];
    protected $appends = ['extra'];


    public function employee() {
        return $this->belongsTo(User::class, 'employee_id')->withTrashed();
    }    


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'type' => $this->renderType(),
        ];
    }

    public static function getTypes() {
        return [
            ['label' => 'Immediate Leader', 'value' => LearningApprover::IMMEDIATE_LEADER],
            ['label' => 'Next Level Leader', 'value' => LearningApprover::NEXT_LEVEL_LEADER],
            ['label' => 'Employee', 'value' => LearningApprover::EMPLOYEE],
        ];
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    //


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
