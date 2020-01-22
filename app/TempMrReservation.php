<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempMrReservation extends MrReservation
{
    public function form()
    {
    	return $this->belongsTo(TempForm::class, 'form_id');
    }

    public function mr_reservation_times()
    {
    	return $this->hasMany(TempMrReservationTime::class, 'mr_reservation_id');
    }

    public function mr_reservation()
    {
    	return $this->belongsTo(MrReservation::class, 'mr_reservation_id');
    }

    public function form_history()
    {
        return $this->hasOne(FormHistory::class, 'temp_mr_reservation_id');
    }
}
