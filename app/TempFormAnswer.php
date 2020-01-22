<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FormAnwer;

class TempFormAnswer extends FormAnswer
{
    public function temp_form() {
        return $this->belongsTo(TempForm::class, 'temp_form_id')->withTrashed();
    }

    public function answer() {
        return $this->belongsTo(FormAnswer::class);
    }    
}