<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FormApprover;

class TempFormApprover extends FormApprover
{
    public function temp_form() {
        return $this->belongsTo(TempForm::class, 'temp_form_id')->withTrashed();
    }	
}
