<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Rooms\RoomStorePost;

use App\Room;
use App\Location;

class RoomController extends Controller
{
     /**
     * Instantiate a new RoomController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Rooms\RoomIndexMiddleware', ['only' => ['index', 'create']]);

        $this->middleware('App\Http\Middleware\Rooms\EditRoomMiddleware', ['only' => ['store', 'update', 'archive', 'restore']]);

        $this->middleware('App\Http\Middleware\Rooms\ViewRoomMiddleware', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::all();

        return view('pages.rooms.rooms', [
            'rooms' => $rooms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locations = Location::all();

        return view('pages.rooms.createroom', [
            'locations' => $locations,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoomStorePost $request)
    {
        $vars = $request->except([]);
        $vars['creator_id'] = $request->user()->id;
        $vars['updater_id'] = $request->user()->id;

        $room = Room::create($vars);

        return response()->json([
            'response' => 1,
            'redirectURL' => route('room.show', $room->id),
            'title' => 'Create room',
            'message' => 'Successfully created Room ' . $room->name
        ]); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = Room::withTrashed()->find($id);
        $locations = Location::all();


        return view('pages.rooms.showroom', [
            'room' => $room,
            'locations' => $locations,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(RoomStorePost $request, $id)
    {
        $room = Room::withTrashed()->findOrFail($id);
        
        $vars = $request->except([]);
        $vars['updater_id'] = $request->user()->id;
        

        /* Update the room */
        $room->update($vars);

        return response()->json([
            'response' => 1,
            'title' => 'Update room details',
            'message' => 'Successfully updated room ' . $room->name
        ]);
    }

    /**
     * Archive the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function archive($id)
    {
        $room = Room::findOrFail($id);


        /* Soft delete group */
        $room->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('room.show', $room->id),
            'title' => 'Archive Room',
            'message' => 'Successfully archived ' . $room->name
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $room = Room::onlyTrashed()->findOrFail($id);


        /* Restore group */
        $room->restore();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('room.show', $room->id),
            'title' => 'Restore Room',
            'message' => 'Successfully restored ' . $room->name
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        //
    }
}
