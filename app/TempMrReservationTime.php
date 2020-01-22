<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempMrReservationTime extends MrReservationTime
{
    public function mr_reservation() {
    	return $this->belongsTo(TempMrReservation::class, 'mr_reservation_id');
    }
}
