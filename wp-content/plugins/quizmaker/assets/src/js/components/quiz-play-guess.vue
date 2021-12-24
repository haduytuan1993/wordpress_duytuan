<template>
	<div :class="{quiz_play_finish: is_finish_data}" class="quiz-play-item quiz-play-guess">
		<v-container fluid grid-list-lg>
			
			<div class="play-content">
				<div>
					{{content.content}}
				</div>

				<aws-player :music="content.musics.songs" :settings="content.musics.settings" ref="player"></aws-player>
			</div>
			
			<v-layout row wrap class="play-answers ans-guess">
				<v-flex sm8>
					
	        		<div class="guess-words">
			              
						<div v-for="(q, index) in content.options" :key="q.id" :id="q.id" class="guess-word">
							
							<input v-if="q.is_show == 0" type="text" class="guess-word_hide" v-model="q.answer"/>

							<div v-if="q.is_show == 1" class="guess-word_show" v-text="q.content"></div>

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
			

			var values = _.map(this.content.options, function(v){

				return _.pick(v, 'id', 'answer');
			});
			
			jQuery.post( this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'answer',
					qid: this.id,
					answer: {
						id: this.content.id,
						selected: values
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