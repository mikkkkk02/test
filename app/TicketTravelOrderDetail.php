<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketTravelOrderDetail extends Model
{
    use SoftDeletes;


    protected $guarded = [];
    protected $appends = ['extra'];

    public function ticket() {
    	return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function field() {
    	return $this->belongsTo(FormTemplateField::class, 'form_template_field_id');
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'updateurl' => $this->renderUpdateURL(),
            'removeurl' => $this->renderRemoveURL(),
        ];
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */
   	public function renderUpdateURL() {
   		return route('ticket.updatetravelorderdetails', [$this->ticket_id, $this->id]);
   	}

    public function renderRemoveURL() {
        return route('ticket.removetravelorderdetails', [$this->ticket_id, $this->id]);
    }    
}
