<template>
	<div class="aws-formbuilder">
		
		<div class="fb-editor"></div>
		
		<button v-show="!loading" class="button-primary save" >Save changes</button>
		
	</div>
</template>
<script>

export default {
	
	props:{
		disable_fields: {
			type: [Array],
			default: function(){
				return [];
			}
		},
		ajax: {
			type: [Object],
			default: function(){
				return {};
			}
		},
		value: {
			type: [Object, Array, String, Number]
		}
	},
	data: function(){

		return {
			loading: false
		};
	},
	mounted: function(){

		var _self = this;

		jQuery(this.$el).find('.save').click(function(e){
			e.preventDefault();

			_self.save( _self.builder.actions.getData() );
		});

		this.load_data();

    },
	methods: {
		load_data: function(){

			var _self = this;

			jQuery.post(this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'get'
				}
			}, function(response){

				_self.builder = jQuery(_self.$el).find('.fb-editor').formBuilder({
					showActionButtons: false,
					roles: {},
					disableFields: _self.disable_fields,
					dataType: 'json',
					formData: JSON.stringify(response.data.form_data)
				});
			});
		},
		save: function( form_data ){

			var _self = this;

			this.loading = true;

			jQuery.post(this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'save',
					data: form_data
				}
			}, function(response){

				_self.loading = false;

			});

		}
	}
}
</script>