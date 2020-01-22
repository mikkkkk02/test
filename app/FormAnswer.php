<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormAnswer extends Model
{
    protected $guarded = [];

    public function form() {
    	return $this->belongsTo(Form::class)->withTrashed();
    }

    public function field() {
    	return $this->belongsTo(FormTemplateField::class);
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
