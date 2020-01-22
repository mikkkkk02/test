<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeCategory extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 
        'title',
    ];

    protected $guarded = [];


    public function employees() {
    	return $this->hasMany(User::class)->withTrashed();
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
    public static function renderFilterArray() {
        $categories = EmployeeCategory::select(EmployeeCategory::MINIMAL_COLUMNS)->get();
        $array = [];

        /* Store each object */
        foreach ($categories as $key => $category) {
            $array[$key] = [
                'id' => $category->id,
                'label' => $category->title,
            ];
        }


        return [
            'label' => 'category',
            'options' => $array,
        ];
    } 
}