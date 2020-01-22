<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Laravel\Scout\Searchable;

use App\User;
use App\Form;

class FormLog extends Model
{
    use Searchable;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'form_id', 'employee_id',
        'text',
        'created_at', 'updated_at',
    ];   
    const TABLE_COLUMNS = [
        'id', 'form_id', 'employee_id',
        'text',
        'created_at', 'updated_at',
    ];

    protected $guarded = [];
    protected $appends = ['extra'];


    public function form() {
    	return $this->belongsTo(Form::class)->withTrashed();
    }

    public function updater() {
        return $this->belongsTo(User::class, 'employee_id')->withTrashed();
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
            'updater' => $this->updater ? $this->updater->renderFullname() : '',
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
            //
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
	//   
}
