window.orderBy = require('lodash.orderby');

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');
} catch (e) {}


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * We'll load the moment.js library 
 */

window.moment = require('moment');


/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

window.Vue = require('vue');

import store from './store';
/**
 * Add in the VUE componeents using lazy loading
 */
Vue.component('datatable', () => System.import('./components/DataTable.vue'));
Vue.component('dataheader', () => System.import('./components/DataHeader.vue'));
Vue.component('workflow', () => System.import('./components/Workflow.vue'));

Vue.component('searching', () => System.import('./components/Searching.vue'));
Vue.component('import', () => System.import('./components/Import.vue'));
Vue.component('notifications', () => System.import('./components/Notifications.vue'));

Vue.component('ticketreport', () => System.import('./components/Tickets/TicketReport.vue'));

Vue.component('groups', () => System.import('./components/Groups/Groups.vue'));

Vue.component('employees', () => System.import('./components/Employees/Employees.vue'));
Vue.component('departmentselector', () => System.import('./components/Employees/DepartmentSelector.vue'));
Vue.component('onvacationsettings', () => System.import('./components/Employees/OnVacationSettings.vue'));

Vue.component('companies', () => System.import('./components/Companies/Companies.vue'));

Vue.component('divisions', () => System.import('./components/Divisions/Divisions.vue'));

Vue.component('departments', () => System.import('./components/Departments/Departments.vue'));

Vue.component('positions', () => System.import('./components/Positions/Positions.vue'));

Vue.component('teams', () => System.import('./components/Teams/Teams.vue'));

Vue.component('locations', () => System.import('./components/Locations/Locations.vue'));
Vue.component('rooms', () => System.import('./components/Rooms/Rooms.vue'));

Vue.component('mrreservations', () => System.import('./components/MrReservations/MrReservations.vue'));
Vue.component('mrdetails', () => System.import('./components/MrReservations/MrDetails.vue'));

Vue.component('requests', () => System.import('./components/Requests/Requests.vue'));
Vue.component('selectrequest', () => System.import('./components/Requests/SelectRequest.vue'));
Vue.component('requestupdates', () => System.import('./components/Requests/RequestUpdates.vue'));
Vue.component('requesthistory', () => System.import('./components/Requests/RequestHistory.vue'));
Vue.component('requestreport', () => System.import('./components/Requests/RequestReport.vue'));
Vue.component('request-logs', () => System.import('./components/Requests/RequestLogs.vue'));

Vue.component('formupdates', () => System.import('./components/FormUpdates/FormUpdates.vue'));

Vue.component('events', () => System.import('./components/Events/Events.vue'));
Vue.component('eventparticipants', () => System.import('./components/Events/EventParticipants.vue'));
Vue.component('datesettings', () => System.import('./components/Events/DateSettings.vue'));
Vue.component('eventparticipantreport', () => System.import('./components/Events/EventParticipantReport.vue'));
Vue.component('sync-event', () => System.import('./components/Events/SyncEvent.vue'));

Vue.component('idps', () => System.import('./components/Learnings/IDPS.vue'));
Vue.component('idpreport', () => System.import('./components/IDPS/IDPReport.vue'));

Vue.component('learnings', () => System.import('./components/Learnings/Learnings.vue'));

Vue.component('formtemplates', () => System.import('./components/FormTemplates/FormTemplates.vue'));
Vue.component('formtemplatefields', () => System.import('./components/FormTemplates/FormTemplateFields.vue'));
Vue.component('slasettings', () => System.import('./components/FormTemplates/SLASettings.vue'));
Vue.component('travelordersettings', () => System.import('./components/FormTemplates/TravelOrderSettings.vue'));

Vue.component('formdetails', () => System.import('./components/Forms/FormDetails.vue'));
Vue.component('ldcost', () => System.import('./components/Forms/L&DCost.vue'));
Vue.component('meetingroomdetails', () => System.import('./components/Forms/MeetingRoomDetails.vue'));

Vue.component('tickets', () => System.import('./components/Tickets/Tickets.vue'));
Vue.component('ticketupdates', () => System.import('./components/Tickets/TicketUpdates.vue'));
Vue.component('ticket-travel-order-details', () => System.import('./components/Tickets/TicketTravelOrderDetails.vue'));
Vue.component('ticket-travel-order-form', () => System.import('./components/Tickets/TicketTravelOrderForm.vue'));
Vue.component('ticket-room-update', () => System.import('./components/Tickets/TicketRoomUpdate.vue'));
// Vue.component('tempticketupdates', () => System.import('./components/Tickets/TempTicketUpdates.vue'));

