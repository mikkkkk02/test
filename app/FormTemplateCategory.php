<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormTemplateCategory extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 
        'name',
    ];

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const FORM = 1;
    const EVENT = 2;
    const LD = 3;

	protected $guarded = [];


    public function form_templates() {
    	return $this->hasMany(FormTemplate::class);
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public static function getTypes() {
        return [
            ['label' => 'Form', 'value' => FormTemplateCategory::FORM],
            ['label' => 'Event', 'value' => FormTemplateCategory::EVENT],
            ['label' => 'L&D', 'value' => FormTemplateCategory::LD],
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
    public function forForm() {
        return $this->id == FormTemplateCategory::FORM;
    }   

    public function forEvent() {
        return $this->id == FormTemplateCategory::EVENT;
    }

    public function forLearning() {
        return $this->id == FormTemplateCategory::LD;
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */   
    public static function renderFilterArray() {
        $categories = FormTemplateCategory::select(FormTemplateCategory::MINIMAL_COLUMNS)->get();        
        $array = [];


        /* Add in status options */
        $array[0] = [
            'label' => 'category',
            'options' => [],
        ];

        /* Store each object */
        foreach ($categories as $key => $category) {
            array_push($array[0]['options'], [
                'id' => $category->id,
                'label' => $category->name,
            ]);
        }

        return $array;
    }

    public function renderType() {
        return $this->renderConstants(FormTemplateCategory::getTypes(), $this->id);
    }

    public function renderConstants($array, $value) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if($obj['value'] == $value)
                return $obj['label'];
        }
    }       
}
