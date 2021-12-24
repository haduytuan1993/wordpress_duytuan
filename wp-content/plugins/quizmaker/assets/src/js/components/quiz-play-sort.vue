<template>
	<div :class="{quiz_play_finish: is_finish_data}" class="quiz-play-item quiz-play-sort">
		<v-container fluid grid-list-lg>
			
			<div class="play-content">
				<div>
					{{content.content}}
				</div>

				<aws-player :music="content.musics.songs" :settings="content.musics.settings" ref="player"></aws-player>
			</div>
			
			<v-layout row wrap class="play-answers ans-sort">
				<v-flex sm8>
					
	        		<div class="sort-options">
        		
			            <div class="sort-option">
			              
			              <div v-for="(q, index) in content.options" :key="q.id" :id="q.id" class="sort-item">
			                <div class="wrap-icon">
			                	<v-icon>swap_vert</v-icon>
			                </div>
			                <div class="content" v-text="q.content"></div>
			              </div>
			              
			            </div>
		        		
		        	</div>
					
	        		<v-btn small primary round class="mt-3 ml-0" @click="answer">Answer</v-btn>
					
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

		var _self = this;

	  	this.sorting = jQuery(this.$el).find('.sort-option').sortable({
	      stop: function(event, ui){

	          _self.value = _self.sorting.sortable('toArray');
	          
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
				console.log('sort');
				_self.is_finish_data = true;
				_self.$emit('answer', response.data.result);
			});
		}
	}
}
</script>