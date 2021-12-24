jQuery( function( $ ) {
	
	function result_pagination(){
		
		var stage		=	$('.result-test-stage'),
			questions	=	stage.find('.stage-questions'),
			pagination	=	stage.find('.stage-pagination');
		
		var showPage	=	function(page){
			
			questions.find('.qm-page').removeClass('active').removeClass('loading');
			questions.find('.qm-page-' + page).addClass('active');
			
		}
		
		var pagi_items	=	pagination.find('li');
		
		pagi_items.click(function(e){
			
			var page	=	$(this).index() + 1;
			
			showPage(page);
			
			pagi_items.removeClass('active');
			$(this).addClass('active');
			
			e.preventDefault();
		});
		
		pagi_items.eq(0).addClass('active');
		
		showPage(1);
	}
	
	function QM_Result(){
				
		var stage_questions	=	$('.result-test-stage .stage-questions');
		
		return {
			id_report: 0,
			is_report_status: 0,
			question_report_message: '',
			init: function(){				
				
				this._events();
			},
			_events: function(){
				
				var _self	=	this;
				
				stage_questions.find('.info_explanation').click(function(){
					
					var current_question	=	$(this).parents('.question');
					
					var explanation	=	current_question.find('.explanation');
					
					if(explanation.hasClass('show')){
						explanation.removeClass('show');
						$(this).removeClass('active');

						explanation.hide();
					}else{
						$(this).addClass('active');
						explanation.addClass('show');

						explanation.show();
					}
				});

				stage_questions.fitVids();

				stage_questions.find('.question').each(function(){

					var id = $(this).data('id');

					$(this).find('.report').click(function(){
						
						_self.showReport( id );
					});

				});

				_self.closeReport();

				$('#cancel-question-report').click(function(e){

					e.preventDefault();

					_self.closeReport();
				});

				$('#submit-question-report').click(function(e){

					e.preventDefault();

					_self.question_report_message = $('#report-question-email').val();

					_self.submitReport();
				});

				$('#qm-share-result').click(function(e){

					e.preventDefault();

					_self.shareResult( $(this).data('title'), $(this).data('link'), $(this).data('image') );
				});

			},
			showReport: function( id ) {

				this.id_report = id;

				$('.result-report').show();
			},
			closeReport: function() {

				this.id_report = 0;
				this.question_report_message = '';

				this.is_report_status = 0;

				$('.result-report').hide();
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

					$('#report-question-email').val('');

					

					_self.is_report_status = 1;

					_self.closeReport();
					
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
		};
	}
	
	function qm_checkbox_toogle_group(){

		var action = {
			check: function( input ){

				var group = input.data('qm-checkbox-toogle');

				if( input.is(':checked') ){

					$(group).css('display', 'block');
				}else{

					$(group).css('display', 'none');
				}
			}
		};

		var inputs = $('input[type=checkbox]').filter(function(){

			var value = $(this).data('qm-checkbox-toogle');

			if( typeof value == 'undefined' || value == '' ){
				return false;
			}else{
				return true;
			}

		});

		inputs.each(function(){

			action.check($(this));
		});

		inputs.change(function(){

			action.check($(this));
		});
	}

	$(document).ready(function(){
		result_pagination();
		QM_Result().init();
	});
	
});
// jQuery( function( $ ) {
	
// 	function result_pagination(){
		
// 		var stage		=	$('.result-test-stage'),
// 			questions	=	stage.find('.stage-questions'),
// 			pagination	=	stage.find('.stage-pagination');
		
// 		var showPage	=	function(page){
			
// 			questions.find('.qm-page').removeClass('active').removeClass('loading');
// 			questions.find('.qm-page-' + page).addClass('active');
			
// 		}
		
// 		var pagi_items	=	pagination.find('li');
		
// 		pagi_items.click(function(e){
			
// 			var page	=	$(this).index() + 1;
			
// 			showPage(page);
			
// 			pagi_items.removeClass('active');
// 			$(this).addClass('active');
			
// 			e.preventDefault();
// 		});
		
// 		pagi_items.eq(0).addClass('active');
		
// 		showPage(1);
// 	}
	
// 	function QM_Result(){
				
// 		var stage_questions	=	$('.result-test-stage .stage-questions');
		
// 		return {
// 			init: function(){				
				
// 				this._events();
// 			},
// 			_events: function(){
				
// 				var _self	=	this;
				

// 				stage_questions.fitVids();

// 			}
// 		};
// 	}
	
// 	function qm_checkbox_toogle_group(){

// 		var action = {
// 			check: function( input ){

// 				var group = input.data('qm-checkbox-toogle');

// 				if( input.is(':checked') ){

// 					$(group).css('display', 'block');
// 				}else{

// 					$(group).css('display', 'none');
// 				}
// 			}
// 		};

// 		var inputs = $('input[type=checkbox]').filter(function(){

// 			var value = $(this).data('qm-checkbox-toogle');

// 			if( typeof value == 'undefined' || value == '' ){
// 				return false;
// 			}else{
// 				return true;
// 			}

// 		});

// 		inputs.each(function(){

// 			action.check($(this));
// 		});

// 		inputs.change(function(){

// 			action.check($(this));
// 		});
// 	}

// 	function qm_facebook_share_result( image_result ) {

		
// 	}

// });