<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormTemplateOption extends Model
{
    use SoftDeletes;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_template_field_id',
        'sort', 'value', 'type', 'type_value',
    ];

    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const TEXTFIELD = 0;
    const DATEFIELD = 1;
    const NUMBER = 2;
    const DROPDOWN = 3;
    const TIME = 4;

    protected $guarded = ['id'];
    protected $appends = ['extra'];


    public function form_template_field() {
    	return $this->belongsTo(FormTemplateField::class);
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

    public function setSort($index) {
        $this->sort = $index;
        $this->save();
    }
 
     public static function getTypes() {
        return [
            ['label' => 'Textfield', 'value' => FormTemplateOption::TEXTFIELD],
            ['label' => 'Datefield', 'value' => FormTemplateOption::DATEFIELD],
            ['label' => 'Number', 'value' => FormTemplateOption::NUMBER],
            ['label' => 'Dropdown', 'value' => FormTemplateOption::DROPDOWN],
            ['label' => 'Time', 'value' => FormTemplateOption::TIME],
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
    public function isTextfield() {
        return $this->type == FormTemplateOption::TEXTFIELD;
    }

    public function isDatefield() {
        return $this->type == FormTemplateOption::DATEFIELD;
    }

    public function isNumber() {
        return $this->type == FormTemplateOption::NUMBER;
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */   
    public function renderType() {
        return $this->renderConstants(FormTemplateOption::getTypes(), $this->type);
    }

    public function renderConstants($array, $value) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if($obj['value'] == $value)
                return $obj['label'];
        }
    } 
}
