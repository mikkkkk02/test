<template>

	<div>

		<div class="row">		
			<div class="form-group col-sm-3">
				<label>Company</label>
				<select :disabled="loading || companies.length == 0" v-model="company" @change="fetch(company, 0)"
				class="form-control">
					<option value="0" disabled selected>Select company...</option>

					<template v-for="company in companies">
				
						<option :value="company.id">{{ company.name }}</option>

					</template>

				</select>
			</div>
			<div class="form-group col-sm-3">

				<label>Group</label>
				<select :disabled="loading || divisions.length == 0" v-model="division" @change="fetch(division, 1)"
				class="form-control">
					<option value="0" disabled selected>Select group...</option>

					<template v-for="division in divisions">
					
						<option :value="division.id">{{ division.name }}</option>						

					</template>

				</select>

			</div>
			<div class="form-group col-sm-3">

				<label>Department</label>
				<select :disabled="loading || departments.length == 0" v-model="department" @change="fetch(department, 2)"
				name="department_id" class="form-control">
					<option value="0" disabled selected>Select department...</option>

					<template v-for="department in departments">
					
						<option :value="department.id">{{ department.name }}</option>						

					</template>

				</select>

			</div>
		</div>

		<div class="row">
			<div class="form-group col-sm-3">

				<label>Team</label>
				<select :disabled="loading || teams.length == 0" v-model="team"
				name="team_id" class="form-control">
					<option value="0" disabled selected>Select team...</option>

					<template v-for="team in teams">
					
						<option :value="team.id">{{ team.name }}</option>						

					</template>

				</select>

			</div>			
			<div class="form-group col-sm-3">

				<label>Position</label>
				<select :disabled="loading || positions.length == 0" v-model="position"
				name="position_id" class="form-control">
					<option value="0" disabled selected>Select position...</option>

					<template v-for="position in positions">
					
						<option :value="position.id">{{ position.title }}</option>						

					</template>

				</select>

			</div>
			<div class="form-group col-sm-3">
				<label>Immediate Leader</label>
				<select :disabled="supervisors.length == 0" v-model="supervisor"
				name="supervisor_id" class="form-control" required>
					<option value="0" disabled selected>Select immediate leader...</option>

					<template v-for="supervisor in supervisors">
				
						<option :value="supervisor.id">{{ supervisor.extra.fullname }}</option>

					</template>

				</select>
			</div>
		</div>

	</div>

</template>
<script>
	export default {

		props: [
			'employee',
			'supervisors',
			'companies',
		],

		data: function() {
			return {
				loading: false,

				supervisor: 0,

				company: 0,
				division: 0,
				department: 0,
				position: 0,
				team: 0,

				divisions: [],
				departments: [],

				teams: [],
				positions: [],
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				/* Check if employee exist */
				if(!jQuery.isEmptyObject(this.employee)) {

					/* Set supervisor */
					this.supervisor = this.employee.supervisor_id;


					if(!jQuery.isEmptyObject(this.employee.department)) {

						/* Set default value of employee */
						this.fetch(this.employee.department.department.division.company.id, 0, 1);
					}
				}
			},

			fetch: function($id, $level, $hasPromise = false) {
				var $this = this;


				/* Check if component is still loading */
				if((this.loading || !$id) && !$hasPromise)
					return false;

				this.loading = true;
				this.resetList($level);


				/* Fetch the divisions */
				axios.post(this.getURL($id, $level), {})
					.then(function(response) {

						/* Check AJAX result */
						if(response.status == 200) {

							/* Update divisions list */
							switch($level) {
								case 0: 

									$this.company = $id;
									$this.divisions = response.data.lists; 


									/* Check for promises */
									if($hasPromise) {

										$this.fetch($this.employee.department.department.division.id, 1, 1);

									} else {

										/* Reset */
										$this.resetSelected($level);
									}

								break;
								case 1: 

									$this.division = $id;
									$this.departments = response.data.lists; 


									/* Check for promises */
									if($hasPromise) {

										$this.fetch($this.employee.department.department.id, 2, 1);

									} else {

										/* Reset */
										$this.resetSelected($level);
									}

								break;
								case 2:

									$this.department = $id;

									$this.positions = response.data.positions; 
									$this.teams = response.data.teams; 


									/* Check for promises */
									if($hasPromise) {

										if($this.employee.department.position)
											$this.position = $this.employee.department.position.id;

										if($this.employee.department.team)
											$this.team = $this.employee.department.team.id;

										$hasPromise = false;

									} else {
									
										/* Reset */
										$this.resetSelected($level);
									}

								break;								
							}

							/* Don't enable yet if there is a promise */
							if(!$hasPromise)
								$this.show();
						}
					});
			},


			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/	
			resetList: function(level) {

				this.teams = [];
				this.positions = [];

				if(level == 2)
					return null;

				this.departments = [];

				if(level == 1)
					return null;

				this.divisions = [];
			},		

			resetSelected: function(level) {

				this.team = 0;
				this.position = 0;

				if(level == 2)
					return null;

				this.department = 0;

				if(level == 1)
					return null;

				this.division = 0;
			},


			show: function() {
				this.loading = false;
			},


			/*
			|-----------------------------------------------
			| @Getter/Setter
			|-----------------------------------------------
			*/
			getURL: function(id, level) {

				switch(level) {
					case 0: return this.getByID(this.companies, id).extra.fetchdivisions;
					case 1: return this.getByID(this.divisions, id).extra.fetchdepartments;
					case 2: return this.getByID(this.departments, id).extra.fetchpositionsteams;
				}
			},

			getByID: function(array, id) {
				return array.filter(function(obj) {
					return obj.id == id;
				})[0];
			},


            /*
            |-----------------------------------------------
            | @Helper
            |-----------------------------------------------
            */	
			//
		},
	}
</script>