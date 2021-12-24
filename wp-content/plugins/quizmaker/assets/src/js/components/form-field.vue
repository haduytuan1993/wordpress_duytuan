<template>
	<div class="form-field">
		
		<text-field v-if="value.type == 1" :value="value" v-on:change="change"></text-field>

		<checkbox-field v-if="value.type == 2" :value="value" v-on:change="change"></checkbox-field>

	</div>
</template>
<script>

import TextField from './forms/text.vue';
import CheckboxField from './forms/checkbox.vue';

export default {
	components: {
		'text-field': TextField,
		'checkbox-field': CheckboxField
	},
	props:{
		value: {
			type: [Object, Array, String, Number]
		}
	},
	data: function(){

		return {
			loading: false
		};
	},
	methods: {
		change: function(){

			this.$emit('change', this.value);
		}
	}
}
</script>