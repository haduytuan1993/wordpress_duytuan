<template>
	<div :class="{quiz_play_finish: is_finish_data}" class="quiz-play-item quiz-play-itemmatch">
		<v-container fluid>
			
			<div class="play-content">
				<div>
					{{content.content}}
				</div>

				<aws-player :music="content.musics.songs" :settings="content.musics.settings" ref="player"></aws-player>

				<div v-if="content.image" class="ans_image">
						<img :src="content.image[0]" alt=""/>
					</div>

			</div>
			
			<v-layout row wrap class="play-answers ans-sort">
				<v-flex sm6>
					
	        		<div class="sort-options">
        		
			            <div class="sort-option sort-option__item_1">
			              
			              <div v-for="(q, index) in content.item_1" :key="q.id" :id="q.id" class="sort-item">
			                <div class="wrap-icon">
			                	<v-icon>swap_vert</v-icon>
			                </div>
			                <div class="content" v-text="q.item_1"></div>
			              </div>
			              
			            </div>
		        		
		        	</div>

				</v-flex>

				<v-flex sm6>

					<div class="sort-options">
        		
			            <div class="sort-option sort-option__item_2">
			              
			              <div v-for="(q, index) in content.item_2" :key="q.id" :id="q.id" class="sort-item">
			                <div class="wrap-icon">
			                	<v-icon>swap_vert</v-icon>
			                </div>
			                <div class="content" v-text="q.item_2"></div>
			              </div>
			              
			            </div>
		        		
		        	</div>

				</v-flex>
				
			</v-layout>

			<v-btn small primary round class="mt-3 ml-0" @click="answer">Answer</v-btn>
			
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
			value: {
				item_1: [],
				item_2: []
			},
			is_finish_data: this.is_finish
		};
	},
	mounted: function(){

		var _self = this;

	  	this.sorting_1 = jQuery(this.$el).find('.sort-option__item_1').sortable({
	  		container: jQuery(this.$el)
	    });

	    this.sorting_2 = jQuery(this.$el).find('.sort-option__item_2').sortable({
	  		container: jQuery(this.$el)
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

			this.value.item_1 = this.sorting_1.sortable('toArray');
	        this.value.item_2 = this.sorting_2.sortable('toArray');
			
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
				
				_self.is_finish_data = true;
				_self.$emit('answer', response.data.result);
			});
		}
	}
}
</script>