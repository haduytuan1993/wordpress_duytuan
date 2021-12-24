<template>

	<div>
		<v-card>
			<v-card-text>
				<v-data-table
				      v-bind:headers="headers"
				      v-bind:items="items"
				      v-bind:search="search"
				      v-bind:pagination.sync="pagination"
				      hide-actions loading="loading" 
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
			        <td><router-link :to="'/tests_results/test/' + props.item.test_id">{{ props.item.test_title }}</router-link></td>
			        <td  class="text-xs-right">{{ props.item.score }}</td>
			        <td  class="text-xs-right">{{ props.item.percent }}</td>
			        <td  class="text-xs-right">{{ props.item.duration }}</td>
			        <td  class="text-xs-right">{{ props.item.date_start }}</td>
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
			loading: true,
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
	          { text: 'Score', value: 'score' },
	          { text: 'Percent', value: 'percent' },
	          { text: 'Duration', value: 'duration' },
	          { text: 'Date', value: 'date_start' }
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

		Vue.http.get('?qm-ajax=account_resource&method=results').then(response => {
			
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