<template>

	<div>
		<v-card>
			<v-card-text>
				<v-data-table
				      v-bind:headers="headers"
				      v-bind:items="items"
				      v-bind:search="search"
				      v-bind:pagination.sync="pagination"
				      hide-actions
				      class="elevation-1"
				    >
			      <template slot="headerCell" slot-scope="props">
			        <v-tooltip bottom>
			          <span slot="activator">
			            {{ $t(props.header.text) }}
			          </span>
			          <span>
			            {{ $t(props.header.text) }}
			          </span>
			        </v-tooltip>
			      </template>
			      <template slot="items" slot-scope="props">
			        <td><a :href="props.item.test_link" target="__blank">{{ props.item.test_title }}</a></td>		       
			        <td  class="text-xs-right">{{ props.item.duration }}</td>
			        <td  class="text-xs-right">{{ props.item.attempt }}</td>
			      </template>
			    </v-data-table>
			    <div class="text-xs-center pt-2">
			      <v-pagination v-model="pagination.page" :length="pages"></v-pagination>
			    </div>
			</v-card-text>
		</v-card>
	</div>

</template>
<script>

import Vue from 'vue';

export default {
	data () {
		return {
			search: '',
	        pagination: {rowsPerPage: 60},
	        selected: [],
	        headers: [
	          {
	            text: 'Title',
	            align: 'left',
	            sortable: false,
	            value: 'test_title'
	          },
	          { text: 'Duration', value: 'duration' },
	          { text: 'Attempt', value: 'attempt' }
	        ],
	        items: []
		}
	},
	computed: {
		pages: function() {
			return this.pagination.rowsPerPage ? Math.ceil(this.items.length / this.pagination.rowsPerPage) : 0;
		}
    },
	mounted () {

		var _self = this;

		Vue.http.get('?qm-ajax=account_resource&method=assigned_tests').then(response => {
			
			response.json().then(function( result ){

				if( typeof result.data != 'undefined' ) {

					_self.items   = result.data;
				}

				_self.loading = false;
			});

		}, response => {
			
			_self.items   = [];
			_self.loading = false;

		});
		
	}
}
</script>