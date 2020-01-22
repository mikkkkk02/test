<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FormAnwer;

class FormHistoryAnswer extends FormAnswer
{
    protected $guarded = [];

    public function history() {
        return $this->belongsTo(FormHistory::class);
    }
}
