<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketUpdate extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_id', 'employee_id',
        'status',
        'created_at',      
    ];

    const TABLE_COLUMNS = [
        'id', 'form_id', 'employee_id',
        'status', 'description',
        'created_at',
    ];

    protected $guarded = [];
    protected $appends = ['extra'];


    public function ticket() {
    	return $this->belongsTo(Ticket::class);
    }

    public function employee() {
        return $this->belongsTo(User::class, 'employee_id')->withTrashed();
    }


    /*
    |-----------------------------------------------
    | @Helper
    |-----------------------------------------------
    */
    public function getExtraAttribute() {
        return $this->attributes['extra'] = [
            'deleteurl' => $this->renderDeleteURL(),
        ];
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    //


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
    public function renderDeleteURL() {
        return route('ticket.removeupdate', $this->ticket->id);
    }    
}