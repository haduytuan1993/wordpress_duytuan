"use strict";
import Vue from 'vue';

import Vuetify from 'vuetify';
import VeeValidate from 'vee-validate';
import Stopwatch from './components/stopwatch.vue';


Window.Vue = Vue;

Vue.config.debug = true;
Vue.config.silent = false;
Vue.config.devtools = true;


Vue.use(Vuetify);
Vue.use(VeeValidate);

jQuery(document).ready(function() {

	Vue.component('star-rating', {
	  props: {
	    'name': String,
	    'value': null,
	    'value_t': null,
	    'id': String,
	    'disabled': Boolean,
	    'required': Boolean
	  },
	  template: '<div class="qm-star-rating">\
	        <label class="star-rating__star" v-for="rating in ratings" \
	        :class="{\'is-selected\': ((ve >= rating) && ve != null), \'is-hover\': ((vt >= rating) && vt != null), \'is-disabled\': disabled}" \
	        v-on:click="set(rating)" v-on:mouseover="star_over(rating)" v-on:mouseout="star_out"><i class="material-icons">grade</i></label></div>',

	  /*
	   * Initial state of the component's data.
	   */
	  data: function() {
	    return {
	      temp_value: null,
	      ratings: [1, 2, 3, 4, 5],
	      vt: this.value_t,
	      ve: this.value
	    };
	  },
	  methods: {
	    /*
	     * Behaviour of the stars on mouseover.
	     */
	    star_over: function(index) {
	      var self = this;

	      if (!this.disabled) {
	        this.temp_value = this.vt;
	        return this.vt = index;
	      }

	    },

	    /*
	     * Behaviour of the stars on mouseout.
	     */
	    star_out: function() {
	      var self = this;

	      if (!this.disabled) {
	        return this.vt = this.temp_value;
	      }
	    },

	    /*
	     * Set the rating of the score
	     */
	    set: function(value) {
	      var self = this;

	      if (!this.disabled) {
	      	// Make some call to a Laravel API using Vue.Resource
	        
	        this.temp_value = value;
	        this.ve = value;

	        this.$emit('change', this.ve);
	      }
	    }
	  }

	});

	if(jQuery('#quizmaker-result').length > 0){

		new Vue({
			mixins: [window.vuelidate.validationMixin],
			el: '#quizmaker-result',
			data: {
				page: 0,
				show_explanation: false,
				id_report: 0,
				is_report_status: 0,
				question_report_message: ''
			},
			mounted: function(){

				var questions = jQuery(this.$el).find('.question');

				questions.each(function(){

					var question = jQuery(this);

					question.find('.info_explanation').click(function(){

						var ex = question.find('.explanation');

						if(ex.hasClass('show')){

							ex.removeClass('show');
						}else{

							ex.addClass('show');
						}
					});
				});
			},
			methods: {
				showReport: function( id ) {

					this.id_report = id;
				},
				closeReport: function() {

					this.id_report = 0;
					this.question_report_message = '';

					this.is_report_status = 0;
				},
				submitReport: function(){

					if( !this.question_report_message ) { return false; }

					var _self = this;

					jQuery.post( quizmaker.ajax_url, { 
						action: "quizmaker_question_report", 
						security: quizmaker.security_question_report, 
						data: {
							id: this.id_report,
							content: this.question_report_message
						}
					} ).then(function(response){

						_self.question_report_message = '';

						_self.is_report_status = 1;

						setTimeout(function(){

							_self.closeReport();

						}, 4000);
					});
					
				},
				rateTest: function( value ){

					jQuery.post( quizmaker.ajax_url, { 
						action: "quizmaker_test_rating_from_result", 
						security: quizmaker.security_test_rating_from_result, 
						data: {
							test_id: quizmaker.test_id,
							star: value
						}
					}).then(function(response){

						console.log(response);
					});
				},
				shareResult: function( title, href, image_result ) {

					FB.ui({
					  method: 'share_open_graph',
					  action_type: 'og.shares',
					  display: 'popup',
					  action_properties: JSON.stringify({
					    object: {
					      'og:url': href,
					      'og:title': title,
					      'og:image': image_result
					    }
					  })
					});
				}
			}
		});

	}

	if(jQuery('#quizmaker-fillform-container').length > 0){ 

		new Vue({
			el: '#quizmaker-fillform-container'
		});
	}

	if(jQuery('#qm-single-test').length > 0){

		new Vue({
			el: '#qm-single-test',
			data: {
				listSelected: []
			}
		});
	}

	if(jQuery('#qm-page-doing').length > 0){
		
		new Vue({
			components: {
				'Stopwatch': Stopwatch
			},
			el: '#qm-page-doing',
			data: {
				is_submited: false,
				is_show_sidebar: false,
				pages: 1,
				page: 1,
				is_last_page: false,
				is_loading: false,
				confirmSubmitAction: false,
				time_passed: quizmaker.time_passed
			},
			mounted: function(){

				var _self = this;

				jQuery('body').on('quizmaker_event_changed_page', function( event, data ){
					
					_self.page = parseInt(data.page);
					_self.is_last_page = data.is_last;
				});

				jQuery('body').on('quizmaker_doing_loading', function( event, is_loading){

					_self.is_loading = is_loading;
				});

				this.pages = parseInt(jQuery(this.$el).data('pages'));
			},
			methods: {
				toggleSidebar: function(){

					this.is_show_sidebar = !this.is_show_sidebar;
				},
				submitTest: function( force ){

					if( !this.confirmSubmitAction && !force ) {

						this.confirmSubmitAction = true;

					}else{

						this.confirmSubmitAction = false;

						if( !this.is_submited ){

							this.is_submited = true;
							
							jQuery(this.$el).find('.qm-btn-submit-test').trigger('click');
						}
					}
					
				},
				cancelTest: function(){
					
					window.location = quizmaker.post_link;
				},
				nextPage: function(){

					jQuery('body').trigger('quizmaker_on_next_page');
				},
				prevPage: function(){

					jQuery('body').trigger('quizmaker_on_prev_page');
				}
			}
		});
	}

	if(jQuery('#qm-my-account-table__view-assigned-tests').length > 0){

		new Vue({
			el: '#qm-my-account-table__view-assigned-tests',
			data: {
				drawer: true,
				right: null
			}
		});
	}

	if(jQuery('#qm-myaccount-form-edit-account').length > 0){

		new Vue({
			el: '#qm-myaccount-form-edit-account',
			data: {
				drawer: true,
				right: null
			}
		});
	}

	if(jQuery('#qm-my-account-table__view-results').length > 0){

		new Vue({
			el: '#qm-my-account-table__view-results',
			data: {
				drawer: true,
				right: null
			}
		});
	}

	if(jQuery('#qm-my-account-table__view-certificates').length > 0){

		new Vue({
			el: '#qm-my-account-table__view-certificates',
			data: {
				drawer: true,
				right: null
			}
		});
	}

	if(jQuery('.qm-shortcode-quiz').length > 0){

		jQuery('.qm-shortcode-quiz').each(function(){

			new Vue({
				el: '#' + jQuery(this).find('.reset-vuetify').attr('id')
			});

		});
	}
	
});