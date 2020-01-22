<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

use App\Notifications\MrReservations\AssignedRoom;

use Carbon\Carbon;

class MrReservation extends Model
{
    use Searchable;
    use SoftDeletes;

    const TABLE_COLUMNS = [
    	'id', 'location_id', 'room_id',
    	'name', 'description',
    ];

    const MINIMAL_COLUMNS = [
        'id', 'location_id', 'room_id',
        'name',
    ];

    protected $guarded = [];
    protected $appends = ['extra'];

    public function mr_reservation_times() {
    	return $this->hasMany(MrReservationTime::class, 'mr_reservation_id');
    }

    public function location() {
        return $this->belongsTo(Location::class, 'location_id')->withTrashed();
    }

    public function room() {
        return $this->belongsTo(Room::class, 'room_id')->withTrashed();
    }

    public function template() {
        return $this->belongsTo(FormTemplate::class, 'form_template_id')->withTrashed();
    }

    public function form() {
        return $this->belongsTo(Form::class, 'form_id')->withTrashed();
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function updater() {
        return $this->belongsTo(User::class, 'updater_id')->withTrashed();
    }

    public function temp_mr_reservation()
    {
        return $this->hasOne(TempMrReservation::class, 'temp_mr_reservation_id');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = [
            'id' => $this->id,
            'name' => $this->name,
        ];

        return $searchable;
    }

    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'view' => $this->renderViewURL(),
        ];
    }

    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public static function submit($request, $mrReservationId = null, $store = true, $autoApprove = true, $form = null, $isTemp = false, $origForm = null, $hasReservations = true) {

        $mrReservation = null;

        if ($mrReservationId) {
            if ($isTemp) {
                $mrReservation = TempMrReservation::withTrashed()->find($mrReservationId);
            } else {
                $mrReservation = MrReservation::withTrashed()->find($mrReservationId);
            }
        }

        $vars = $request->except(['mrreservationtime', 'mr_title', 'mr_date', 'mr_start_time', 'mr_end_time', 'fields', 'purpose', 'employee_id', 'form_template_id', 'save', 'draft']);
        $user = \Auth::user();

        $formId = null;
        $templateId = $request->input('form_template_id');

        if ($mrReservation) {
            $formId = $mrReservation->form->id;
            $templateId = $mrReservation->form->template->id;
        }

        if ($autoApprove) {
            $form = Form::createRequest($request, $templateId, $formId, true);
        }


        if ($store && $form && $autoApprove) {
            $form->setAsApproved(true);
            $form->ticket->setAsClose(true);
            $form->ticket->updateState(Carbon::now());
        }

        if ($store && $form) {
            $vars['form_template_id'] = $form->template->id;
            $vars['form_id'] = $form->id;
            $vars['creator_id'] = $user->id;
        }

        $vars['updater_id'] = $user->id;

        if (!$mrReservation) {
            
            /* Create reservation */
            if ($isTemp) {
                $vars['mr_reservation_id'] = $origForm->mr_reservation->id;
                $mrReservation = TempMrReservation::create($vars);
            } else {
                $mrReservation = MrReservation::create($vars);
            }

        } else {
            /* Notify User owner */
            if ($mrReservation->location_id !== $request->input('location_id') || $mrReservation->room_id !== $request->input('room_id')) {
                if (!$isTemp) {
                    $ticket = $mrReservation->form->ticket;
                    $owner = $ticket->owner;

                    if ($user->id !== $owner->id) {
                        $owner->notify(new AssignedRoom($ticket, $user));
                    }
                }
            }

            /* Update the reservation */
            $mrReservation->update($vars);
        }

        $ids = [];

        if ($request->has('mrreservationtime') && $hasReservations) {

            $mrReservationTimes = $request->input('mrreservationtime');

            $ids = array_keys($mrReservationTimes);

            foreach ($mrReservationTimes as $id => $mrReservationTime) {
                $mrReservationTime['mr_reservation_id'] = $mrReservation->id;
                $mrReservationTime['creator_id'] = $user->id;
                $mrReservationTime['updater_id'] = $user->id;

                if ($isTemp) {
                    $reservationTime = TempMrReservationTime::updateOrCreate(
                        [
                            'id' => is_integer($id) ? $id : null,
                        ],
                        $mrReservationTime
                    );
                } else {
                    $reservationTime = MrReservationTime::where('id', $id)->first();

                    if ($reservationTime) {
                        $reservationTime->update($mrReservationTime);
                    } else {
                        $reservationTime = MrReservationTime::create($mrReservationTime);
                    }
                }

                array_push($ids, $reservationTime->id);
            }
        }

        if (!$store && $hasReservations) {
            foreach ($mrReservation->mr_reservation_times as $mrReservationTime) {
                if (!in_array($mrReservationTime->id, $ids)) {
                    $mrReservationTime->delete();
                }
            }
        }

        return $mrReservation;

    }

    public function archive() {
        $this->delete();
        // $this->form->setAsCancelled(true);
        // $this->form->ticket->setAsCancelled(true);
    }

    public function unarchive() {
        $this->restore();
        // $this->form->setAsApproved(true);
        // $this->form->ticket->setAsClose(true);
    }


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
    public function renderViewURL() {
        return route('mrreservation.show', $this->id); 
    }
}
