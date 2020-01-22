<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignee extends Model
{
    protected $table = 'assignee_user';
    protected $guarded = [];


    public function assigner() {
    	return $this->belongsTo(User::class, 'assigner_id')->withTrashed();
    }

    public function assignee() {
    	return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }    


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    //


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
