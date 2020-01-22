<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Room extends Model
{
	use Searchable;
    use SoftDeletes;

    const TABLE_COLUMNS = [
    	'id', 'location_id',
    	'name', 'description', 'color',
    	'creator_id', 'updater_id',
    ];

    const MINIMAL_COLUMNS = [
        'id', 'location_id',
        'name',
    ];

    protected $guarded = [];
    protected $appends = ['extra'];

    public function location() {
    	return $this->belongsTo(Location::class, 'location_id')->withTrashed();
    }

    public function creator() {
    	return $this->belongsTo(User::class, 'creator_id');
    }

    public function updater() {
    	return $this->belongsTo(User::class, 'updater_id');
    }

    public function mr_reservations() {
        return $this->hasMany(MrReservation::class, 'room_id');
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

    public function isSelected($id, $relation = 'location') {
        $result = false;

        if ($this->$relation->id === $id) {
            $result = 'selected';
        }

        return $result;
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
    public function renderViewURL() {
        return route('room.show', $this->id); 
    }
}
