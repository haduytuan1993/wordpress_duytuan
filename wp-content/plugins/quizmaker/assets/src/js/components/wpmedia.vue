<template>
	<div class="wpmedia">
		<img v-if="valueData > 0" :src="image.url">

		<v-btn-toggle mandatory v-model="toggle_one" class="mt-1">
			<v-btn flat small @click.stop="open">Upload Image</v-btn>
			<v-btn flat small v-if="valueData" @click.stop="remove">Clear</v-btn>
		</v-btn-toggle>
		<input type="hidden" :name="name" v-model="valueData"/>
	</div>
</template>
<script>

export default {
	props: {
		id: {
			type: Number,
			default: function(){
				return 0;
			}
		},
		ajax: {
			type: Object,
			default: function(){

				return {
					action: 'wpmedia',
					url: qm_admin.ajax_url,
					security: qm_admin.security
				};
			}
		},
		size: {
			type:String,
			default: function(){

				return 'thumbnail';
			}
		},
		name: {
			type:String,
			default: function(){

				return 'thumbnail';
			}
		},
		value: {
			type: [Number, String],
			default: function(){

				return 0;
			}
		}
	},
	data: function(){

		var params = {
			toggle_one: 0,
			frameWPMedia: null,
			valueData: this.value,
			image: {
				url: 'https://vuetifyjs.com/static/doc-images/cards/desert.jpg'
			},
			loading: false
		};

		return params;
		
	},
	watch: {
		value: function(v1){

			if( v1 && parseInt(v1) > 0 ) {
				
				this.loadData( v1, this.size );
			}

			this.valueData = v1;
		}
	},
	mounted: function(){

		if( this.valueData && parseInt(this.valueData) > 0 ) {

			this.loadData( this.valueData, this.size );
		}
	},
	methods: {
		open: function(){

			var _self = this;

			var frameWPMedia = this.frameWPMedia;

			if ( frameWPMedia ) {

				frameWPMedia.close();
				return;
			}
			
			frameWPMedia = wp.media({
				title: 'Select or Upload Media Of Your Chosen Persuasion',
				button: {
					text: 'Use this media'
				},
				multiple: false
			});
			
			frameWPMedia.on( 'select', function() {

				var attachment = frameWPMedia.state().get('selection').first().toJSON();
				
				if( attachment.sizes ) {
					
					_self.image = attachment.sizes.medium;
					_self.valueData = attachment.id;

					_self.$emit('update:value', _self.valueData);
				}
				
			});

			frameWPMedia.open();
		},
		remove: function(){

			this.valueData = null;
			this.$emit('update:value', 0);
		},
		loadData: function( id, size ){

			var _self = this;

			jQuery.post( this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'get',
					id: id,
					size: size
				}
			}, function( response ){

				_self.image = response.data.image;
			});
		}
	}
}
</script>