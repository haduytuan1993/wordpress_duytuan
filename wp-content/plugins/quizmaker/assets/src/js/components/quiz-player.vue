<template>
	<div class="quiz-player">
		
		<div v-if="steps == 1" class="quiz-start">
			<div v-if="settings.cover_image_src" v-bind:style="{backgroundImage: 'url('+ settings.cover_image_src[0] + ')'}" class="cover-image"></div>
			<div @click="start" class="quiz-start_icon"><v-icon class="white--text">play_circle_outline</v-icon></div>
		</div>

		<div v-if="steps == 2">

			<div>
				<div class="pagination">
					<span class="num-1">{{page+1}}</span>
					<span class="num-2">/{{question_content.pages}}</span>
				</div>
			</div>
			<div>
				<quiz-play-video v-if="question_videos.length > 0" :videos="question_videos" v-on:finish="finishVideos"></quiz-play-video>

				<!-- Play Type Text -->
				<quiz-play-text v-if="!questionResult.is_show && question_type == 1" :id="id" :ajax="ajax" :content="question_content" :is_timeout="is_timeout" v-on:answer="finishQuestion"></quiz-play-text>
				
				<!-- Play Type Image -->
				<quiz-play-image v-if="!questionResult.is_show && question_type == 2" :id="id" :ajax="ajax" :content="question_content" :is_timeout="is_timeout" v-on:answer="finishQuestion"></quiz-play-image>
				
				<!-- Play Type Scale -->
				<quiz-play-scale v-if="!questionResult.is_show && question_type == 3" :id="id" :ajax="ajax" :content="question_content" :is_timeout="is_timeout" v-on:answer="finishQuestion"></quiz-play-scale>

				<!-- Play Type Sort -->
				<quiz-play-sort v-if="!questionResult.is_show && question_type == 4" :id="id" :ajax="ajax" :content="question_content" :is_timeout="is_timeout" v-on:answer="finishQuestion"></quiz-play-sort>

				<!-- Play Type Guess -->
				<quiz-play-guess v-if="!questionResult.is_show && question_type == 5" :id="id" :ajax="ajax" :content="question_content" :is_timeout="is_timeout" v-on:answer="finishQuestion"></quiz-play-guess>

				<!-- Play Type Keyword -->
				<quiz-play-keyword v-if="!questionResult.is_show && question_type == 6" :id="id" :ajax="ajax" :content="question_content" :is_timeout="is_timeout" v-on:answer="finishQuestion"></quiz-play-keyword>

				<!-- Play Type Item match -->
				<quiz-play-itemmatch v-if="!questionResult.is_show && question_type == 7" :id="id" :ajax="ajax" :content="question_content" :is_timeout="is_timeout" v-on:answer="finishQuestion"></quiz-play-itemmatch>

				<!-- Play Type Group match -->
				<quiz-play-groupmatch v-if="!questionResult.is_show && question_type == 8" :id="id" :ajax="ajax" :content="question_content" :is_timeout="is_timeout" v-on:answer="finishQuestion"></quiz-play-groupmatch>
			</div>
			
			<div class="tracking">
				<v-container fluid grid-list-lg>
				<v-layout row wrap>
					<v-flex sm10>
						
						<v-slider hide-details prepend-icon="alarm" v-model="question_duration" thumb-label step="1" min="0" :max="max_duration" disabled></v-slider>
						
					</v-flex>
					<v-flex sm2>
						<div class="points">
							<!-- <span v-text="question_content.settings.points"></span> -->
						</div>
					</v-flex>
				</v-layout>
				</v-container>
			</div>

		</div>
		<!-- <div v-if="steps == 3" v-html="final_step" class=""> -->
		<div v-if="steps == 3" class="final_step">
			
			<div class="final_step_info points">
				<span class="lbl">Point</span>
				<span class="value" v-text="final_result.points"></span>
			</div>

			<div class="final_step_info ranking">
				<span class="lbl">Ranking</span>
				<span class="value" v-text="final_user_ranking">1</span>
			</div>
			
			<v-layout row wrap>
				<v-flex sm6>
					<div class="head-action"><span v-text="final_result.duration"></span></div>
					
					<div class="wrap-rating">
						
						<star-rating v-model="rating" v-on:change="setRating"></star-rating>

					</div>

					<v-btn round outline dark @click="playAgain"><v-icon left>autorenew</v-icon>Play Again</v-btn>
				</v-flex>

				<v-flex sm6>
					<div class="head-ranking">Ranking</div>
					<table class="head-ranking-data">
						<tbody>
							<tr v-for="(rk, index) in final_ranking">
								<td class="text-xs-center order">{{index + 1}}</td>
								<td>{{rk.user_name}}</td>
								<td class="points">{{rk.score}}</td>
							</tr>
						</tbody>
					</table>
				</v-flex>
			</v-layout>
		
		</div>

		<div v-if="questionResult.is_show" class="question-result">
			
			<div>
				<div class="part-1">
					<span v-if="questionResult.result">Correct!</span>
					<span v-if="!questionResult.result">Wrong!</span>
				</div>

				<div class="part-2">
					Total points: {{questionResult.points}}
				</div>

				<div class="part-3 mb-3">
					
					<div class="status-results">
						
						<template v-for="(q, i) in question_content.pages">
							
							<template v-if="i < questionResult.tracking.length">
								<v-btn v-if="questionResult.tracking[i][0]" small dark fab icon class="green">
									<span v-text="q" class="white--text"></span>
								</v-btn>
								<v-btn v-else small dark fab icon class="red">
									<span v-text="q" class="white--text"></span>
								</v-btn>
							</template>
							
							<v-btn v-else small dark fab icon class="white">
								<span v-text="q" class="black--text"></span>
							</v-btn>
							
						</template>

					</div>

				</div>

				<div class="part-5 player-form-1" v-if="is_last && !sandbox">
					
					<form>
						<div class="group-input">
							<label>Your Name</label>
							<input v-validate="'required|max:255'" :class="{'is-danger': errors.has('input_your_name') }" type="text" name="input_your_name" v-model="input_your_name"/>
						</div>
						<div class="group-input">
							<label>Email</label>
							<input v-validate="'required|email'" :class="{'is-danger': errors.has('input_your_email') }" type="text" name="input_your_email" v-model="input_your_email"/>
						</div>
					</form>

				</div>
				
				<div class="part-4">
					<v-btn v-if="!is_last" dark round primary @click.stop="nextQuestion">Next Question</v-btn>

					<v-btn v-if="!sandbox && is_last && (settings.is_share_for_view_result == 'true')" dark round large primary @click.stop="finish(1)"><v-icon left>share</v-icon>Share Social for get the result</v-btn>
					
					<v-btn v-if="!sandbox && is_last && (settings.is_share_for_plus_points == 'true')" dark round outline large @click.stop="finish(2)"><v-icon left>share</v-icon>Share Social for plus {{settings.plus_points_when_share}} points</v-btn>

					<v-btn v-if="!sandbox && is_last && (settings.is_share_for_plus_points == 'true')" dark round large primary @click.stop="finish(0)"><v-icon left>visibility</v-icon>View Result</v-btn>
				</div>
			</div>

		</div>
		
		<v-snackbar :bottom="true" v-model="message.enable" :color="message.type">
        {{message.content}}
        <v-btn dark flat @click.native="message.enable = false">Close</v-btn>
      </v-snackbar>

	</div>
