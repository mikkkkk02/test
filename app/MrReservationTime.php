<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Helper\Helpers;
use Carbon\Carbon;

class MrReservationTime extends Model
{
	use SoftDeletes;
	
    protected $guarded = [];

    public function mr_reservation() {
    	return $this->belongsTo(MrReservation::class, 'mr_reservation_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'updater_id');
    }

    /**
     * @Helpers
     */
    
    public static function toCalendarObject($reservationTimes = null)
    {
        if (!$reservationTimes) {
            $reservationTimes = MrReservationTime::get();
        }
        
        /* Fetch events */
        $eventArray = [];

        /* Create event array */
        foreach ($reservationTimes as $reservationTime) {

            if ($reservationTime->mr_reservation) {

                if (!$reservationTime->mr_reservation->form->ticket) {
                    continue;
                }

                if ($reservationTime->mr_reservation->form->ticket->isClosed()) {
                    /* Fetch No. of days */
                    $date = Carbon::parse($reservationTime->date);
                    $startTime = Carbon::parse($reservationTime->start_time);
                    $endTime = Carbon::parse($reservationTime->end_time);

                    /* Escape if on weekend and weekend isn't toggled as included */
                    // $canAdd = $date->isWeekend() ? $reservationTime->hasWeekend : true;
                    // if($canAdd) {
                    $startDate = null;
                    $endDate = null;

                    /* Create tmp object */
                    $tmp = new \stdClass();
                    $tmp->id = 'iservemrreservation' . $reservationTime->id;
                    $tmp->title = $reservationTime->renderName();
                    $tmp->backgroundColor = $reservationTime->renderColor();
                    $tmp->textColor = '#' . Helpers::renderTextColor($reservationTime->renderColor());
                    $tmp->boderColor = $reservationTime->renderColor();
                    $tmp->url = $reservationTime->renderViewURL();

                    /* Check if all days have the same time */
                    $startDate = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime->format('H:i'));
                    $endDate = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime->format('H:i'));

                    $tmp->start = $startDate->toDateTimeString();
                    $tmp->googleStart = $startDate->format('Y-m-d\TH:i:sP');
                    $tmp->end = $endDate->toDateTimeString();
                    $tmp->googleEnd = $endDate->format('Y-m-d\TH:i:sP');

                    /* Collect array of events */
                    array_push($eventArray, $tmp);

                    // }
                }
            }
        }

        return $eventArray;     
    }

    /**
     * @Render
     */
    public function renderName() {
        $mrReservation = $this->mr_reservation;
        $name = $mrReservation->name;

        if ($mrReservation) {
            $room = $mrReservation->room;
            if ($room) {
                $name .= ' - ' . $room->name;
            }
        }

        return $name;
    }

    public function renderColor() {
        $color = '#FFFFFF';

        $mrReservation = $this->mr_reservation;

        if ($mrReservation) {
            $room = $mrReservation->room;
            if ($room) {
                $color = '#' . $room->color;
            }
        }

        return $color;
    }

    public function renderViewURL() {
        return $this->mr_reservation->renderViewURL();
    }
}
