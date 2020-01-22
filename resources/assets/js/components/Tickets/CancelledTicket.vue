<template>
	<div>
		<div class="row ">
			<div v-if="settings.showdaterange"
			class="row s-margin-b">
				<div class="col-sm-12">
					<div class="form-group">
						<div class="input-group">
							<button type="button" class="btn btn-default" id="daterangecancelled">
								<i class="fa fa-calendar"></i>
								<span> Select date here... </span>
								<i class="fa fa-caret-down"></i>
							</button>
							<button id="btn-datefilter"
							type="button" class="btn btn-default s-margin-l">
								<i class="fa fa-refresh"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">

			<div class="col-md-10">

				<template v-for="category in categories">
					
					<select v-model="filter[category.label]" :data-column="category.label"
					class="form-control types" style="width: 150px;">
						<option value="" selected>Filter {{ category.label }}...</option>

						<template v-for="option in category.options">

							<option :value="option.label">{{ option.label }}</option>

						</template>

					</select>

				</template>

			</div>						
			
		</div>
		<br><br>
		
		<table class="table table-responsive table-striped table-bordered cancelledData" style="width:100%">	
			<thead>
				<tr>
	              <th><b>Ticket#</b></th>
	              <th><b>Request#</b></th>
	              <th><b>Form</b></th>
	              <th><b>Priority</b></th>
	              <th><b>Category</b></th>
	              <th><b>Requested By</b></th>
	              <th><b>Assigned To</b></th>
	              <th><b>SLA</b></th>
	              <th><b>Date Needed</b></th>
	              <th><b>Date Closed</b></th>
	              <th><b>Date Created</b></th>
	              <th><b>Status</b></th>
	              <th><b>State</b></th>
	              <th><b>Action</b></th>
				</tr>
			</thead>
		</table>
	</div>	

