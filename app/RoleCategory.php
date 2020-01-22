<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleCategory extends Model
{
    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 
        'name', 'description'
    ];	

    /*
    |-----------------------------------------------
    | @Category
    |-----------------------------------------------
    */
    const EMP_SELFSERVICE = 1;
    const USER_MNGMT = 2;
    const EMPDEPT_MNGMT = 3;
    const CALENDAR_MNGMT = 4;
    const IDP_MNGMT = 5;
    const FORMS_MNGMT = 6;
    const TICKET_MNGMT = 7;
    const SETTINGS_MNGMT = 8;
    const GOVFORMS_MNGMT = 9;
    const MEETINGROOM_MNGMT = 10;

    protected $guarded = [];


    public function roles() {
    	return $this->hasMany(Role::class);
    }
}
