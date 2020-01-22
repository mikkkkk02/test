<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GovernmentFormAttachment extends Model
{
    protected $guarded = [];
    protected $appends = ['extra'];


	public function government_form() {
    	return $this->belongsTo(GovernmentForm::class);
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'view' => $this->URL(),
        ];
    } 

    public function URL() {
    	return url('/') . '/storage/' . $this->path;
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
