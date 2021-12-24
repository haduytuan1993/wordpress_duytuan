<template>
	<div class="form-field__checkbox">
		
		<v-text-field v-model="valueData.title" label="Title"></v-text-field>
		<v-list two-line>
			<template v-for="(item, index) in valueData.data">
			<v-list-tile avatar :key="index">
				
				<v-list-tile-action>
				  <v-checkbox :value="1" v-model="item.default"></v-checkbox>
				</v-list-tile-action>

				<v-list-tile-content>
					
					<v-text-field label="Name" v-model="item.name"></v-text-field>
					
				</v-list-tile-content>

				<v-list-tile-action>
	              <v-btn icon ripple @click="remove(index)">
	                <v-icon color="grey lighten-1">delete</v-icon>
	              </v-btn>
	            </v-list-tile-action>
				
			</v-list-tile>
			</template>
		</v-list>
		<v-btn dark primary small @click="add">Add New</v-btn>
	</div>
</template>
<script>
export default {
	props: {
		value: {
			type: [Object]
		}
	},
	data: function(){

		var data = {
			valueData: this.value
		};

		if( !data.valueData.data ) {

			data.valueData.data = [{name: 'Checkbox', default: 0}];

		}
		
		return data;
	},
	methods: {
		add: function(){

			this.valueData.data.push({name: 'Checkbox', default: 0});
		},
		remove: function( index ){

			this.valueData.data.splice(index, 1);
		}
	}
}
</script>