</template>
<script>

import QuizPlayVideo from "./quiz-play-video.vue";
import QuizPlayText from "./quiz-play-text.vue";
import QuizPlayImage from "./quiz-play-image.vue";
import QuizPlayScale from "./quiz-play-scale.vue";
import QuizPlaySort from "./quiz-play-sort.vue";
import QuizPlayGuess from "./quiz-play-guess.vue";
import QuizPlayKeyword from "./quiz-play-keyword.vue";
import QuizPlayItemmatch from "./quiz-play-itemmatch.vue";
import QuizPlayGroupmatch from "./quiz-play-groupmatch.vue";

export default {
	components: {
		'quiz-play-video': QuizPlayVideo,
		'quiz-play-text': QuizPlayText,
		'quiz-play-image': QuizPlayImage,
		'quiz-play-scale': QuizPlayScale,
		'quiz-play-sort': QuizPlaySort,
		'quiz-play-guess': QuizPlayGuess,
		'quiz-play-keyword': QuizPlayKeyword,
		'quiz-play-itemmatch': QuizPlayItemmatch,
		'quiz-play-groupmatch': QuizPlayGroupmatch
	},
	props: {
		sandbox: {
			type: Boolean,
			default: function(){
				return false;
			}
		},
		id: {
			type: Number,
			default: function(){
				return 0;
			}
		},
		size: {
			type: Number,
			default: function(){

				return 1;
			}
		}
	},
	data: function(){

		var params = {
			message: {
				enable: false,
				type: 'success',
				content: 'Success'
			},
			steps: 1,
			final_result: {
				points: 0
			},
			page: 0,
			settings: {},
			question_type: 0,
			question_duration: 0,
			question_content: {},
			question_videos: [],
			questionResult: {
				is_show: false,
				result: false,
				points: 0,
				tracking: []
			},
			max_duration: 0,
			trackingTime: false,
			is_timeout: false,
			is_last: false,
			timeout: false,
			loading: false,
			rating: 0,
			is_rated: false,
			input_your_name: '',
			input_your_email: ''
		};

		if( this.sandbox ) {

			params.ajax = {
				url: qm_admin.ajax_url,
				security: qm_admin.security,
				action: 'quiz_resource'
			};

		}else{

			params.ajax = {
				url: qm_front.ajax_url,
				security: qm_front.security,
				action: 'quizmaker_quiz_player'
			};
		}

		return params;
		
	},
	watch: {
		rating: function( rating_1, rating_2 ) {

			console.log(rating_1, rating_2);
		}
	},
	mounted: function(){

		jQuery(this.$el).addClass('size-' + this.size);

		this.intro();
	},
	methods: {
		intro: function(){

			var _self = this;

			this.steps = 1;

			jQuery.post( this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'intro',
					qid: this.id
				}
			}, function( response ){
				
				_self.settings = response.data.settings;
				
			});
			
		},
		start: function(){

			var _self = this;

			jQuery.post( this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'setup',
					qid: this.id
				}
			}, function( response ){
				
				// _self.settings = response.data.settings;
				
				_self.doing();
			});
		},
		doing: function(){

			var _self = this;

			this.loading = true;

			jQuery.when(this.loadQuestion()).then(function(response){

				_self.steps = 2;

				_self.loading = false;

				_self.is_timeout = false;
				
				if( typeof _self.question_content.videos == 'undefined' || _self.question_content.videos.length == 0 ){

					_self.question_type 	= parseInt(_self.question_content.type);
					
					_self.startTrackingTime( _self.max_duration );

				}else{

					_self.question_videos = _self.question_content.videos;
				}
			});

		},
		nextQuestion: function(){

			var _self = this;

			this.page++;

			this.loading = true;

			jQuery.when(this.loadQuestion()).then(function(response){

				_self.loading = false;

				_self.questionResult.is_show = false;
				_self.questionResult.result  = false;

				if( typeof _self.question_content.videos == 'undefined' || _self.question_content.videos.length == 0 ){

					_self.question_type = _self.question_content.type;

					_self.is_timeout = false;

					_self.startTrackingTime( _self.max_duration );

				}else{

					_self.question_type	  = 0;
					_self.question_videos = _self.question_content.videos;
				}

			});
		},
		reset: function(){

			this.question_duration = 0;
			this.stopTrackingTime();
			this.steps = 1;

		},
		startTrackingTime: function( duration ){

			if( duration == 0 ) { this.stopTrackingTime(); return; }

			var _self = this;

			if( !this.is_timeout && (_self.question_duration <= duration) ) {
				
				this.timeout = setTimeout(function(){
					
					_self.question_duration++;
					_self.startTrackingTime(duration);

				}, 1000);

			}else{

				this.is_timeout = true;
			}
		},
		stopTrackingTime: function(){

			clearTimeout(this.timeout);

			this.is_timeout = true;
			this.question_duration = 0;

		},
		loadQuestion: function(){

			var _self = this;
			var defer = jQuery.Deferred();

			jQuery.post( this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'play',
					qid: this.id,
					page: this.page
				}
			}, function( response ){
				
				_self.question_content  = response.data.item;



				_self.is_last 			= _self.question_content.is_last;

				
				if( _self.is_last ){
					
					_self.input_your_name 	= response.data.item.user.your_name;
					_self.input_your_email 	= response.data.item.user.your_email;
				}

				_self.max_duration 		= _self.question_content.settings.duration;

				defer.resolve(response);

			});

			return defer.promise();
		},
		finish: function( type ){

			var _self = this;
			var defer = jQuery.Deferred();

			this.$validator.validateAll().then(function(is_validate){

				if( is_validate ){
					
					if( type == 1 || type == 2 ){

						FB.ui({
		  					method: 'share',
		  					href: qm_front.current_url,
						}, function(response){

							if( response ){

								var plusPoints = 0;

								if( type == 2 ) {

									plusPoints = 1;
								}

								jQuery.post( _self.ajax.url, {
									security: _self.ajax.security,
									action: _self.ajax.action,
									data: {
										method: 'finish',
										qid: _self.id, 
										plus_points: plusPoints,
										user: {
											'your_name': _self.input_your_name,
											'your_email': _self.input_your_email
										}
									}
								}, function( response ){
									
									_self.final_result  = response.data.result;
									_self.final_ranking = response.data.ranking;
									_self.final_user_ranking = response.data.user_ranking;

									_self.steps = 3;

									_self.questionResult.is_show = false;

									defer.resolve(response);

								});

							}
							
						});

					}else{

						jQuery.post( _self.ajax.url, {
							security: _self.ajax.security,
							action: _self.ajax.action,
							data: {
								method: 'finish',
								qid: _self.id, 
								user: {
									'your_name': _self.input_your_name,
									'your_email': _self.input_your_email
								}
							}
						}, function( response ){
							
							_self.final_result  = response.data.result;
							_self.final_ranking = response.data.ranking;
							_self.final_user_ranking = response.data.user_ranking;

							_self.steps = 3;

							_self.questionResult.is_show = false;

							defer.resolve(response);

						});

					}

					// jQuery.post( _self.ajax.url, {
					// 	security: _self.ajax.security,
					// 	action: _self.ajax.action,
					// 	data: {
					// 		method: 'finish',
					// 		qid: _self.id, 
					// 		user: {
					// 			'your_name': _self.input_your_name,
					// 			'your_email': _self.input_your_email
					// 		}
					// 	}
					// }, function( response ){
						
					// 	_self.final_result  	 = response.data.result;
					// 	_self.final_ranking 	 = response.data.ranking;
					// 	_self.final_user_ranking = response.data.user_ranking;
					// 	_self.steps = 3;

					// 	_self.questionResult.is_show = false;

					// 	defer.resolve(response);

					// });

				}
					
			});

			

			return defer.promise();
		},
		playAgain: function(){

			location.reload();
		},
		setRating: function( rating ){

			if( this.is_rated ) {

				return false;
			}
			
			var _self = this;

			jQuery.post( this.ajax.url, {
				security: this.ajax.security,
				action: this.ajax.action,
				data: {
					method: 'rating',
					qid: this.id, 
					rating: rating
				}
			}, function( response ){
				
				_self.is_rated = true;

				_self.message.enable = true;
				_self.message.content = 'Thank your rating!';

			});
		},
		finishVideos: function(){

			this.question_type = this.question_content.type;
			this.is_timeout = false;
			this.question_videos = [];

			this.startTrackingTime( this.max_duration );
		},
		finishQuestion: function( data ){

			this.stopTrackingTime();

			this.questionResult.tracking.push( data.result );
			this.questionResult.result = data.result[0];
			this.questionResult.points += parseInt(data.result[1]);

			this.questionResult.is_show = true;
		}
	}
}
</script>