<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\Groups\GroupStorePost;
use App\Http\Requests\Groups\GroupUpdatePost;
use App\Http\Requests\Groups\GroupUpdateRolePost;
use App\Http\Requests\Groups\GroupUpdateUserPost;
use App\Http\Requests\Groups\GroupAssignRolePost;
use App\Http\Requests\Groups\GroupAssignUserPost;

use App\Group;
use App\Company;
use App\RoleCategory;

class GroupController extends Controller
{
    /**
     * Instantiate a new DivisionController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('App\Http\Middleware\Groups\GroupIndexMiddleware');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.groups.groups');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::get(Company::MINIMAL_COLUMNS);
        $types = Group::getType();


        return view('pages.groups.creategroup', [
            'companies' => $companies,
            'types' => $types,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupStorePost $request)
    {
        /* Create the group */
        $group = Group::create($request->all());


        return response()->json([
            'response' => 1,
            'redirectURL' => route('group.show', $group->id),
            'title' => 'Create Responsibility',
            'message' => 'Successfully created responsibility ' . $group->name
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
        $group = Group::withTrashed()->findOrFail($id);
        $companies = Company::get(Company::MINIMAL_COLUMNS);

        $categories = RoleCategory::select(RoleCategory::MINIMAL_COLUMNS)->get();
        $types = Group::getType();


        return view('pages.groups.showgroup', [
            'group' => $group,
            'companies' => $companies,
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
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
    public function update(GroupUpdatePost $request, $id)
    {
        $group = Group::withTrashed()->findOrFail($id);
        

        /* Update the group */
        $group->update($request->all());

        return response()->json([
            'response' => 1,
            'title' => 'Update responsibility details',
            'message' => 'Successfully updated responsibility ' . $group->name
        ]);
    }

    /**
     * Assign a role to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addRole(GroupAssignRolePost $request, $id)
    {
        $group = Group::withTrashed()->findOrFail($id);
        $role = Role::findOrFail($request->input('role_id'));


        /* Add role to group */
        $group->addRole($role);

        return response()->json([
            'response' => 1,
            'message' => 'Successfully added ' . $role->renderFullname() . ' to the responsibility'  
        ]);
    }

    /**
     * Remove a role to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeRole(GroupAssignRolePost $request, $id)
    {
        $group = Group::withTrashed()->findOrFail($id);
        $role = Role::findOrFail($request->input('role_id'));


        /* Remove role to group */
        $group->removeRole($role);

        return response()->json([
            'response' => 1,
            'message' => 'Successfully removed ' . $role->renderFullname() . ' to the responsibility'  
        ]);
    }

    /**
     * Update the specified resource's Roles in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRoles(GroupUpdateRolePost $request, $id)
    {
        $group = Group::withTrashed()->findOrFail($id);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update the group roles */
        $group->updateRoles($request->input('roles'));


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'title' => 'Update roles',
            'message' => 'Successfully updated responsibility ' . $group->name
        ]);
    }

    /**
     * Assign a user to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addUsers(GroupUpdateUserPost $request, $id)
    {
        $group = Group::withTrashed()->findOrFail($id);
        $users = $request->input('users');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Add user to group */
        $group->addUsers($users);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('group.show', $group->id),
            'title' => 'Add users',
            'message' => 'Successfully added ' . count($users) . ' user(s) to ' . $group->name  
        ]);
    }

    /**
     * Remove a user to the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeUsers(GroupUpdateUserPost $request, $id)
    {
        $group = Group::withTrashed()->findOrFail($id);
        $users = $request->input('users');


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Remove user to group */
        $group->removeUsers($users);


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'redirectURL' => route('group.show', $group->id),
            'title' => 'Remove users',
            'message' => 'Successfully removed ' . count($users) . ' user(s) to ' . $group->name  
        ]);
    }

    /**
     * Update the specified resource's Users in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUsers(GroupUpdateUserPost $request, $id)
    {
        $group = Group::withTrashed()->findOrFail($id);


        /*
        | @Begin Transaction
        |---------------------------------------------*/
        \DB::beginTransaction();


        /* Update the group users */
        $group->updateUsers($request->input('users'));


        /*
        | @End Transaction
        |---------------------------------------------*/
        \DB::commit();


        return response()->json([
            'response' => 1,
            'message' => 'Successfully updated responsibility ' . $group->name
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
        $group = Group::findOrFail($id);


        /* Soft delete group */
        $group->delete();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('group.show', $group->id),
            'title' => 'Archive Responsibility',
            'message' => 'Successfully archived ' . $group->name
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
        $group = Group::onlyTrashed()->findOrFail($id);


        /* Restore group */
        $group->restore();

        return response()->json([
            'response' => 1,
            'redirectURL' => route('group.show', $group->id),
            'title' => 'Restore Responsibility',
            'message' => 'Successfully restored ' . $group->name
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
