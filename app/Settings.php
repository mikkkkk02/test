<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $guarded = [];

    public function ceo() {
    	return $this->belongsTo(User::class, 'ceo_id')->withTrashed();
    }

    public function hr() {
    	return $this->belongsTo(User::class, 'hr_id')->withTrashed();
    } 

    public function od() {
    	return $this->belongsTo(User::class, 'od_id')->withTrashed();
    } 
}