</template>
<script>
	

	export default {

		props: [

		'showdaterange',
		'daterange',
		'categories',
		'status',
		'states',
		'priorities',
		'fetchurl',
		'status',
		],


		data(){
			return{

				settings: {
					autofetch: false,
					initialfetch: false,
					nosearch: false,
					showdaterange: false,					
				},
				
				startdate: null,
				enddate: null,

				startDisplay: null,
				endDisplay: null,	
				filter: {},		

			}

		},
        methods: {

        	init: function() {

				var $this = this;


				/* Create default filter variables */
				if(this.categories) {

					for(var $i = 0; $i < this.categories.length; $i++) {
						this.filter[this.categories[$i].label] = '';
					}
				}
			
				// this.settings.autofetch = this.autofetch ? true : false;
				// this.settings.paginationlimit = this.paginationlimit ? this.paginationlimit : 10;
				// this.settings.nosearch = this.nosearch ? this.nosearch : false;
				this.settings.showdaterange = this.daterange ? true : false;


				/* Initialize date range */
				if(this.settings.showdaterange) {
					this.$nextTick(function() {
						$this.initDatePicker();
					});
				}

				/* Check if auto fetch is enable */
				if(this.settings.autofetch)
					this.fetch();
			},

			initDatePicker: function() {
				var daterange = $('#' + this.daterange + 'daterange-btn'),
					startDate = moment().startOf('year'),
					endDate = moment().endOf('year'),
					$this = this;


				daterange.daterangepicker({
					locale: {
						format: 'YYYY-MM-DD'
				    },
					startDate: 0,
					endDate: 0,
					opens: 'right',
					ranges: {
						'This Year': [startDate, endDate],
						'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
						'This Month': [moment().startOf('month'), moment().endOf('month')],
						'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					},
				}, function(start, end) {

					/* Update date */
					$this.setDate(start, end);
				});
			},		

			setDate: function(startDate, endDate) {

				/* Check if still fetching */
				// if(this.loading)
				// 	return false;


				this.startdate = startDate.format('YYYY-MM-DD');
				this.enddate = endDate.format('YYYY-MM-DD');

				this.startDisplay = startDate.format('MMMM D, YYYY');
				this.endDisplay = endDate.format('MMMM D, YYYY');
           	},		

           	fetch(){

           		console.log(this.startdate);
           		console.log(this.enddate);
           		
           	},	

            read() {

            	var startDate;
            	var endDate;
            	var url = this.fetchurl;
            	var status = this.status;

            	$(document).ready(function(){
            	var table = $('table.cancelledData').DataTable({

            		// "processing": true,
	               	// "stateSave": true,
	               	"responsive": true,
	               	"order": [],
	               	"searchable": true, 
	               	"ajax": {
	               		'url' : url,
	               		'type': 'POST',
	               		'headers': {
				        		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				    	},
				    	"data": function(d){
        					d.startdate = startDate,
        					d.enddate = endDate,
        					d.status = status
    					}
	           		},
	           		"columnDefs": [ 
	           			//action
		           		{
				            "targets": -1,
				            "data": null,
				            "render": function (data, type, row, meta) {
						     	return '<a href="/tickets/' + data[0] + '"  class="btn btn-primary btn-xs"><span class="fa fa-eye"></span></a>';
						   	}
				       
				        },
				         //priority column
				        {

				        	"targets": 3,
				            "render": function (data, type, row, meta) {
					            if(data == 'LOW'){
							     	return '<span class="badge bg-yellow">'+data+'</span>'
					            }
					            if(data == 'MEDIUM'){
					            	return '<span class="badge bg-orange">'+data+'</span>'
					            }
					            if(data == 'HIGH'){
					            	return '<span class="badge bg-red">'+data+'</span>'
					            }
						   	}
				        },
				        //status column
				        {
				        	"targets": 11,
				            "render": function (data, type, row, meta) {
					            if(data == 'OPEN'){
							     	return '<span class="badge bg-primary">'+data+'</span>'
					            }
					            if(data == 'CLOSED'){
					            	return '<span class="badge bg-green">'+data+'</span>'
					            }
					            if(data == 'ON HOLD'){
					            	return '<span class="badge bg-yellow">'+data+'</span>'
					            }
					            if(data == 'CANCELLED'){
					            	return '<span class="badge bg-red">'+data+'</span>'
					            }
						   	}

				        },
				        // state
				        {
				        	"targets": 12,
				            "render": function (data, type, row, meta) {

					            if(data == 'On-time'){
					            	return '<span class="badge bg-green">'+data+'</span>'
					            }
					            if(data == 'Delayed'){
					            	return '<span class="badge bg-red">'+data+'</span>'
					            }
					            if(data == ''){
					            	return ''
					            }
						   	}
				        }
			        ],
			  //       stateLoadParams: function( settings, data ) {
					//    if (data.order) delete data.order;

					// }
            	});

            	
            	$('#daterangecancelled').daterangepicker({
            		locale: {
						format: 'YYYY-MM-DD'
				    },
					opens: 'right',
					ranges: {
						'This Year': [moment().startOf('year'), moment().endOf('year')],
						'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
						'This Month': [moment().startOf('month'), moment().endOf('month')],
						'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
					},
				}, function(start, end, label){
            		$('#daterangecancelled span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
					startDate = start.format('YYYY-MM-DD');
					endDate = end.format('YYYY-MM-DD');
					console.log(startDate, endDate);
					table.ajax.reload();
            	});


            	$('#btn-datefilter').click(function(){
            		table.ajax.reload();
            	});

  


            	$("select.types").change(function(){
            		var type = $(this).attr('data-column');
            		var selected = $(this).children("option:selected").val();
            		var column;

            		if(type == 'form'){
            			column =  2
            		}
            		if(type == 'priority'){
            			column = 3
            		}
            		if(type == 'category'){
            			column = 4
            		}
            		if(type == 'status'){
            			column = 11
            		}
            		if(type == 'state'){
            			column = 12
            			if(selected == 'Ongoing'){
            			selected = '';
            		}
            			
            		}
            		
            		console.log(selected)
            		filterColumn(column, selected);
			       
			    });

			    $('input.column_filter').on( 'keyup click', function () {
			        filterColumn( $(this).parents('tr').attr('data-column') );

			    });

			    function filterColumn ( i, selected ) {
				    $('.cancelledData').DataTable().column( i ).search(

				    	selected,
				       
				    ).draw();
				}

				

				});
            	
			   
			},        
        },
		mounted() {

			this.init();
            this.read();
        }
		
	}
</script>