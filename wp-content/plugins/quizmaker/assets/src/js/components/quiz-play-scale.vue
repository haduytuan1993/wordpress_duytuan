<template>
	<div :class="{quiz_play_finish: is_finish_data}" class="quiz-play-item quiz-play-scale">
		<v-container fluid grid-list-lg>

			<v-layout row wrap class="play-answers ans-scale">
				<v-flex sm8>
					<p class="play-content">
						<div>
							{{content.content}}
						</div>

						<aws-player :music="content.musics.songs" :settings="content.musics.settings" ref="player"></aws-player>
					</p>
					<div class="mb-3 wrap-slider-range">
						<div class="slider-range">
		        			<div class="custom-handle ui-slider-handle">
		        				<span class="value"></span>
		        			</div>
		        		</div>
	        		</div>
	        		<v-btn small primary round @click="answer">Answer</v-btn>
				</v-flex>
				<v-flex sm4>
					<div class="ans_image">
						<img v-if="content.image" :src="content.image[0]" alt=""/>
					</div>
				</v-flex>
			</v-layout>

			
		</v-container>
	</div>
</template>
<script>

export default {
	props: {
		ajax: {
			type: Object,
			default: function(){
				return {};
			}
		},
		id: {
			type: Number
		},
		content: {
			type: Object,
			default: function(){
				return {};
			}
		},
		is_finish: {
			type: Boolean,
			default: function(){

				return false;
			}
		},
		is_timeout: {
			type: Boolean,
			default: function(){

				return false;
			}
		}
	},
	data: function(){

		if( typeof this.content.musics == 'undefined' ) {

			this.content.musics = {};
		}

		return {
			is_answered: false,
			loading: false,
			value: 0,
			is_finish_data: this.is_finish
		};
	},
	mounted: function(){

		var _self = this, handle = jQuery(this.$el).find( ".custom-handle" );

	  	this.slider = jQuery(this.$el).find('.slider-range').slider({
			min: parseInt(_self.content.options.min),
			max: parseInt(_self.content.options.max),
			step: parseInt(_self.content.options.steps),
			value: 1,
			create: function() {
	        	handle.find('.value').text( jQuery( this ).slider( "value" ) );
	      	},
			slide: function( event, ui ) {

				handle.find('.value').text( ui.value );

				_self.value = ui.value;

			}
	  	});
	},
	watch: {
		is_timeout: function( new_value ) {

			if( !this.is_finish && new_value ) {

				this.answer(-1);
			}
		}
	},
	methods: {
		answer: function(){

			var _self = this;

			jQuery.post( this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'answer',
					qid: this.id,
					answer: {
						id: this.content.id,
						selected: this.value
					}
				}
			}, function( response ){

				// if( typeof response.data.result.result[4].correct != 'undefined' ) {

				// 	_self.slider.slider('value', parseInt(response.data.result.result[4].correct));
				// }

				_self.is_finish_data = true;
				_self.$emit('answer', response.data.result);
			});
		}
	}
}
</script>