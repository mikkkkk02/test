<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempAttachment extends Model
{
    protected $guarded = [];
    protected $appends = ['extra'];


	public function employee() {
    	return $this->belongsTo(User::class)->withTrashed();
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'employee' => $this->employee ? $this->employee->renderFullname() : '',
            'view' => $this->URL(),
            'delete' => $this->renderDeleteURL(),
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
    public function renderDeleteURL() {
    	return route('temp.removeattachment', $this->id);
    }
}
