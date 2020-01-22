<template>

	<div v-show="lists && lists.length > 0"
	class="row">
		<div class="col-sm-5">
			<div class="dataTables_info" role="status" aria-live="polite">
				Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
			</div>
		</div>
		<div class="col-sm-7">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination">

					<li :class="{
						'disabled': pagination.prev_page_url == null
					}"
					class="paginate_button previous">
						<a @click="prev()">Previous</a>
					</li>

					<template v-if="pagination.last_page > 1" v-for="n in pagination.last_page">
						
						<li v-if="Math.abs(n - pagination.current_page) > maxpage && Math.abs(n - pagination.current_page) < (maxpage + 2)"
						class="paginate_button disabled">
							<a>...</a>
						</li>
						<li v-else-if="Math.abs(n - pagination.current_page) < (maxpage + 2)"
						:class="{ 
							'active': n == pagination.current_page 
						}"
						class="paginate_button">
							<a @click="page(n)">{{ n }}</a>
						</li>

					</template>
						
					<template v-if="pagination.last_page == 1">
						
						<li class="paginate_button active">
							<a>1</a>
						</li>

					</template>

					<li :class="{
						'disabled': pagination.next_page_url == null
					}"
					class="paginate_button next">
						<a @click="next()">Next</a>
					</li>

				</ul>
			</div>
		</div>
	</div>

</template>
<script>
	export default {

		props: [
			'lists',
			'url',
			'limit',
		],

		data: function() {
			return {
				maxpage: 3,
				pagination: {
					prev_page_url: null,
					next_page_url: null,
					current_page: 0,
					last_page: 0,
					next_page: 0,
					total: 0,
					from: 0,
					to: 0,

					limit: 0,
				},				
			};
		},

		computed: {},

		mounted: function() {
			this.init();
		},

		methods: {

			init: function() {

				/* Set Default */
				this.pagination.limit = this.limit || 10;
			},

			/*
			|-----------------------------------------------
			| @Controller
			|-----------------------------------------------
			*/	
			page: function(index) {

				/* Check if this is the current url */
				if(this.pagination.current_page == index)
					return false;

				this.$emit('page-was-clicked', this.addURLParam('page', index, this.url));
			},

			prev: function() {

				/* Check if there is a prev url */
				if(this.pagination.prev_page_url == null)
					return false;

				this.$emit('page-was-clicked', this.addURLParam('page', (this.pagination.current_page - 1), this.url));
			},

			next: function() {

				/* Check if there is a next url */
				if(this.pagination.next_page_url == null)
					return false;

				this.$emit('page-was-clicked', this.addURLParam('page', (this.pagination.current_page + 1), this.url));
			},


			/*
			|-----------------------------------------------
			| @Getter/Setter
			|-----------------------------------------------
			*/
			//


            /*
            |-----------------------------------------------
            | @Helper
            |-----------------------------------------------
            */
           	updatePagination: function(lists) {

				/* Update the pagination data */
				this.pagination.prev_page_url = lists.prev_page_url;
				this.pagination.next_page_url = lists.next_page_url;
				this.pagination.current_page = lists.current_page;
				this.pagination.last_page = lists.last_page;
				this.pagination.next_page = lists.next_page;
				this.pagination.total = lists.total;
				this.pagination.from = lists.from;
				this.pagination.to = lists.to;
           	},			
		},
	}
</script>