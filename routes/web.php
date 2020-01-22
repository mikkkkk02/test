<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*
|--------------------------------------------------------------------------
| @Public Routes
|--------------------------------------------------------------------------
|
|	Collection of routes that is accessible to the public. These are
|	mostly splash, error & invalid pages
|
*/
Route::group([], function () {


	Route::get('logout', 'ErrorController@logout')->name('error.logout');

	Route::get('forbidden', 'ErrorController@forbidden')->name('error.forbidden');

	Route::get('unauthorized-access', 'ErrorController@unauthorized')->name('error.unauthorized');

});


/*
|--------------------------------------------------------------------------
| @General Routes
|--------------------------------------------------------------------------
|
|	Collection of routes that is accessible w/ any authentication level
|
*/
Route::group(['middleware' => 'auth'], function () {

	
	/*
	|-----------------------------------------------
	| @Dashboard
	|---------------------------------------------*/
	Route::get('', 'DashboardController@index')->name('dashboard');


	/*
	|-----------------------------------------------
	| @Notifications
	|---------------------------------------------*/
	Route::get('notifications', 'NotificationController@index')->name('notifications');

	Route::post('notifications/mark-as-read', 'NotificationController@markAsRead')->name('notification.read');

	/*
	| @Notifications: Fetching
	|---------------------------------------------*/	
	Route::post('notifications/fetch/q', 'NotificationFetchController@fetch')->name('notification.fetch');
	

	/*
	|-----------------------------------------------
	| @Users
	|---------------------------------------------*/
	Route::post('users/{id}', 'UserController@update')->name('user.edit');

	Route::post('users/import/q', 'UserImportController@import')->name('user.import');


	/*
	|-----------------------------------------------
	| @Groups
	|---------------------------------------------*/
	Route::get('groups', 'GroupController@index')->name('groups');

	Route::get('groups/create', 'GroupController@create')->name('group.create');
	Route::get('groups/{id}', 'GroupController@show')->name('group.show');

	Route::post('groups', 'GroupController@store')->name('group.store');
	Route::post('groups/{id}', 'GroupController@update')->name('group.edit');

	Route::delete('groups/{id}/archive', 'GroupController@archive')->name('group.archive');
	Route::post('groups/{id}/restore', 'GroupController@restore')->name('group.restore');

	/*
	| @Groups: Roles
	|---------------------------------------------*/
	Route::post('groups/{id}/roles/add', 'GroupController@addRole')->name('group.addrole');
	Route::post('groups/{id}/roles/remove', 'GroupController@removeRole')->name('group.removerole');
	Route::post('groups/{id}/roles', 'GroupController@updateRoles')->name('group.editroles');

	/*
	| @Groups: Users
	|---------------------------------------------*/
	Route::post('groups/{id}/users/add', 'GroupController@addUsers')->name('group.addusers');
	Route::post('groups/{id}/users/remove', 'GroupController@removeUsers')->name('group.removeusers');
	Route::post('groups/{id}/users', 'GroupController@updateUsers')->name('group.editusers');	

	/*
	| @Groups: Fetching
	|---------------------------------------------*/
	Route::post('groups/fetch/q', 'GroupFetchController@fetch')->name('group.fetch');

	Route::post('groups/fetch/q?archive=1', 'GroupFetchController@fetch')->name('group.fetcharchive');


	/*
	|-----------------------------------------------
	| @Employees
	|---------------------------------------------*/
	Route::get('employees', 'EmployeeController@index')->name('employees');
	Route::get('employees/register', 'EmployeeController@create')->name('employee.create');
	Route::get('employees/{id}', 'EmployeeController@show')->name('employee.show');

	Route::post('employees', 'EmployeeController@store')->name('employee.store');
	Route::post('employees/{id}', 'EmployeeController@update')->name('employee.edit');

	Route::delete('employees/{id}/archive', 'EmployeeController@archive')->name('employee.archive');
	Route::post('employees/{id}/restore', 'EmployeeController@restore')->name('employee.restore');

	Route::post('employees/{id}/settings', 'EmployeeController@updateSettings')->name('employee.updatesettings');
	Route::post('employees/{id}/upload', 'EmployeeController@uploadAvatar')->name('employee.uploadavatar');

	/*
	| @Employees: Fetching
	|---------------------------------------------*/
	Route::post('employees/fetch/q', 'EmployeeFetchController@fetch')->name('employee.fetch');

	Route::post('employees/fetch/q?archive=1', 'EmployeeFetchController@fetch')->name('employee.fetcharchive');
	Route::post('employees/fetch/q?group={id}', 'EmployeeFetchController@fetch')->name('employee.fetchgroupusers');
	Route::post('employees/fetch/q?xgroup={id}', 'EmployeeFetchController@fetch')->name('employee.fetchnotgroupusers');
	Route::post('employees/fetch/q?company={id}', 'EmployeeFetchController@fetch')->name('employee.fetchcompanyemployees');
	Route::post('employees/fetch/q?division={id}', 'EmployeeFetchController@fetch')->name('employee.fetchdivisionemployees');
	Route::post('employees/fetch/q?department={id}', 'EmployeeFetchController@fetch')->name('employee.fetchdepartmentemployees');
	Route::post('employees/fetch/q?position={id}', 'EmployeeFetchController@fetch')->name('employee.fetchpositionemployees');
	Route::post('employees/fetch/q?xposition={id}', 'EmployeeFetchController@fetch')->name('employee.fetchnotpositionemployees');
	Route::post('employees/fetch/q?team={id}', 'EmployeeFetchController@fetch')->name('employee.fetchteamemployees');
	Route::post('employees/fetch/q?xteam={id}', 'EmployeeFetchController@fetch')->name('employee.fetchnotteamemployees');


	/*
	|-----------------------------------------------
	| @Company
	|---------------------------------------------*/
	Route::get('companies', 'CompanyController@index')->name('companies');
	Route::get('companies/create', 'CompanyController@create')->name('company.create');	
	Route::get('companies/{id}', 'CompanyController@show')->name('company.show');

	Route::post('companies', 'CompanyController@store')->name('company.store');
	Route::post('companies/{id}', 'CompanyController@update')->name('company.edit');

	Route::delete('companies/{id}/archive', 'CompanyController@archive')->name('company.archive');
	Route::post('companies/{id}/restore', 'CompanyController@restore')->name('company.restore');		

	/*
	| @Company: Divisions
	|---------------------------------------------*/
	Route::post('companies/{id}/division/add', 'CompanyController@addDivisions')->name('company.adddivisions');
	Route::post('companies/{id}/division/remove', 'CompanyController@removeDivisions')->name('company.removedivisions');

	/*
	| @Company: Fetching
	|---------------------------------------------*/
	Route::post('companies/fetch/q', 'CompanyFetchController@fetch')->name('company.fetch');

	Route::post('companies/fetch/q?archive=1', 'CompanyFetchController@fetch')->name('company.fetcharchive');

	Route::post('companies/{id}/divisions', 'CompanyFetchController@fetchDivisions')->name('company.fetchdivision');


	/*
	|-----------------------------------------------
	| @Divisions (a.k.a GROUP)
	|---------------------------------------------*/
	Route::get('divisions', 'DivisionController@index')->name('divisions');
	Route::get('divisions/create', 'DivisionController@create')->name('division.create');		
	Route::get('divisions/{id}', 'DivisionController@show')->name('division.show');

	Route::post('divisions', 'DivisionController@store')->name('division.store');
	Route::post('divisions/{id}', 'DivisionController@update')->name('division.edit');

	Route::delete('divisions/{id}/archive', 'DivisionController@archive')->name('division.archive');
	Route::post('divisions/{id}/restore', 'DivisionController@restore')->name('division.restore');			

	/*
	| @Divisions: Company
	|---------------------------------------------*/
	Route::post('divisions/{id}/company/assign', 'DivisionController@assignCompany')->name('division.addcompany');
	Route::post('divisions/{id}/company/unassign', 'DivisionController@unassignCompany')->name('division.removecompany');

	/*
	| @Divisions: Departments
	|---------------------------------------------*/
	Route::post('divisions/{id}/departments/add', 'DivisionController@addDepartments')->name('division.adddepartments');
	Route::post('divisions/{id}/departments/remove', 'DivisionController@removeDepartments')->name('division.removedepartments');

	/*
	| @Divisions: Fetching
	|---------------------------------------------*/
	Route::post('divisions/fetch/q', 'DivisionFetchController@fetch')->name('division.fetch');

	Route::post('divisions/fetch/q?archive=1', 'DivisionFetchController@fetch')->name('division.fetcharchive');

	Route::post('divisions/fetch/q?company={id}', 'DivisionFetchController@fetch')->name('division.fetchcompanydivisions');
	Route::post('divisions/fetch/q?xcompany={id}', 'DivisionFetchController@fetch')->name('division.fetchnotcompanydivisions');

	Route::post('divisions/{id}/departments', 'DivisionFetchController@fetchDepartments')->name('division.fetchdepartments');


	/*
	|-----------------------------------------------
	| @Departments
	|---------------------------------------------*/
	Route::get('departments', 'DepartmentController@index')->name('departments');
	Route::get('departments/create', 'DepartmentController@create')->name('department.create');
	Route::get('departments/{id}', 'DepartmentController@show')->name('department.show');

	Route::post('departments', 'DepartmentController@store')->name('department.store');
	Route::post('departments/{id}', 'DepartmentController@update')->name('department.edit');

	Route::post('departments/{id}/division/assign', 'DivisionController@assignDivision')->name('position.adddivision');
	Route::post('departments/{id}/division/uassign', 'DivisionController@unassignDivision')->name('position.removedivision');	
	
	/*
	| @Departments: Employees
	|---------------------------------------------*/
	Route::post('departments/{id}/employees/add', 'DepartmentController@addEmployees')->name('department.addemployees');
	Route::post('departments/{id}/employees/remove', 'DepartmentController@removeEmployees')->name('department.removeemployees');

	/*
	| @Departments: Positions
	|---------------------------------------------*/
	Route::post('departments/{id}/positions/add', 'DepartmentController@addPositions')->name('department.addpositions');
	Route::post('departments/{id}/positions/remove', 'DepartmentController@removePositions')->name('department.removepositions');	

	/*
	| @Departments: Teams
	|---------------------------------------------*/
	Route::post('departments/{id}/teams/add', 'DepartmentController@addTeams')->name('department.addteams');
	Route::post('departments/{id}/teams/remove', 'DepartmentController@removeTeams')->name('department.removeteams');

	/*
	| @Departments: Fetching
	|---------------------------------------------*/
	Route::post('departments/fetch/q', 'DepartmentFetchController@fetch')->name('department.fetch');

	Route::post('departments/fetch/q?company={id}', 'DepartmentFetchController@fetch')->name('department.fetchcompanydepartments');
	Route::post('departments/fetch/q?division={id}', 'DepartmentFetchController@fetch')->name('department.fetchdivisiondepartments');
	Route::post('departments/fetch/q?xdivision={id}', 'DepartmentFetchController@fetch')->name('department.fetchnotdivisiondepartments');

	Route::post('departments/{id}/positions-teams', 'DepartmentFetchController@fetchPositionsTeams')->name('department.fetchpositionsteams');
	Route::post('departments/{id}/positions', 'DepartmentFetchController@fetchPositions')->name('department.fetchpositions');
	Route::post('departments/{id}/teams', 'DepartmentFetchController@fetchTeams')->name('department.fetchteams');


	/*
	|-----------------------------------------------
	| @Positions
	|---------------------------------------------*/
	Route::get('positions/create', 'PositionController@create')->name('position.create');
	Route::get('positions/{id}', 'PositionController@show')->name('position.show');

	Route::post('positions', 'PositionController@store')->name('position.store');
	Route::post('positions/{id}', 'PositionController@update')->name('position.edit');

	/*
	| @Positions: Employees
	|---------------------------------------------*/
	Route::post('positions/{id}/employees/add', 'PositionController@addEmployees')->name('position.addemployees');
	Route::post('positions/{id}/employees/remove', 'PositionController@removeEmployees')->name('position.removeemployees');

	/*
	| @Positions: Fetching
	|---------------------------------------------*/
	Route::post('positions/fetch/q', 'PositionFetchController@fetch')->name('position.fetch');

	Route::post('positions/fetch/q?department={id}', 'PositionFetchController@fetch')->name('position.fetchdepartmentpositions');
	Route::post('positions/fetch/q?xdepartment={id}', 'PositionFetchController@fetch')->name('position.fetchnotdepartmentpositions');


	/*
	|-----------------------------------------------
	| @Teams
	|---------------------------------------------*/
	Route::get('teams', 'TeamController@index')->name('teams');
	Route::get('teams/create', 'TeamController@create')->name('team.create');
	Route::get('teams/{id}', 'TeamController@show')->name('team.show');

	Route::post('teams', 'TeamController@store')->name('team.store');
	Route::post('teams/{id}', 'TeamController@update')->name('team.edit');

	/*
	| @Teams: Employees
	|---------------------------------------------*/
	Route::post('teams/{id}/employees/add', 'TeamController@addEmployees')->name('team.addemployees');
	Route::post('teams/{id}/employees/remove', 'TeamController@removeEmployees')->name('team.removeemployees');

	/*
	| @Teams: Fetching
	|---------------------------------------------*/
	Route::post('teams/fetch/q', 'TeamFetchController@fetch')->name('team.fetch');

	Route::post('teams/fetch/q?company={id}', 'TeamFetchController@fetch')->name('team.fetchcompanyteams');	
	Route::post('teams/fetch/q?division={id}', 'TeamFetchController@fetch')->name('team.fetchdivisionteams');
	Route::post('teams/fetch/q?department={id}', 'TeamFetchController@fetch')->name('team.fetchdepartmentteams');
	Route::post('teams/fetch/q?xdepartment={id}', 'TeamFetchController@fetch')->name('team.fetchnotdepartmentteams');


	/*
	|-----------------------------------------------
	| @Locations
	|---------------------------------------------*/
	Route::get('locations', 'LocationController@index')->name('locations');
	Route::get('locations/create', 'LocationController@create')->name('location.create');
	Route::get('locations/{id}', 'LocationController@show')->name('location.show');

	Route::post('locations', 'LocationController@store')->name('location.store');
	Route::post('locations/{id}', 'LocationController@update')->name('location.edit');

	Route::delete('locations/{id}/archive', 'LocationController@archive')->name('location.archive');
	Route::post('locations/{id}/restore', 'LocationController@restore')->name('location.restore');	

	/*
	| @Locations: Fetching
	|---------------------------------------------*/
	Route::post('locations/fetch/q', 'LocationFetchController@fetch')->name('location.fetch');
	Route::post('locations/fetch/q?nopagination=1', 'LocationFetchController@fetch')->name('location.fetchnopagination');

	Route::post('locations/fetch/q?archive=1', 'LocationFetchController@fetch')->name('location.fetcharchive');

	Route::post('rooms/fetch/q?location={id}', 'RoomFetchController@fetch')->name('location.fetchrooms');


	/*
	|-----------------------------------------------
	| @Rooms
	|---------------------------------------------*/
	Route::get('rooms', 'RoomController@index')->name('rooms');
	Route::get('rooms/create', 'RoomController@create')->name('room.create');
	Route::get('rooms/{id}', 'RoomController@show')->name('room.show');

	Route::post('rooms', 'RoomController@store')->name('room.store');
	Route::post('rooms/{id}', 'RoomController@update')->name('room.edit');

	Route::delete('rooms/{id}/archive', 'RoomController@archive')->name('room.archive');
	Route::post('rooms/{id}/restore', 'RoomController@restore')->name('room.restore');

	/*
	| @Rooms: Fetching
	|---------------------------------------------*/
	Route::post('rooms/fetch/q', 'RoomFetchController@fetch')->name('room.fetch');

	Route::post('rooms/fetch/q?archive=1', 'RoomFetchController@fetch')->name('room.fetcharchive');

	/*
	|-----------------------------------------------
	| @Events
	|---------------------------------------------*/
	Route::get('events', 'EventController@index')->name('events');
	Route::get('events/my-own', 'EventController@indexMyOwn')->name('events.myown');
	Route::get('events/my-team', 'EventController@indexMyTeam')->name('events.myteam');
	Route::get('events/create', 'EventController@create')->name('event.create');
	Route::get('events/{id}', 'EventController@show')->name('event.show');

	Route::post('events', 'EventController@store')->name('event.store');
	Route::post('events/{id}', 'EventController@update')->name('event.edit');

	Route::post('events/{id}/participants/remove', 'EventController@removeParticipants')->name('event.removeparticipants');
	Route::post('events/{id}/participants/cancel', 'EventController@cancel')->name('event.cancelparticipant');

	/*
	| @Events: Requests
	|---------------------------------------------*/
	Route::get('events/{event}/attend/{form}', 'EventController@createRequest')->name('event.createrequest');

	/*
	| @Events: Fetching
	|---------------------------------------------*/
	Route::post('events/fetch/q', 'EventFetchController@fetch')->name('event.fetch');

	/*
	| @Events: Participant
	|---------------------------------------------*/
	Route::post('event-participants/{id}/approve', 'EventParticipantController@approve')->name('eventparticipant.approve');
	Route::post('event-participants/{id}/disapprove', 'EventParticipantController@disapprove')->name('eventparticipant.disapprove');

	Route::post('event-participants/{id}/attendance/attended', 'EventParticipantController@attended')->name('eventparticipant.attended');
	Route::post('event-participants/{id}/attendance/noshow', 'EventParticipantController@noshow')->name('eventparticipant.noshow');

	/*
	| @Events: Participant: Fetching
	|---------------------------------------------*/
	Route::post('event-participants/fetch/q', 'EventParticipantFetchController@fetch')->name('eventparticipant.fetch');

	Route::post('event-participants/fetch/q?report=1', 'EventParticipantFetchController@fetch')->name('eventparticipant.fetchreport');	

	Route::post('event-participants/fetch/q?user={id}', 'EventParticipantFetchController@fetch')->name('eventparticipant.fetchuserevents');
	Route::post('event-participants/fetch/q?myteam={id}', 'EventParticipantFetchController@fetch')->name('eventparticipant.fetchteamevents');
	Route::post('event-participants/fetch/q?approver={id}', 'EventParticipantFetchController@fetch')->name('eventparticipant.fetchuserapprovalevents');

	Route::post('event-participants/fetch/q?event={id}&participant=1', 'EventParticipantFetchController@fetch')->name('eventparticipant.fetcheventparticipants');
	Route::post('event-participants/fetch/q?event={id}&inqueue=1', 'EventParticipantFetchController@fetch')->name('eventparticipant.fetcheventinqueue');	
	Route::post('event-participants/fetch/q?event={id}&pending=1', 'EventParticipantFetchController@fetch')->name('eventparticipant.fetcheventpendings');	

	/*
	| @Events: Participant: Reporting
	|---------------------------------------------*/
	Route::get('event-participants/report/monitoring', 'EventParticipantReportController@index')->name('eventparticipant.report');

	Route::get('event-participants/report/monitoring/export', 'EventParticipantReportController@export')->name('eventparticipant.export');		


	/*
	|-----------------------------------------------
	| @IDP
	|---------------------------------------------*/
	Route::get('idps', 'IDPController@index')->name('idps');
	Route::get('idps/create', 'IDPController@create')->name('idp.create');
	Route::get('idps/{id}', 'IDPController@show')->name('idp.show');

	Route::post('idps', 'IDPController@store')->name('idp.store');
	Route::post('idps/{id}', 'IDPController@update')->name('idp.edit');

	Route::delete('idps/{id}/delete', 'IDPController@destroy')->name('idp.delete');
	Route::delete('idps/delete-all', 'IDPController@destroyAll')->name('idp.deleteall');

	Route::post('idps/{id}/approve', 'IDPController@approve')->name('idp.approve');
	Route::post('idps/{id}/disapprove', 'IDPController@disapprove')->name('idp.disapprove');	

	Route::post('idps/import/q', 'IDPImportController@import')->name('idp.import');

	/*
	| @IDP: Fetching
	|---------------------------------------------*/
	Route::post('idps/fetch/q', 'IDPFetchController@fetch')->name('idp.fetch');	

	Route::post('idps/fetch/q?user={id}', 'IDPFetchController@fetch')->name('idp.fetchuseridps');
	Route::post('idps/fetch/q?myteam={id}', 'IDPFetchController@fetch')->name('idp.fetchteamidps');

	Route::post('idps/fetch/q?approval={id}', 'IDPFetchController@fetch')->name('idp.fetchuserapprovalidps');

	/*
	| @IDP: Reporting
	|---------------------------------------------*/
	Route::get('idps/report/monitoring', 'IDPReportController@index')->name('idp.report');

	Route::get('idps/report/monitoring/export', 'IDPReportController@export')->name('idp.export');	


	/*
	|-----------------------------------------------	
	| @Temp IDP
	|---------------------------------------------*/
	Route::get('idps-tmp/{id}', 'IDPController@showTemp')->name('idptmp.show');

	// Route::post('idps-tmp/{id}/approve', 'IDPController@approve')->name('idptmp.approve');
	// Route::post('idps-tmp/{id}/disapprove', 'IDPController@disapprove')->name('idptmp.disapprove');
	Route::post('idps-tmp/{id}/transfer', 'IDPController@transfer')->name('idptmp.transfer');

	/*
	| @Temp IDP: Fetching
	|---------------------------------------------*/
	Route::post('idps-tmp/fetch/q', 'TempIDPFetchController@fetch')->name('idptmp.fetchtmpidps');


	/*
	|-----------------------------------------------
	| @L&D
	|---------------------------------------------*/
	Route::get('learnings', 'LearningController@index')->name('learnings');
	Route::get('learnings/my-team', 'LearningController@indexMyTeam')->name('learning.myteam');
	Route::get('learnings/{id}', 'LearningController@show')->name('learning.show');

	// Route::post('learnings', 'LearningController@store')->name('learning.store');
	// Route::post('learnings/{id}', 'LearningController@update')->name('learning.edit');

	// Route::post('learnings/{id}/participants/attend', 'LearningController@attend')->name('learning.attendparticipant');
	// Route::post('learnings/{id}/participants/cancel', 'LearningController@cancel')->name('learning.cancelparticipant');

	// Route::post('learnings/{id}/participants/add', 'LearningController@addParticipants')->name('group.addparticipants');
	// Route::post('learnings/{id}/participants/remove', 'LearningController@removeParticipants')->name('group.removeparticipants');

	/*
	| @L&D: Participant
	|---------------------------------------------*/
	// Route::post('learning-participants/{id}/approve', 'EventParticipantController@approve')->name('learningparticipant.approve');
	// Route::post('learning-participants/{id}/disapprove', 'learningparticipant@disapprove')->name('learningparticipant.disapprove');

	// Route::post('learning-participants/{id}/attendance/update', 'EventParticipant@updateAttendance')->name('learningparticipant.updateattendance');

	/*
	| @L&D: Participant: Fetching
	|---------------------------------------------*/
	// Route::post('learning-participants/fetch/q', 'LearningParticipantFetchController@fetch')->name('learning.fetch');

	// Route::post('learning-participants/fetch/q?user={id}', 'LearningParticipantFetchController@fetch')->name('learning.fetchuserlearnings');

	/*
	|-----------------------------------------------
	| @Learning Nook
	|---------------------------------------------*/
	Route::get('learning-nook', 'LearningNookController@index')->name('learningnook');


	/*
	|-----------------------------------------------
	| @Form Templates
	|---------------------------------------------*/
	Route::get('form-templates', 'FormTemplateController@index')->name('formtemplates');

	Route::get('form-templates/create', 'FormTemplateController@create')->name('formtemplate.create');
	Route::get('form-templates/{id}', 'FormTemplateController@show')->name('formtemplate.show');

	Route::post('form-templates', 'FormTemplateController@store')->name('formtemplate.store');
	Route::post('form-templates/{id}', 'FormTemplateController@update')->name('formtemplate.edit');
	Route::post('form-templates/{id}/approver', 'FormTemplateController@updateApprover')->name('formtemplate.editapprover');

	Route::delete('form-templates/{id}/archive', 'FormTemplateController@archive')->name('formtemplate.archive');
	Route::post('form-templates/{id}/restore', 'FormTemplateController@restore')->name('formtemplate.restore');	

	Route::post('form-templates/{id}/contacts/add', 'FormTemplateController@addContact')->name('formtemplate.addcontact');
	Route::post('form-templates/{id}/contacts/remove', 'FormTemplateController@removeContact')->name('formtemplate.removecontact');

	Route::post('form-templates/{id}/fields/add', 'FormTemplateController@addField')->name('formtemplate.addfield');
	Route::post('form-templates/{id}/fields/remove', 'FormTemplateController@removeField')->name('formtemplate.removefield');
	Route::post('form-templates/{id}/fields/sorting', 'FormTemplateController@updateSorting')->name('formtemplate.updatesorting');

	Route::post('form-templates/{id}/approvers/add', 'FormTemplateController@addApprover')->name('formtemplate.addapprover');
	Route::post('form-templates/{id}/approvers/remove', 'FormTemplateController@removeApprover')->name('formtemplate.removeapprover');

	/*
	| @Form Templates: Fetching
	|---------------------------------------------*/
	Route::post('form-templates/fetch/q', 'FormTemplateFetchController@fetch')->name('formtemplate.fetch');

	Route::post('form-templates/fetch/q?archive=1', 'FormTemplateFetchController@fetch')->name('formtemplate.fetcharchive');

	Route::post('form-templates/{id}/fetch/contacts', 'FormTemplateFetchController@fetchContacts')->name('formtemplate.fetchcontacts');
	Route::post('form-templates/{id}/fetch/fields', 'FormTemplateFetchController@fetchFields')->name('formtemplate.fetchfields');
	Route::post('form-templates/{id}/fetch/approvers', 'FormTemplateFetchController@fetchApprovers')->name('formtemplate.fetchapprovers');

	Route::post('form-templates/{id}/sla/fetch/available-fields', 'FormTemplateFetchController@fetchAvailableDatefields')->name('formtemplate.availabledatefields');
	Route::post('form-templates/{id}/travelorder/fetch/available-tables', 'FormTemplateFetchController@fetchAvailableTablefields')->name('formtemplate.availabletablefields');
	
	Route::post('form-templates/fetch/forms', 'FormTemplateFetchController@fetchForms')->name('formtemplate.fetchforms');

	/*
	| @Form Templates: Contacts
	|---------------------------------------------*/
	//

	/*
	| @Form Templates: Fields
	|---------------------------------------------*/
	Route::post('form-template-fields/{id}/update', 'FormTemplateFieldController@update')->name('formtemplatefield.edit');
	Route::post('form-template-fields/{id}/delete', 'FormTemplateFieldController@destroy')->name('formtemplatefield.delete');

	Route::post('form-template-fields/{id}/options/add', 'FormTemplateFieldController@addOption')->name('formtemplatefield.addoption');	
	Route::post('form-template-fields/{id}/options/remove', 'FormTemplateFieldController@removeOption')->name('formtemplatefield.removeoption');	

	/*
	| @Form Templates: Fields: Options
	|---------------------------------------------*/
	Route::post('form-template-options/{id}/update', 'FormTemplateOptionController@update')->name('formtemplateoption.update');


	/*
	|-----------------------------------------------
	| @Forms
	|---------------------------------------------*/
	Route::post('forms', 'FormController@index')->name('forms');


	/*
	|-----------------------------------------------
	| @Benefits
	|---------------------------------------------*/
	Route::get('benefits', 'BenefitController@index')->name('benefits');

	Route::post('benefits/fetch/forms', 'BenefitController@fetchForms')->name('benefit.fetchforms');


	/*
	|-----------------------------------------------
	| @MeetingRooms
	|---------------------------------------------*/
	Route::get('mr-reservations', 'MrReservationController@index')->name('mrreservations');
	Route::get('mr-reservations/create', 'MrReservationController@create')->name('mrreservation.create');
	Route::get('mr-reservations/{id}', 'MrReservationController@show')->name('mrreservation.show');

	Route::post('mr-reservations', 'MrReservationController@store')->name('mrreservation.store');
	Route::post('mr-reservations/{id}', 'MrReservationController@update')->name('mrreservation.edit');

	Route::delete('mr-reservations/{id}/archive', 'MrReservationController@archive')->name('mrreservation.archive');
	Route::post('mr-reservations/{id}/restore', 'MrReservationController@restore')->name('mrreservation.restore');

	/*
	| @Rooms: Fetching
	|---------------------------------------------*/
	Route::post('mr-reservations/fetch/q', 'MrReservationFetchController@fetch')->name('mrreservation.fetch');

	Route::post('mr-reservations/fetch/q?archive=1', 'MrReservationFetchController@fetch')->name('mrreservation.fetcharchive');


	/*
	|-----------------------------------------------
	| @Requests
	|---------------------------------------------*/
	Route::get('requests', 'RequestController@index')->name('requests');
	Route::get('requests/{id}', 'RequestController@show')->name('request.show');

	Route::get('requests/create/{id?}', 'RequestController@create')->name('request.create');
	Route::get('requests/create2/{id?}', 'RequestController@createStep2')->name('request.create.step2');
	Route::get('requests/resubmit/{id}', 'RequestController@resubmit')->name('request.resubmit');

	Route::post('requests/{id?}', 'RequestController@store')->name('request.store');
	Route::post('requests-store1/{id?}', 'RequestController@validateStep1')->name('request.validate.step1');

	Route::post('requests/{templateID}/form/{formID}', 'RequestController@update')->name('request.edit');

	Route::delete('requests/{id}/archive', 'RequestController@archive')->name('request.archive');
	Route::post('requests/{id}/restore', 'RequestController@restore')->name('request.restore');		

	/*
	| @Requests: Attachments
	|---------------------------------------------*/
	Route::post('requests/{id}/attachments/add', 'RequestController@addAttachment')->name('request.addattachment');
	Route::post('requests/{id}/attachments/remove', 'RequestController@removeAttachment')->name('request.removeattachment');

	/*
	| @Requests: Approvals
	|---------------------------------------------*/
	Route::post('requests/{id}/approve', 'RequestController@approve')->name('request.approve');
	Route::post('requests/{id}/disapprove', 'RequestController@disapprove')->name('request.disapprove');

	Route::get('requests/{id}/approve', 'RequestController@approve')->name('request.emailapprove');
	Route::get('requests/{id}/disapprove', 'RequestController@disapprove')->name('request.emaildisapprove');

	Route::post('requests/{id}/withdraw', 'RequestController@withdraw')->name('request.withdraw');
	Route::post('requests/{id}/withdrawAll', 'RequestController@withdrawAll')->name('request.withdrawAll');

	/*
	| @Requests: Updates
	|---------------------------------------------*/
	Route::post('requests/{id}/update', 'RequestController@addUpdate')->name('request.addupdate');
	Route::post('requests/{id}/update/remove', 'RequestController@removeUpdate')->name('request.removeupdate');


	/*
	| @Requests: Fetching
	|---------------------------------------------*/
	Route::post('requests/fetch/q', 'RequestFetchController@fetch')->name('request.fetch');

	Route::post('requests/fetch/q?user={id}&xcategory=' . App\FormTemplateCategory::EVENT, 'RequestFetchController@fetch')->name('request.fetchuserrequest');
	Route::post('requests/fetch/q?user={id}&archive=1&xcategory=' . App\FormTemplateCategory::EVENT, 'RequestFetchController@fetch')->name('request.fetchuserrequestarchive');
	Route::post('requests/fetch/q?user={id}&category=' . App\FormTemplateCategory::LD, 'RequestFetchController@fetch')->name('request.fetchuserldrequest');
	Route::post('requests/fetch/q?myteam={id}&category=' . App\FormTemplateCategory::LD, 'RequestFetchController@fetch')->name('request.fetchteamldrequest');

	Route::post('requests/fetch/q?user={id}&ongoing=1', 'RequestFetchController@fetch')->name('request.fetchuserongoingrequest');

	Route::post('requests/fetch/q?report=1&type=' . App\FormTemplate::ADMIN, 'RequestFetchController@fetch')->name('request.fetchadminrequest');
	Route::post('requests/fetch/q?report=1&type=' . App\FormTemplate::HR, 'RequestFetchController@fetch')->name('request.fetchhrrequest');
	Route::post('requests/fetch/q?type=' . App\FormTemplate::OD, 'RequestFetchController@fetch')->name('request.fetchorrequest');

	Route::post('requests/fetch/q?status=' . App\Form::PENDING, 'RequestFetchController@fetch')->name('request.fetchpendingrequest');

	Route::post('requests/fetch/q?category=' . App\FormTemplateCategory::LD, 'RequestFetchController@fetch')->name('request.fetchldrequest');

	Route::post('requests/fetch/q?approval={id}', 'RequestFetchController@fetch')->name('request.fetchuserapprovalrequest');
	Route::post('requests/fetch/q?approval={id}&category=' . App\FormTemplateCategory::LD, 'RequestFetchController@fetch')->name('request.fetchuserldapprovalrequest');

	Route::post('requests/{id}/updates', 'RequestFetchController@fetchUpdates')->name('request.fetchupdates');

	Route::post('requests/{id}/fetch/answers', 'RequestFetchController@fetchAnswer')->name('request.fetchanswers');
	Route::post('requests/{id}/fetch/attachments', 'RequestFetchController@fetchAttachments')->name('request.fetchattachments');	

	/*
	| @Requests Logs: Fetching
	|---------------------------------------------*/
	Route::post('requests/logs/fetch/q', 'RequestLogFetchController@fetch')->name('requestlog.fetch');

	/*
	| @Requests: Report
	|---------------------------------------------*/
	Route::get('requests/admin/report/monitoring', 'RequestReportController@adminIndex')->name('request.adminreport');
	Route::get('requests/hr/report/monitoring', 'RequestReportController@hrIndex')->name('request.hrreport');
	Route::get('requests/l&d/report/monitoring', 'RequestReportController@ldIndex')->name('request.l&dreport');

	Route::get('requests/report/monitoring/export', 'RequestReportController@export')->name('request.export');
	Route::get('requests/report/monitoring/export?type=' . App\FormTemplate::ADMIN, 'RequestReportController@export')->name('request.exportadmin');
	Route::get('requests/report/monitoring/export?type=' . App\FormTemplate::HR, 'RequestReportController@export')->name('request.exporthr');
	Route::get('requests/report/monitoring/export?category=' . App\FormTemplateCategory::LD, 'RequestReportController@export')->name('request.exportld');


	/*
	|-----------------------------------------------
	| @Request Updates
	|---------------------------------------------*/
	Route::get('request-updates', 'TempRequestController@index')->name('temprequests');
	Route::get('request-updates/{id}', 'TempRequestController@show')->name('temprequest.show');

	Route::post('request-updates/{id}/approve', 'TempRequestController@approve')->name('temprequest.approve');
	Route::post('request-updates/{id}/disapprove', 'TempRequestController@disapprove')->name('temprequest.disapprove');	

	/*
	|-----------------------------------------------
	| @Request Updates: Fetching
	|---------------------------------------------*/
	Route::post('request-updates/q', 'TempRequestFetchController@fetch')->name('temprequest.fetch');

	Route::post('request-updates/q?approval={id}&status=' . App\TempForm::PENDING, 'TempRequestFetchController@fetch')->name('temprequest.fetchapproval');
	Route::post('request-updates/q?status=' . App\TempForm::PENDING, 'TempRequestFetchController@fetch')->name('temprequest.fetchpending');
	Route::post('request-updates/q?done=1', 'TempRequestFetchController@fetch')->name('temprequest.fetchdone');

	Route::post('request-updates/{id}/fetch/answers', 'TempRequestFetchController@fetchAnswer')->name('temprequest.fetchanswers');	


	/*
	|-----------------------------------------------
	| @Request Histories
	|---------------------------------------------*/
	Route::get('request-history/{id}', 'RequestHistoryController@show')->name('requesthistory.show');	

	/*
	|-----------------------------------------------
	| @Request Histories: Fetching
	|---------------------------------------------*/
	Route::post('request-history/q', 'RequestHistoryFetchController@fetch')->name('requesthistory.fetch');
	Route::post('request-history/q?request={id}', 'RequestHistoryFetchController@fetch')->name('requesthistory.fetch');



	/*
	|-----------------------------------------------
	| @Approvals
	|---------------------------------------------*/
	Route::get('approvals', 'ApprovalController@index')->name('approvals');


	/*
	|-----------------------------------------------
	| @Forms
	|---------------------------------------------*/
	//
	

	/*
	|-----------------------------------------------
	| @Tickets
	|---------------------------------------------*/

	/*
	|-----------------------------------------------
	| testTickets
	|---------------------------------------------*/
	Route::get('tickets', 'TestTicketController@index')->name('tickets');
	Route::post('tickets/alldatas', 'TestTicketFetchController@datatable')->name('datatable');


	// Route::get('tickets', 'TicketController@index')->name('tickets');
	Route::get('tickets/{id}', 'TicketController@show')->name('ticket.show');

	Route::post('tickets/{id}/technician', 'TicketController@updateTechnician')->name('ticket.updatetechnician');

	/*
	| @Tickets: Attachments
	|---------------------------------------------*/
	Route::post('tickets/{id}/attachments/add', 'TicketController@addAttachment')->name('ticket.addattachment');
	Route::post('tickets/{id}/attachments/remove', 'TicketController@removeAttachment')->name('ticket.removeattachment');

	/*
	| @Tickets: Updates
	|---------------------------------------------*/
	Route::post('tickets/{id}/update', 'TicketController@addUpdate')->name('ticket.addupdate');
	Route::post('tickets/{id}/update/remove', 'TicketController@removeUpdate')->name('ticket.removeupdate');
	Route::post('tickets/{id}/form/{formID}/update-travel-order', 'TicketController@updateTravelOrder')->name('ticket.updatetravelorder');
	
	Route::post('tickets/{id}/travel-order-details/new', 'TicketController@addTravelOrderDetail')->name('ticket.addtravelorderdetails');
	Route::post('tickets/{id}/travel-order-details/{travelID}/edit', 'TicketController@updateTravelOrderDetail')->name('ticket.updatetravelorderdetails');
	Route::post('tickets/{id}/travel-order-details/{travelID}/remove', 'TicketController@removeTravelOrderDetail')->name('ticket.removetravelorderdetails');
	Route::post('tickets/mr-reservations/{mrReservationId}/edit', 'TicketController@updateRoomDetails')->name('ticket.updateroomdetails');

	/*
	| @Tickets: Fetching
	|---------------------------------------------*/
	// Route::post('tickets/fetch/q', 'TicketFetchController@fetch')->name('ticket.fetch');

	// Route::post('tickets/fetch/q?all=1', 'TicketFetchController@fetch')->name('ticket.fetchall');
	// Route::post('tickets/fetch/q?ongoing=1', 'TicketFetchController@fetch')->name('ticket.fetchongoing');
	// Route::post('tickets/fetch/q?status=' . App\Ticket::CANCELLED, 'TicketFetchController@fetch')->name('ticket.fetchcancelled');
	// Route::post('tickets/fetch/q?status=' . App\Ticket::CLOSE, 'TicketFetchController@fetch')->name('ticket.fetchcompleted');

	Route::post('tickets/fetch/q?user={id}&ongoing=1', 'TicketFetchController@fetch')->name('ticket.fetchuserongoing');
	Route::post('tickets/fetch/q?user={id}&status=' . App\Ticket::CANCELLED, 'TicketFetchController@fetch')->name('ticket.fetchusercancelled');
	Route::post('tickets/fetch/q?user={id}&status=' . App\Ticket::CLOSE, 'TicketFetchController@fetch')->name('ticket.fetchusercompleted');

	Route::post('tickets/{id}/updates', 'TicketFetchController@fetchUpdates')->name('ticket.fetchupdates');

	Route::post('tickets/{id}/fetch/attachments', 'TicketFetchController@fetchAttachments')->name('ticket.fetchattachments');		
	Route::post('tickets/{ticket}/fetch/travel-order-details', 'TicketFetchController@fetchTravelOrderDetails')->name('ticket.fetchtravelorderdetails');		

	/*
	| @Tickets: Reports
	|---------------------------------------------*/
	Route::post('tickets/generate/percent', 'TicketReportController@generatePercent')->name('ticket.generatepercent');
	Route::post('tickets/generate/piechart', 'TicketReportController@generatePiechart')->name('ticket.generatepiechart');
	Route::post('tickets/generate/barchart', 'TicketReportController@generateBarchart')->name('ticket.generatebarchart');

	/*
	|-----------------------------------------------
	| @Ticket Updates
	|---------------------------------------------*/	
	// Route::get('ticket-updates', 'TempTicketUpdateController@index')->name('tempticketupdates');
	// Route::get('ticket-updates/{id}', 'TempTicketUpdateController@show')->name('tempticketupdate.show');

	// Route::post('ticket-updates/{id}/approve', 'TempTicketUpdateController@approve')->name('tempticketupdate.approve');
	// Route::post('ticket-updates/{id}/disapprove', 'TempTicketUpdateController@disapprove')->name('tempticketupdate.disapprove');

	/*
	| @Ticket Updates: Fetching
	|---------------------------------------------*/
	// Route::post('ticket-updates/fetch/q', 'TempTicketUpdateFetchController@fetch')->name('tempticketupdate.fetch');	

	// Route::post('ticket-updates/fetch/q?archive=1', 'TempTicketUpdateFetchController@fetch')->name('tempticketupdate.fetcharchive');	

	// Route::post('ticket-updates/fetch/q?done=1', 'TempTicketUpdateFetchController@fetch')->name('tempticketupdate.fetchdone');	


	/*
	|-----------------------------------------------
	| @Government Forms
	|---------------------------------------------*/
	Route::get('government-forms', 'GovernmentFormController@index')->name('governmentforms');
	Route::get('government-forms/create', 'GovernmentFormController@create')->name('governmentform.create');
	Route::get('government-forms/{id}', 'GovernmentFormController@show')->name('governmentform.show');

	Route::post('government-forms', 'GovernmentFormController@store')->name('governmentform.store');
	Route::post('government-forms/{id}', 'GovernmentFormController@update')->name('governmentform.edit');

	Route::delete('government-forms/{id}/archive', 'GovernmentFormController@archive')->name('governmentform.archive');
	Route::post('government-forms/{id}/restore', 'GovernmentFormController@restore')->name('governmentform.restore');	

	/*
	|-----------------------------------------------
	| @Government Form: Attachments
	|---------------------------------------------*/
	Route::post('government-forms/{id}/attachments/add', 'GovernmentFormController@addAttachment')->name('governmentform.addattachment');

	/*
	| @Government Forms: Fetching
	|---------------------------------------------*/
	Route::post('government-forms/fetch/q', 'GovernmentFormFetchController@fetch')->name('governmentform.fetch');

	Route::post('government-forms/fetch/q?archive=1', 'GovernmentFormFetch@fetch')->name('governmentform.fetcharchive');


	/*
	|-----------------------------------------------
	| @Meeting Room
	|---------------------------------------------*/
	Route::post('meeting-room/check-availability', 'MeetingRoomController@checkAvailability')->name('meetingroom.checkavailability');


	/*
	|-----------------------------------------------
	| @Temporary Attachments
	|---------------------------------------------*/
	Route::post('temp/attachments/add', 'TempAttachmentController@addAttachment')->name('temp.addattachment');
	Route::post('temp/attachments/remove', 'TempAttachmentController@removeAttachment')->name('temp.removeattachment');


	/*
	|-----------------------------------------------
	| @Settings
	|---------------------------------------------*/
	Route::get('settings', 'SettingsController@index')->name('settings');

	Route::post('settings/delegate', 'SettingsController@delegate')->name('settings.delagate');
	Route::post('settings/ticket', 'SettingsController@ticket')->name('settings.ticket');
	Route::post('settings/others', 'SettingsController@update')->name('settings.update');


});


