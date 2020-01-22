<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Temp\TempAttachmentPost;
use App\Http\Requests\Temp\TempAttachmentRemovePost;

use App\TempAttachment;

class TempAttachmentController extends Controller
{
    /**
     * Instantiate a new TempController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('App\Http\Middleware\Temp\TempAttachmentMiddleware', ['only' => ['addAttachment', 'removeAttachment']]);
    } 

    /**
     * Add an attachment to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addAttachment(TempAttachmentPost $request)
    {
        $user = \Auth::user();
        $attachment = null;


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Store the image */
        if($request->file('attachment') && $request->file('attachment')->isValid()) {

            $name = $request->file('attachment')->getClientOriginalName();
            $path = $request->file('attachment')->store('temp-attachments', 'public');

		    $attachment = TempAttachment::create([
		        'employee_id' => $user->id,
		        
		        'name' => $name,
		        'path' => $path
		    ]);
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'attachment' => $attachment,
            'message' => 'Successfully added new attachment'
        ]);        
    }

    /**
     * Remove an attachment to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeAttachment(TempAttachmentRemovePost $request)
    {
        $attachmentID = $request->input('attachment_id');
        $attachment = TempAttachment::findOrFail($attachmentID);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Store the image */
        if($attachment) {

            /* Delete file */
            \Storage::delete($attachment->path);

            /* Delete object */
            $attachment->delete();
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'attachment' => $attachmentID,
            'message' => 'Successfully removed attachment'
        ]); 
    }    
}
