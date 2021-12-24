<template>
	<div class="aws-select2">
		<label v-if="label != ''">{{label}}</label>
		<select v-bind:name="name"><slot></slot></select>
	</div>
</template>
<script>
export default {
	props: {
		'label': {
			type: String,
			default: function(){

				return '';
			}
		},
		'name' : {
			type: [String, Number],
			default: 'categories'
		},
		'options' : {
			type: [Array, Object]
		},
		'value' : [String, Number],
		'params' : {
			type: Object,
			default: function(){
				return {};
			}
		},
		'auto_change': {
			type: [String, Number],
			default: 0
		}
	},
	computed: {
		prop_name2: function(){

			return this.name;
		}
	},
	data: function(){
		
		return {
			pr: {},
			select2JS: false,
			selected: this.value
		}
	},
	mounted: function () {

		var vm = this;

		this.pr = jQuery.extend({ 
			data: this.options, 
			theme: 'aws', 
			width: '100%',
			minimumResultsForSearch: 20
		}, this.params);
		
		this.select2JS = jQuery(this.$el).find('select')
		// init select2
		.select2( this.pr )
		.val( this.selected )
		.trigger('change')
		// emit event on change.
		.on('change', function () {

			vm.$emit('input', jQuery(this).val());
			vm.$emit('change', jQuery(this).val());
		});

		if( this.auto_change > 0 ){
			setTimeout(function(){
		  		
		  		vm.select2JS.trigger('change');

		  	}, this.auto_change);
		}
		
	},
	methods: {
		refresh: function(){

			jQuery(this.$el).find('select').select2(this.pr).trigger('change');
		}
	},
	watch: {
		options: function (options) {
		  	// update options
		  	
		  	this.pr.data = options;
		  	
			jQuery(this.$el).find('select').select2(this.pr).val( this.selected ).trigger('change');
		},
		value: function( value_1, value_2 ) {
			
			jQuery(this.$el).find('select').select2(this.pr).val( value_1 ).trigger('change');
		},
		destroyed: function () {
			jQuery(this.$el).find('select').off().select2('destroy');
		}
	}
}
</script>