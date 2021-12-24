<template>
	<div :class="{quiz_play_finish: is_finish_data}" class="quiz-play-item quiz-play-groupmatch">
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
				<v-flex xs12>
					
	        		<div class="sort-groups">
			              
						<div v-for="(q, index) in content.options" :key="q.id" :id="q.id" class="sort-group">
							
							<div class="sort-group_title">{{q.group}}</div>
							
							<div class="sort-items">
								<div v-for="(item, item_index) in q.items" :key="item_index" class="sort-item" :id="item">
									<div class="wrap-icon">
										<v-icon>swap_vert</v-icon>
									</div>
									<div class="content" v-text="item"></div>
								</div>
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
			is_finish_data: this.is_finish,
			sortings: []
		};
	},
	mounted: function(){

		var _self = this;

		jQuery(this.$el).find('.sort-items').each(function( index, group ){
			
			_self.sortings[index] = {
				'group': _self.content.options[index].group,
				'sorting': jQuery(group).sortable({
			  		connectWith: jQuery(_self.$el).find('.sort-items')
			    })
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

			var data = [];

			_.each( this.sortings, function( value, index ){

				data[index] = {
					group: _self.content.options[index].group,
					items: value.sorting.sortable('toArray')
				}

				
			});
			
			jQuery.post( this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'answer',
					qid: this.id,
					answer: {
						id: this.content.id,
						selected: data
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