/*
|--------------------------------------------------------------------------
| @Authentication Routes
|--------------------------------------------------------------------------
|
|	Collection of routes that handles all authentication.
|
*/
Route::group(['prefix' => 'auth'], function () {


	/*
	| @Logout
	|---------------------------------------------*/
	Route::get('logout', 'Auth\LogoutController@logout')->name('logout');


	/*
	|-----------------------------------------------
	| @Google
	|---------------------------------------------*/
	Route::get('google', 'Auth\AuthGoogleController@redirectToProvider')->name('google.auth');
	Route::get('google/callback', 'Auth\AuthGoogleController@handleProviderCallback')->name('google.callback');	


});


/*
|--------------------------------------------------------------------------
| @Developer Mode
|--------------------------------------------------------------------------
|
|	Collection of routes that are used to easily edit or customize
|	important system elements for debugging purposes only!
|
*/
Route::group(['prefix' => 'dev', 'middleware' => 'App\Http\Middleware\DeveloperMiddleware'], function () {


	Route::post('switch-account', 'DeveloperController@switchAccount')->name('dev.switchaccount');


});


/*
|--------------------------------------------------------------------------
| @Test Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'test'], function () {

	//	

	// Route::get('/request-has-approver', function () {
	//     $form = App\Form::find(245);
	//     $approver = App\FormApprover::find(287);

	// 	$message = (new App\Notifications\Requests\RequestHasApprover($form, $approver))->toMail('test@email.com');
	    
	//     $markdown = new \Illuminate\Mail\Markdown(view(), config('mail.markdown'));

	//     return $markdown->render('vendor.notifications.email', $message->toArray());
	// });

});