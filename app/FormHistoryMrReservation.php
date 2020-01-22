<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormHistoryMrReservation extends MrReservation
{
    public function form_history() {
    	return $this->belongsTo(FormHistory::class, 'history_mr_reservation_id');
    }

    public function mr_reservation_times() {
    	return $this->hasMany(FormHistoryMrReservationTime::class, 'mr_reservation_id');
    }
}
