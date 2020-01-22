<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Scout\Searchable;

use App\GovernmentFormAttachment;

class GovernmentForm extends Model
{
    use SoftDeletes;    
    use Searchable;

    public $asYouType = true;

    /*
    |-----------------------------------------------
    | @Columns
    |-----------------------------------------------
    */
    const MINIMAL_COLUMNS = [
        'id', 'creator_id', 'updater_id',
        'name',
        'created_at', 'updated_at',
    ];
    const TABLE_COLUMNS = [
        'id', 'creator_id', 'updater_id',
        'name', 'description',
        'created_at', 'updated_at',
    ];    

    protected $guarded = [];
    protected $appends = ['extra'];


    public function attachment() {
    	return $this->hasOne(GovernmentFormAttachment::class);
    }

    public function creator() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function updater() {
        return $this->belongsTo(User::class)->withTrashed();
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
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_by' => $this->updater->id ? $this->updater->renderFullname() : '',
            'updated_at' => $this->updated_at,
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
        	'link' => $this->renderDownloadURL(),
            'create' => $this->renderViewURL(),
            'view' => $this->renderViewURL(),
        ];
    }


    /*
    |-----------------------------------------------    
    | @Methods
    |-----------------------------------------------
    */
    public function addAttachment($name, $path) {

        /* Delete old file */
        if($this->attachment) {

            /* Delete file in storage */
            \Storage::delete('/public/' . $this->attachment->path);

            /* Delete main object */
            $this->attachment->delete();
        }

        /* Create the file data */
        $attachment = GovernmentFormAttachment::create([
            'government_form_id' => $this->id,
            
            'name' => $name,
            'path' => $path
        ]);


        /* Add attachment */
        $this->attachment()->save($attachment);
        $this->save();
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
	public function renderDownloadURL() {
        return $this->attachment ? $this->attachment->URL() : null;
    }   

    public function renderViewURL() {
        return route('governmentform.show', $this->id); 
    }  
}
