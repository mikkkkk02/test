<aside class="main-sidebar custom-sidebar">
    <div class="sidebar custom-sidebar" id="scrollspy">
        
        <a href="{{ route('employee.show', $self->id) }}">
            <div class="user-panel">
                    <div class="pull-left image">
                        <img src="{{ $self->getProfilePhoto() }}" class="img-circle" alt="User Image">
                    </div>
                    <div class="pull-left info">
                        <p>{{ $self->renderFullname() }}</p>
                    </div>
            </div>
        </a>


        <ul class="nav sidebar-menu">
            {{-- <li class="header">Main Navigation</li> --}}
            <li class=""></li>

            <li class="{{ $checker->isOnRoute('dashboard') }}">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('image/icons/layout-grid-edit-adjust-organize%402x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-th"></i> --}}
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- <li class="{{ $checker->isOnroute('employee.show') }}">
                <a href="{{ route('employee.show', $self->id) }}">
                    <i class="fa fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li> --}}

            @if($checker->hasModuleRoles([
                'Adding/Editing of Employee Profile', 
                'Adding/Editing of Company', 
                'Adding/Editing of Group', 
                'Adding/Editing of Department', 
                'Adding/Editing of Teams', 
                'Adding/Editing of Meeting Location',
                'Adding/Editing of Meeting Rooms'
            ]))
            <li class="treeview {{ $checker->areOnRoutes([
                                    'employees', 'employee.create', 'employee.show',
                                    'companies', 'company.create', 'company.show',
                                    'divisions', 'division.create', 'division.show',
                                    'departments', 'department.create', 'department.show', 'position.create', 'position.show',
                                    'teams', 'team.create', 'team.show',
                                    'locations', 'location.create', 'location.show',
                                    'rooms', 'room.create', 'room.show'
                                ]) }}">
                <a href="#">
                    <img src="{{ asset('image/icons/organization-chart-diagram-hierachy-system%402x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-sitemap"></i> --}}
                    <span>Organization</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">

                    @if($checker->hasModuleRoles(['Adding/Editing of Employee Profile']))
                    <li class="{{ $checker->areOnRoutes(['employees', 'employee.create', 'employee.show']) }}">
                        <a href="{{ route('employees') }}">
                            <i class="fa fa-users"></i>
                            <span>Employees</span>
                        </a>
                    </li>
                    @endif

                    @if($checker->hasModuleRoles(['Adding/Editing of Company']))
                    <li class="{{ $checker->areOnRoutes(['companies', 'company.create', 'company.show']) }}">
                        <a href="{{ route('companies') }}">
                            <i class="fa fa-building-o"></i>
                            <span>Companies</span>
                        </a>
                    </li>
                    @endif                    

                    @if($checker->hasModuleRoles(['Adding/Editing of Group']))
                    <li class="{{ $checker->areOnRoutes(['divisions', 'division.create', 'division.show']) }}">
                        <a href="{{ route('divisions') }}">
                            <i class="fa fa-industry"></i>
                            <span>Groups</span>
                        </a>
                    </li>
                    @endif

                    @if($checker->hasModuleRoles(['Adding/Editing of Department']))
                    <li class="{{ $checker->areOnRoutes(['departments', 'department.create', 'department.show', 'position.create', 'position.show']) }}">
                        <a href="{{ route('departments') }}">
                            <i class="fa fa-home"></i>
                            <span>Departments</span>
                        </a>
                    </li>
                    @endif

                    @if($checker->hasModuleRoles(['Adding/Editing of Teams']))            
                    <li class="{{ $checker->areOnRoutes(['teams', 'team.create', 'team.show']) }}">
                        <a href="{{ route('teams') }}">
                            <i class="fa fa-users"></i>
                            <span>Teams</span>
                        </a>
                    </li>
                    @endif

                    @if($checker->hasModuleRoles(['Adding/Editing of Meeting Locations']))            
                    <li class="{{ $checker->areOnRoutes(['locations', 'location.create', 'location.show']) }}">
                        <a href="{{ route('locations') }}">
                            <i class="fa fa-map-marker"></i>
                            <span>Locations</span>
                        </a>
                    </li>
                    @endif
                    
                    @if($checker->hasModuleRoles(['Adding/Editing of Meeting Rooms', 'Viewing of Meeting Room Reservations']))
                    <li class="{{ $checker->areOnRoutes(['rooms', 'room.create', 'room.show']) }}">
                        <a href="{{ route('rooms') }}">
                            <i class="fa fa-cube"></i>
                            <span>Rooms</span>
                        </a>
                    </li>
                    @endif

                </ul>
            </li>
            @endif     

            <li class="treeview {{ $checker->areOnRoutes([
                                    'events', 'event.create', 'event.show', 'event.createrequest',
                                    'events.myown', 'events.myteam', 'showevent'
                                ]) }}">
                <a href="#">
                    <img src="{{ asset('image/icons/calendar-month-day-booking-date%402x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-calendar"></i> --}}
                    <span>Calendar</span>

                    @if($eventCount)
                    <small class="label notif pull-right bg-red">{{ $eventCount }}</small>
                    @else
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    @endif
                </a>
                <ul class="treeview-menu">
                    <li class="{{ $checker->areOnRoutes(['events', 'event.create', 'event.show', 'event.createrequest']) }}">
                        <a href="{{ route('events') }}"><i class="fa fa-calendar-check-o"></i> Events</a>
                    </li>
                    <li class="{{ $checker->areOnRoutes(['events.myown']) }}">
                        <a href="{{ route('events.myown') }}"><i class="fa fa-calendar-check-o"></i> My Events</a>
                    </li>
                    <li class="{{ $checker->areOnRoutes(['events.myteam']) }}">
                        <a href="{{ route('events.myteam') }}"><i class="fa fa-calendar-check-o"></i> My Team's Events</a>
                    </li>
                </ul>
            </li>

            <li class="treeview {{ $checker->areOnRoutes(['idps', 'idp.create', 'idp.show', 'learnings', 'learning.create', 'learning.show' ,'learning.myteam', 'learningnook']) }}">
                <a href="#">
                    <img src="{{ asset('image/icons/puzzle-solution-problem-challenge-resolution%402x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-puzzle-piece"></i> --}}
                    <span>Learning & Development</span>

                    @if($idpCount)
                    <small class="label notif pull-right bg-red">{{ $idpCount }}</small>
                    @else
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    @endif
                </a>
                <ul class="treeview-menu">
                    <li class="{{ $checker->areOnRoutes(['learnings', 'learning.create', 'learning.show']) }}">
                        <a href="{{ route('learnings') }}">
                            <img src="{{ asset('image/icons/content-form-contract-article-paper@2x.png') }}" class="icon--dashboard">
                            {{-- <i class="fa fa-circle-o"></i> --}}
                            Learning and Development Form
                        </a>
                    </li>
                    <li class="{{ $checker->areOnRoutes(['learningnook']) }}">
                        <a href="{{ route('learningnook') }}">
                            <img src="{{ asset('image/icons/content-form-application-article-paper%402x.png') }}" class="icon--dashboard">
                            {{-- <i class="fa fa-circle-o"></i> --}}
                            Learning Nook
                        </a>
                    </li>                    
                    <li class="{{ $checker->areOnRoutes(['idps', 'idp.create', 'idp.show']) }}">
                        <a href="{{ route('idps') }}">
                            <img src="{{ asset('image/icons/content-form-contract-article-paper@2x.png') }}" class="icon--dashboard">
                            {{-- <i class="fa fa-circle-o"></i>  --}}
                            My Individual Development Plan
                        </a>
                    </li>
                    <li class="{{ $checker->areOnRoutes(['learning.myteam']) }}">
                        <a href="{{ route('learning.myteam') }}">
                            <img src="{{ asset('image/icons/content-form-application-article-paper%402x.png') }}" class="icon--dashboard">
                            {{-- <i class="fa fa-circle-o"></i> --}}
                            My Team's Development Plan
                        </a>
                    </li>
                </ul>
            </li>

            @if($checker->hasModulePermission([App\RoleCategory::EMP_SELFSERVICE]))
            <li class="{{ $checker->areOnRoutes(['benefits']) }}">
                <a href="{{ route('benefits') }}">
                    <img src="{{ asset('image/icons/verified-check-secured-legal-certified%402x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-check-circle-o"></i> --}}
                    <span>Benefits</span>
                </a>
            </li>

            <li class="{{ $checker->areOnRoutes(['requests', 'request.create', 'request.show']) }}">
                <a href="{{ route('requests') }}">
                    <img src="{{ asset('image/icons/edit-document-note-writing-review%402x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-pencil-square-o"></i> --}}
                    <span>Requests</span>
                </a>
            </li>

            <li class="{{ $checker->areOnRoutes(['governmentforms', 'governmentform.create', 'governmentform.show']) }}">
                <a href="{{ route('governmentforms') }}">
                    <img src="{{ asset('image/icons/content-form-contract-article-paper@2x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-wpforms"></i> --}}
                    <span>Government & Partner Forms</span>
                </a>
            </li>
            @endif
            
            @if ($checker->hasModuleRoles([
                'Adding/Editing of Meeting Room Reservations',
                'Viewing of Meeting Room Reservations',
            ]) || $isUserTechnician)
            <li class="{{ $checker->areOnRoutes(['mrreservations', 'mrreservation.create', 'mrreservation.show']) }}">
                <a href="{{ route('mrreservations') }}">
                    <img src="{{ asset('image/tabs/team.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-users"></i> --}}
                    <span>Meeting Room Reservations</span>
                </a>
            </li>
            @endif

            @if($subordinateCount || $approvalCount || $updateApprovalCount)
            <li class="treeview {{ $checker->areOnRoutes(['approvals']) }}">
                <a href="#">
                    <i class="fa fa-thumbs-o-up"></i>
                    <span>For Approval</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left" style="margin-top: 3px;"></i>
                        @if ($approvalCount || $updateApprovalCount)
                            <small class="label notif bg-red">{{ $approvalCount + $updateApprovalCount }}</small>
                        @endif
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ $checker->isOnRoute('approvals') }}">
                        <a href="{{ route('approvals') }}"><i class="fa fa-circle-o"></i> 
                            Requests
                            @if ($approvalCount)
                                <small class="label notif pull-right bg-red">{{ $approvalCount }}</small>
                            @endif
                        </a>
                    </li>
                    <li class="{{ $checker->isOnRoute('temprequests') }}">
                        <a href="{{ route('temprequests') }}"><i class="fa fa-circle-o"></i>
                            Request Updates
                            @if ($updateApprovalCount)
                                <small class="label notif pull-right bg-red">{{ $updateApprovalCount }}</small>
                            @endif
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if($checker->hasModulePermission([App\RoleCategory::FORMS_MNGMT]))
            <li class="{{ $checker->areOnRoutes(['formtemplates', 'formtemplate.create', 'formtemplate.show']) }}">
                <a href="{{ route('formtemplates') }}">
                    <img src="{{ asset('image/icons/content-form-application-article-paper%402x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-files-o"></i> --}}
                    <span>Forms</span>
                </a>
            </li>
            @endif

            @if($checker->hasModulePermission([App\RoleCategory::TICKET_MNGMT]))
            <li class="{{ $checker->areOnRoutes(['tickets', 'ticket.show']) }}">
                <a href="{{ route('tickets') }}">
                    <img src="{{ asset('image/icons/note-short-reminder-memo-brief@2x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-ticket"></i> --}}
                    <span>Tickets</span>
                    @if ($ticketCount)
                        <small class="label notif pull-right bg-red">{{ $ticketCount }}</small>
                    @endif
                </a>
            </li>
            @endif
    
            @if($checker->hasModuleRoles(['Generating of IDP Reports', 'Generating of Admin Reports', 'Generating of HR Reports', 'Generating of L&D Reports', 'Generating of BBLS Reports']))
            <li class="treeview {{ $checker->areOnRoutes(['idp.report', 'request.adminreport', 'request.hrreport', 'request.l&dreport', 'eventparticipant.report']) }}">
                <a href="#">
                    <img src="{{ asset('image/icons/presentation-graph-chart-teach-board@2x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-object-group"></i> --}}
                    <span>Reports</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>                    
                </a>
                <ul class="treeview-menu">
                    @if($checker->hasModuleRoles(['Generating of IDP Reports']))
                    <li class="{{ $checker->areOnRoutes(['idp.report']) }}">
                        <a href="{{ route('idp.report') }}"><i class="fa fa-object-group"></i> IDP Monitoring</a>
                    </li>
                    @endif
                    @if($checker->hasModuleRoles(['Generating of Admin Reports']))
                    <li class="{{ $checker->areOnRoutes(['request.adminreport']) }}">
                        <a href="{{ route('request.adminreport') }}"><i class="fa fa-object-group"></i> Admin Requests Monitoring</a>
                    </li>
                    @endif
                    @if($checker->hasModuleRoles(['Generating of HR Reports']))
                    <li class="{{ $checker->areOnRoutes(['request.hrreport']) }}">
                        <a href="{{ route('request.hrreport') }}"><i class="fa fa-object-group"></i> HR Requests Monitoring</a>
                    </li>
                    @endif
                    @if($checker->hasModuleRoles(['Generating of L&D Reports']))
                    <li class="{{ $checker->areOnRoutes(['request.l&dreport']) }}">
                        <a href="{{ route('request.l&dreport') }}"><i class="fa fa-object-group"></i> L&D Report</a>
                    </li>
                    @endif
                    @if($checker->hasModuleRoles(['Generating of BBLS Reports']))
                    <li class="{{ $checker->areOnRoutes(['eventparticipant.report']) }}">
                        <a href="{{ route('eventparticipant.report') }}"><i class="fa fa-object-group"></i> BBLS Report</a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if($checker->hasModulePermission([App\RoleCategory::USER_MNGMT]))
            <li class="{{ $checker->areOnRoutes(['groups', 'group.create', 'group.show']) }}">
                <a href="{{ route('groups') }}">
                    <img src="{{ asset('image/icons/user-add-plus-create-admin@2x.png') }}" class="icon--dashboard">
                    {{-- <i class="fa fa-user-plus"></i> --}}
                    <span>Responsibilities</span>
                </a>
            </li>
            @endif

            {{-- <li class="{{ $checker->isOnRoute('settings') }}">
                <a href="{{ route('settings') }}">
                    <i class="fa fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li> --}}

        </ul>
    </div>
<!-- /.sidebar -->
</aside>