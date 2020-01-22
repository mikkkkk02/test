<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormTemplateContact extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_template_id', 'employee_id',
        'type', 'type_value',
    ]; 
    
    /*
    |-----------------------------------------------
    | @Type
    |-----------------------------------------------
    */
    const LEVEL = 1;
    const EMPLOYEE = 2;
    const COMPANY = 3;
    const CEO = 4;
    const GROUP_HEAD = 5;

    /*
    |-----------------------------------------------
    | @Level
    |-----------------------------------------------
    */
    const IMMEDIATE_LEADER = 0;
    const NEXT_LEVEL_LEADER = 1;

    /*
    |-----------------------------------------------
    | @Company
    |-----------------------------------------------
    */
    const HR = 0;
    const OD = 1;


    protected $guarded = [];
    protected $appends = ['extra'];


    public function form_template() {
    	return $this->belongsTo(FormTemplate::class);
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
            'type' => $this->renderType(),
        ];
    }

    public static function getTypes() {
        return [
            ['label' => 'By Level', 'value' => FormTemplateApprover::LEVEL],
            ['label' => 'By Employee', 'value' => FormTemplateApprover::EMPLOYEE],
            ['label' => 'By Company', 'value' => FormTemplateApprover::COMPANY],
        ];
    }

    public static function getLevels() {
        return [
            ['label' => 'Immediate Leader', 'value' => FormTemplateApprover::IMMEDIATE_LEADER],
            ['label' => 'Next Level Leader', 'value' => FormTemplateApprover::NEXT_LEVEL_LEADER],
        ];
    }

    public static function getCompanies() {
        return [
            ['label' => 'HR', 'value' => FormTemplateApprover::HR],
            ['label' => 'OD', 'value' => FormTemplateApprover::OD],
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
    public function renderType() {
        switch($this->type) {
            case FormTemplateApprover::LEVEL: return $this->renderConstants(FormTemplateApprover::getLevels(), $this->type_value);
            case FormTemplateApprover::EMPLOYEE: return $this->renderConstants(FormTemplateApprover::getTypes(), $this->type);  
            case FormTemplateApprover::COMPANY: return $this->renderConstants(FormTemplateApprover::getCompanies(), $this->type_value);
            case FormTemplateApprover::CEO: return 'CEO';
            default: return '';
        }
    }  

    public function renderConstants($array, $value) {

        /* Loop through the array */
        foreach ($array as $obj) {
            
            if($obj['value'] == $value)
                return $obj['label'];
        }
    }   
}
