<template>
	<div :class="{quiz_play_finish: is_finish_data}" class="quiz-play-item quiz-play-text">
		<v-container fluid grid-list-lg>
			<v-layout row wrap play-content>
				<v-flex sm12 class="content">
					<div>
						{{content.content}}
					</div>

					<aws-player :music="content.musics.songs" :settings="content.musics.settings" ref="player"></aws-player>

				</v-flex>
			</v-layout>
			<v-layout row wrap class="play-answers ans-text">
				<v-flex sm8>
					<template v-for="(q, index) in content.options">
						
						<div class="answer" @click="answer(q.id)">
							{{ q.content }}
						</div>

					</template>
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
		answer: function( selected ){

			var _self = this;

			jQuery.post( this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'answer',
					qid: this.id,
					answer: {
						id: this.content.id,
						selected: selected
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