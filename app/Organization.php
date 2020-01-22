<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $guarded = [];


    public function department() {
    	return $this->belongsTo(Department::class);
    }

    public function creator() {
    	return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function updater() {
    	return $this->belongsTo(User::class, 'updater_id')->withTrashed();
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
