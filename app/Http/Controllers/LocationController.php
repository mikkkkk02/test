<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Locations\LocationStorePost;

use App\Location;
use App\Room;

class LocationController extends Controller
{
    /**
     * Instantiate a new LocationController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Locations\LocationIndexMiddleware', ['only' => ['index', 'create']]);
        
        $this->middleware('App\Http\Middleware\Locations\EditLocationMiddleware', ['only' => ['store', 'update', 'archive', 'restore']]);

        $this->middleware('App\Http\Middleware\Locations\ViewLocationMiddleware', ['only' => ['show', 'update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.locations.locations');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = \Auth::user();

        $rooms = Room::all();

        return view('pages.locations.createlocation', [
        	'rooms' => $rooms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationStorePost $request)
    {
        $vars = $request->except(['rooms']);
        $rooms = null;

        /* Create the location */
        $location = Location::create($vars);

        if ($request->has('rooms')) {
            $rooms = Room::whereIn('id', $request->input('rooms'))->get();
        }

        if ($rooms) {
            $location->rooms()->saveMany($rooms);
        }

        return response()->json([
            'response' => 1,
            'redirectURL' => route('location.show', $location->id),
            'title' => 'Create location',
            'message' => 'Successfully created Location ' . $location->name
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
        $user = \Auth::user();
        $location = Location::withTrashed()->findOrFail($id);
        $rooms = Room::all();

        return view('pages.locations.showlocation', [
            'location' => $location,
            'rooms' => $rooms,
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LocationStorePost $request, $id)
    {
        $location = Location::withTrashed()->findOrFail($id);
        $rooms = null;
        
        $vars = $request->except(['rooms']);
        
        /* Update the location */
        $location->update($vars);

        if ($request->has('rooms')) {
            $rooms = Room::whereIn('id', $request->input('rooms'))->get();
        }

        if ($rooms) {
            $location->rooms()->saveMany($rooms);
        }

        return response()->json([
            'response' => 1,
            'title' => 'Update location details',
            'message' => 'Successfully updated location ' . $location->name
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
        $location = Location::findOrFail($id);


        /* Soft delete group */
        $location->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('location.show', $location->id),
            'title' => 'Archive Location',
            'message' => 'Successfully archived ' . $location->name
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
        $location = Location::onlyTrashed()->findOrFail($id);


        /* Restore group */
        $location->restore();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('location.show', $location->id),
            'title' => 'Restore Location',
            'message' => 'Successfully restored ' . $location->name
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
