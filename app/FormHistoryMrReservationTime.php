<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormHistoryMrReservationTime extends MrReservationTime
{
    public function mr_reservation() {
    	return $this->belongsTo(FormHistoryMrReservation::class, 'mr_reservation_id');
    }
}
