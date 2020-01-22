<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormUpdate extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_id', 'employee_id',
        'created_at',        
    ];

    const TABLE_COLUMNS = [
        'id', 'form_id', 'employee_id',
        'description',
        'created_at',
    ];    

    protected $guarded = [];
    protected $appends = ['extra'];


    public function form() {
    	return $this->belongsTo(Form::class)->withTrashed();
    }

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
            'deleteurl' => $this->renderDeleteURL(),
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
    public function renderDeleteURL() {
        return route('request.removeupdate', $this->form->id);
    }    
}
