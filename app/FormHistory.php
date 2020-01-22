<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FormHistory;
use App\FormHistoryAnswer;

class FormHistory extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'created_at', 'updated_at',
    ];   
    const TABLE_COLUMNS = [
        'id', 'purpose',

        'mr_title', 'mr_date', 'mr_start_time', 'mr_end_time',
        'isLocal', 'course_cost', 'accommodation_cost', 'meal_cost', 'transport_cost', 'others_cost', 'total_cost',

        'created_at', 'updated_at',
    ];


    public function form() {
    	return $this->belongsTo(Form::class)->withTrashed();
    }

    public function temp_form() {
    	return $this->belongsTo(TempForm::class, 'temp_form_id');
    }    

    public function answers() {
    	return $this->hasMany(FormHistoryAnswer::class);
    }

    public function temp_mr_reservation() {
        return $this->belongsTo(TempMrReservation::class, 'temp_mr_reservation_id');
    }

    public function mr_reservation() {
        return $this->belongsTo(FormHistoryMrReservation::class, 'history_mr_reservation_id');
    }

    protected $guarded = [];
    protected $appends = ['extra'];


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
   	public function setAnswers() {
   		/* Fetch current answers */
   		foreach ($this->form->answers as $key => $answer) {
   			/* Create history */
   			$answer = FormHistoryAnswer::create([
   					'form_history_id' => $this->id,
   					'form_id' => $this->form_id,
   					'form_template_field_id' => $answer->form_template_field_id,

   					'value' => $answer->value,
   					'value_others' => $answer->value_others,
   				]);
   		}
   	}

    public function setMrReservation() {

        $mrReservation = $this->temp_mr_reservation->mr_reservation;

        $historyMrReservation = FormHistoryMrReservation::create([
            'location_id' => $mrReservation->location_id,
            'room_id' => $mrReservation->room_id,
            'form_template_id' => $mrReservation->form_template_id,
            'form_id' => $mrReservation->form_id,
            'name' => $mrReservation->name,
            'description' => $mrReservation->description,
            'color' => $mrReservation->color,
            'creator_id' => $mrReservation->creator_id,
            'updater_id' => $mrReservation->updater_id,
        ]);

        $this->history_mr_reservation_id = $historyMrReservation->id;
        $this->save();

        foreach ($mrReservation->mr_reservation_times as $mrReservationTime) {
            $historyMrReservation->mr_reservation_times()->create([
                'date' => $mrReservationTime->date,
                'start_time' => $mrReservationTime->start_time,
                'end_time' => $mrReservationTime->end_time,
                'creator_id' => $mrReservationTime->creator_id,
                'updater_id' => $mrReservationTime->updater_id,
            ]);
        }

    }


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */
    public function isDraft() {
        return false;
    }   

    public function isEditable() {
        return false;
    } 


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
    public function renderViewURL() {
        return route('requesthistory.show', $this->id); 
    }          
}
