<template>

	<div>
		<v-card>
			<v-card-title>{{test_title}}</v-card-title>
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
						<td><a :href="test_link + 'result/' + props.item.id" target="__blank">{{ props.item.test_title }}</a></td>
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
	        items: [],
	        test_link: '',
	        test_title: ''
		}
	},
	computed: {
		pages: function() {
			return this.pagination.rowsPerPage ? Math.ceil(this.items.length / this.pagination.rowsPerPage) : 0;
		}
    },
	created () {

		var _self = this;

		Vue.http.get('?qm-ajax=account_resource&method=result&tid=' + this.$route.params.id).then(response => {
			
			response.json().then(function( result ){

				if( typeof result.data != 'undefined' ) {
					
					_self.items   = result.data;

					if( _self.items ) {
						_self.test_link = result.test_link;
						_self.test_title = result.test_title;
					}

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