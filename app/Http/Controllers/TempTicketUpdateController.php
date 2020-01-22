<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Tickets\TicketUpdateApprovePost;
use App\Http\Requests\Tickets\TicketUpdateDisapprovePost;

use App\Ticket;
use App\TicketUpdate;
use App\TempTicketUpdate;

class TempTicketUpdateController extends Controller
{
    /**
     * Instantiate a new TicketController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Tickets\TicketUpdateIndexMiddleware', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = TempTicketUpdate::getStatus();


        return view('pages.tickets.ticketupdateforapproval', [
            'status' => $status,
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
        $tmpTicketUpdate = TempTicketUpdate::findOrFail($id);


        return view('pages.tickets.showticketupdate', [
            'ticketUpdate' => $tmpTicketUpdate,
        ]);
    }

    /**
     * Approve the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function approve(TicketUpdateApprovePost $request, $id)
    {
        $user = \Auth::user();
        $tmpTicketUpdate = TempTicketUpdate::findOrFail($id);
        $ticket = $tmpTicketUpdate->ticket;


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Create & Update original ticket */
        $ticketUpdate = TicketUpdate::create([
                'ticket_id' => $tmpTicketUpdate->ticket_id,
                'employee_id' => $tmpTicketUpdate->employee_id,
                'description' => $tmpTicketUpdate->description
            ]);
        $ticket->updateStatus($tmpTicketUpdate->ticket_status, $ticketUpdate);
        /* Approve temp ticket */
        $tmpTicketUpdate->setAsApprove();


        /* Create log */
        $ticket->form->createLog($user, "Updated the ticket status to " . $tmpTicketUpdate->description, $ticket->form->renderViewURL());


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('ticket.show', $ticket->id),
            'message' => "You have successfully approved the ticket update for ticket #" . $ticket->id
        ]);
    }

    /**
     * Disapprove the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function disapprove(TicketUpdateDisapprovePost $request, $id)
    {
        $tmpTicketUpdate = TempTicketUpdate::findOrFail($id);
        $ticket = $tmpTicketUpdate->ticket;


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Disapprove temp ticket */
        $tmpTicketUpdate->setAsDisapprove();


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('tempticketupdates'),
            'message' => "You have successfully disapproved the ticket update for ticket #" . $ticket->id
        ]);
    }    
}
