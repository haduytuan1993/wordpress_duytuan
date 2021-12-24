<template>
	<div class="form-field__text">
		
		<v-text-field v-model="value.name" label="Name"></v-text-field>
		<v-text-field v-model="value.default" label="Default"></v-text-field>

	</div>
</template>
<script>
export default {
	props: {
		name: {
			type: String,
			default: function(){

				return '';
			}
		},
		value: {
			type: [Object],
			default: function(){

				return {};
			}
		}
	},
	data: function(){

		return {
			
		};
	},
	mounted: function(){


	},
	methods: {
		
		
	}
}
</script>