Vue.component('governmentforms', () => System.import('./components/GovernmentForms/GovernmentForms.vue'));

Vue.component('calendar', () => System.import('./components/Calendars/Calendar.vue'));

Vue.component('attachments', () => System.import('./components/Attachments.vue'));
Vue.component('uploadfile', () => System.import('./components/UploadFile.vue'));
Vue.component('currency-input', () => System.import('./components/CurrencyInput.vue'));


Vue.component('developersettings', () => System.import('./components/Dev/DeveloperSettings.vue'));



//Test
Vue.component('alltickets', () => System.import('./components/Tickets/AllTickets.vue'));
Vue.component('ongoing', () => System.import('./components/Tickets/OngoingTicket.vue'));
Vue.component('completed', () => System.import('./components/Tickets/CompletedTicket.vue'));
Vue.component('cancelled', () => System.import('./components/Tickets/CancelledTicket.vue'));
Vue.component('testdatatable', () => System.import('./components/TestDataTable.vue'));
//End Test


/**
 * Setup in VUE mixin
 */
Vue.directive('datepicker', {
    bind: function(el, binding, vnode) {
        const vm = vnode.context;

        $(el).change(function() {
            vm.$data[binding.expression] = $(el).val();
        });
    },
});


/**
 * Setup in VUE mixin
 */
Vue.mixin({

    methods: {
        
        /*
        |-----------------------------------------------
        | @Helper
        |-----------------------------------------------
        */
        moment: function (date, format = 'MMM. DD, YYYY', addedDays = 0) {

            if(!date)
                return '';

            /* Create moment obj */
            let momentDate = moment(date, 'YYYY-MM-DD hh:mm:ss');
            /* Check if need to add days */
            if(addedDays)
                momentDate = momentDate.add(addedDays, 'days');


            return momentDate.format(format);
        },

        truncate: function(string, limit) {
            
            if(!string || string.length < limit)
                return string;

            return string.substring(0, limit) + '...';
        },

        addURLParam: function(key, value, url) {
            var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
                hash;

            if(re.test(url)) {

                if(typeof value !== 'undefined' && value !== null) {

                    return url.replace(re, '$1' + key + "=" + value + '$2$3');

                } else {

                    hash = url.split('#');
                    url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');

                    if(typeof hash[1] !== 'undefined' && hash[1] !== null) {

                        url += '#' + hash[1];
                    }

                    return url;
                }

            } else {

                if(typeof value !== 'undefined' && value !== null) {

                    var separator = url.indexOf('?') !== -1 ? '&' : '?';

                    hash = url.split('#');
                    url = hash[0] + separator + key + '=' + value;

                    if(typeof hash[1] !== 'undefined' && hash[1] !== null) {
                        url += '#' + hash[1];
                    }

                    return url;

                } else {
                    return url;
                }
            }
        },
    },
});

/**
 * Create main VUE
 */
const vueApp = new Vue({

    el: '#main',
    store,

    data: function() {
    	return {
    		//
    	};	
    },

    components: {},

    methods: {

        onShow: function(ref) {
            var ref = this.$refs[ref];

            /* Check if component exists */
            if(ref) {
                var table = ref.$refs.datatable || ref;

                /* Check if already loaded */
                if(!table.isInitialFetch()) {
                    table.fetch();
                }
            }
        },

        renderCalendar: function(ref) {
            var ref = this.$refs[ref];

            /* Check if component exists */
            if(ref) {
                ref.render();
            }
        },        

        workingDaysBetweenDates: function(startDate, endDate, includeWeekend) {
          
            /* Validate input */
            if(endDate < startDate)
                return 0;
            
            /* Calculate days between dates */
            var millisecondsPerDay = 86400 * 1000;

            startDate.setHours(0,0,0,1);
            endDate.setHours(23,59,59,999);

            var diff = endDate - startDate;
            var days = Math.ceil(diff / millisecondsPerDay);
            
            /* Check if weekends are incuded */
            if(includeWeekend)
                return days;

            /* Subtract two weekend days for every week in between */
            var weeks = Math.floor(days / 7);
            days = days - (weeks * 2);

            /* Handle special cases */
            var startDay = startDate.getDay();
            var endDay = endDate.getDay();
            
            /* Remove weekend not previously removed */
            if (startDay - endDay > 1)         
                days = days - 2;      
            
            /* Remove start day if span starts on Sunday but ends before Saturday */
            if (startDay == 0 && endDay != 6)
                days = days - 1  
                    
            /* Remove end day if span ends on Saturday but starts after Sunday */
            if (endDay == 6 && startDay != 0)
                days = days - 1  
            
            return days;
        },          
    },
});