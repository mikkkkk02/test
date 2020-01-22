<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Tickets\TicketAddUpdatePost;
use App\Http\Requests\Tickets\TicketRemoveUpdatePost;
use App\Http\Requests\Tickets\TicketUpdateTechnicianPost;
use App\Http\Requests\Tickets\TicketAttachmentPost;
use App\Http\Requests\Tickets\TicketAttachmentRemovePost;
use App\Http\Requests\Tickets\TicketUpdateTravelOrder;
use App\Http\Requests\Tickets\TravelOrderDetailPost;

use App\Notifications\Tickets\TicketWasUpdated;

use App\Helper\RoleChecker;
use App\User;
use App\Form;
use App\FormAnswer;
use App\Ticket;
use App\TicketUpdate;
use App\TempTicketUpdate;
use App\TicketAttachment;
use App\TicketTravelOrderDetail;
use App\Role;
use App\FormTemplate;
use App\FormTemplateField;
use App\FormTemplateOption;
use App\MrReservation;

class TicketController extends Controller
{
    /**
     * Instantiate a new TicketController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Tickets\TicketIndexMiddleware', ['only' => ['index']]);

        $this->middleware('App\Http\Middleware\Tickets\AssignTicketMiddleware', ['only' => ['updateTechnician']]);

        $this->middleware('App\Http\Middleware\Tickets\ViewTicketMiddleware', ['only' => ['show']]);

        $this->middleware('App\Http\Middleware\Tickets\EditTicketMiddleware', ['only' => [
                'addUpdate', 'removeUpdate', 
                'addAttachment', 'removeAttachment', 
                'updateTravelOrder',
            ]
        ]);

        $this->middleware('App\Http\Middleware\Tickets\UpdateTicketTravelOrderMiddleware', ['only' => [
                'addTravelOrderDetail', 
                'updateTravelOrderDetail'
            ]
        ]);
    }    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = Ticket::getStatus();
        $states = Ticket::getStates();
        $priorities = Ticket::getPriorities();



        return view('pages.tickets.tickets', [
            'status' => $status,
            'states' => $states,
            'priorities' => $priorities,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Add an attachment to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addAttachment(TicketAttachmentPost $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = \Auth::user();

        $response = 0;
        $message = 'Please check your permission, unable to add attachment';


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Store the image */
        if($request->file('attachment') && $request->file('attachment')->isValid() && $ticket->canAttach()) {

            $name = $request->file('attachment')->getClientOriginalName();
            $attachment = $request->file('attachment')->store('form-attachments', 'public');


            /* Add attachment */
            $ticket->addAttachment($name, $attachment, $user);

            /* Create log */
            $ticket->form->createLog($user, "Attached a file to the request's ticket", $ticket->form->renderViewURL());

            $response = 1;
            $message = 'Successfully added new attachment';
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => $response,
            'message' => $message,
        ]);        
    }

    /**
     * Remove an attachment to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeAttachment(TicketAttachmentRemovePost $request, $id)
    {
        $user = \Auth::user();
        $ticket = Ticket::findOrFail($id);
        $attachment = TicketAttachment::findOrFail($request->input('attachment_id'));

        $response = 0;
        $message = 'Please check your permission, unable to add attachment';


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Store the image */
        if($attachment && $ticket->canAttach()) {

            /* Delete file */
            \Storage::delete($attachment->path);

            /* Delete object */
            $attachment->delete();

            /* Create log */
            $ticket->form->createLog($user, "Removed an attachment to the request's ticket", $ticket->form->renderViewURL());

            $response = 1;
            $message = 'Successfully removed attachment';            
        }


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => $response,
            'message' => $message,
        ]); 
    }

    /**
     * Store an update to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addUpdate(TicketAddUpdatePost $request, $id)
    {
        $user = \Auth::user();
        $ticket = Ticket::findOrFail($id);

        $vars = $request->except(['status']);
        $vars['ticket_id'] = $ticket->id;
        $vars['employee_id'] = $user->id;

        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Create update */
        $ticketUpdate = TicketUpdate::create($vars);
        $ticket->updateStatus($request->input('status'), $ticketUpdate);

        /* Create log */
        $ticket->form->createLog($user, "Updated the ticket status to " . $request->input('description'), $ticket->form->renderViewURL());


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 2,
            'redirectURL' => route('tickets'),
            'message' => 'Successfully updated ticket of ' . $ticket->owner->renderFullname(),
            'update' => $ticketUpdate,
        ]);
    }

    /**
     * Remove an update to the resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeUpdate(TicketRemoveUpdatePost $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticketUpdate = TicketUpdate::findOrFail($request->input('ticket_update_id'));


        /* Delete update to the request */
        $ticketUpdate->delete();

        return response()->json([
            'response' => 3,
            'id' => $request->input('ticket_update_id'),
        ]);
    }

    public function updateTravelOrder(TicketUpdateTravelOrder $request, $id, $formID)
    {
        $form = Form::find($formID);

        $form->addAnswers($request->input('fields'));

        return response()->json([
            'response' => 1,
            'title' => 'Update Request',
            'message' => 'Successfully updated the Travel Order',
        ]); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        $form = $ticket->form;  
        $formTemplate = $form->template()->select(FormTemplate::MINIMAL_COLUMNS)->with('fields', 'fields.options')->first();
        $answers = $form->answers;
        $approvers = $form->approvers;
        $statuses = Ticket::getStatus();
 
        /* Get role for technicians */
        $role = Role::where('name', 'Updating of Ticket Status')->get()->first();

        /* Get available technicians */
        $roleChecker = new RoleChecker();
        $technicianIDS = $roleChecker->getUserIDs($role);
        $technicians = User::select(User::MINIMAL_COLUMNS)->whereIn('id', $technicianIDS)->orderBy('last_name', 'asc')->get(); 

        $mrReservation = null;

        if ($form->mr_reservation) {
            $mrReservation = MrReservation::with('mr_reservation_times')->where('id', $form->mr_reservation->id)->first();
        }

        return view('pages.tickets.showticket', [
            'ticket' => $ticket,
            'form' => $form,
            'formTemplate' => $formTemplate,
            'disableForm' => 1,
            'answers' => $answers,
            'approvers' => $approvers,            
            'technicians' => $technicians,
            'statuses' => $statuses,
            'mrReservation' => $mrReservation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource's technician in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateTechnician(TicketUpdateTechnicianPost $request, $id)
    {
        // dd($request->all());
        $ticket = Ticket::findOrFail($id);
        $user = \Auth::user();
        $technician = User::findOrFail($request->input('technician_id'));


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Assign technician to the ticket */
        $ticket->assignTechnician($technician);

        /* Create log */
        $ticket->form->createLog($user, "Assigned the ticket to " . $technician->renderFullname(), $ticket->form->renderViewURL());


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();
            

        return response()->json([
            'response' => 1,
            'redirectURL' => route('ticket.show', $ticket->id),
            'title' => 'Assign technician',
            'message' => 'Successfully assigned ' . $technician->renderFullname() . ' to ticket #' . $ticket->id
        ]);
    }

    public function addTravelOrderDetail(TravelOrderDetailPost $request, $id)
    {
        $ticket = Ticket::find($id);
        $vars = $request->except([]);

        $response = 0;
        $title = 'Unable to add Travel Order details';
        $message = 'You have exceeded the number of rows for the Travel Order details table';

        /* Create if less than number of rows in the travel order details table */
        if (count($ticket->travel_order_details) < $ticket->getTravelOrderTableRowCount()) {
            $response = 1;
            $title = 'Add Travel Order details';
            $message = 'You have successfully added a new travel order details';
            /* Create travel order details */
            $ticket->travel_order_details()->create($vars);
        }

        return response()->json([
            'response' => $response,
            'title' => $title,
            'message' => $message,
        ]);
    }

    public function updateTravelOrderDetail(Request $request, $id, $travelID)
    {
        $travelDetails = TicketTravelOrderDetail::find($travelID);
        $vars = $request->except([]);

        /* Update the travel order details */
        $travelDetails->update($vars);
        

        return response()->json([
            'response' => 1,
            'title' => 'Update Travel Order details',
            'message' => 'You have successfully updated the travel order details',
        ]);
    }

    public function removeTravelOrderDetail($id, $travelID)
    {
        $travelDetails = TicketTravelOrderDetail::find($travelID);

        /* Update the travel order details */
        $travelDetails->delete();
        

        return response()->json([
            'response' => 1,
            'title' => 'Delete Travel Order details',
            'message' => 'You have successfully deleted the travel order details',
        ]);
    }       

    public function updateRoomDetails(TicketAddUpdatePost $request, $id)
    {
        /** 
        *   Update ticket status
        */

        $user = \Auth::user();
        $mrReservationId = MrReservation::find($id);
        $ticket = Ticket::where('form_id', '=' , $mrReservationId->form_id)->firstOrFail();

        $vars = $request->except(['location_id','status','room_id','_token']);
        $vars['ticket_id'] = $ticket->id;
        $vars['employee_id'] = $user->id;

        /**
         * Begin Transaction
         */
        \DB::beginTransaction();
        /**
        *  Update ticket status
        */
        $ticketUpdate = TicketUpdate::create($vars);
        $ticket->updateStatus($request->input('status'), $ticketUpdate);

        $mrReservation = MrReservation::submit($request, $id, false, false, null, false, null, false);

        /**
         * Commit
         */
        \DB::commit();
        return response()->json([
            'response' => 1,
            'redirectURL' => route('ticket.show', $ticket->id),
            'title' => 'Update room details',
            'message' => 'Successfully updated room ' . $mrReservation->name,
            'update' => $ticketUpdate,
        ]);
    } 

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}