<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Scout\Searchable;

class Location extends Model
{
    use Searchable;
    use SoftDeletes;    

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 
        'name',
    ];

    const TABLE_COLUMNS = [
        'id', 
        'name',
    ];    
    
    protected $guarded = [];
    protected $appends = ['extra'];


    public function users() {
    	return $this->hasMany(User::class);
    }

    public function rooms() {
        return $this->hasMany(Room::class, 'location_id');
    }    

    public function creator() {
    	return $this->belongsTo(User::class, 'creator_id')->withTrashed();
    }

    public function updater() {
    	return $this->belongsTo(User::class, 'updater_id')->withTrashed();
    }

    public function mr_reservations() {
        return $this->hasMany(MrReservation::class, 'location_id');
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
    //


    /*
    |-----------------------------------------------
    | @Checker
    |-----------------------------------------------
    */   
    public function getRoomsID() {
        return $this->rooms()->pluck('id')->toArray();
    }


    /*
    |-----------------------------------------------
    | @Render
    |-----------------------------------------------
    */   
    public function renderViewURL() {
        return route('location.show', $this->id); 
    }    
}
