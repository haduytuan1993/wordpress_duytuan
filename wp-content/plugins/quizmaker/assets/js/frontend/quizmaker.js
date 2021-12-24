
function aws_inherit(proto) {
  function F() {}
  F.prototype = proto;
  return new F;
}

jQuery( function( $ ) {
	
	QM_Question = {
		enable: true,
		init: function(){

			this.explanation = this._explanation();

		},
		get_container: function() {

			return this.container;
		},
		get_id: function() {

			return parseInt(this.container.data('id'));
		},
		get_answer: function() {
				
			return { id: this.get_id(), value: this.get_value(), type: this.type };
		},
		_explanation: function(){

			var _s = this;

			var explanation = {
				init: function(){

					var _self = this;

					this.container = _s.container.find('.explanation');
					this.button	   = _s.container.find('.info_explanation');

					this.button.click(function(){

						if($(this).hasClass('active')){

							$(this).removeClass('active');
						}else{
							$(this).addClass('active');
						}

						_self.toggle();
					});

				},
				toggle: function(){

					if( this.container.hasClass('s') ){

						this.hide();

					}else{

						this.show();
					}
				},
				show: function(){

					this.container.removeClass('h').addClass('s');
				},
				hide: function(){

					this.container.removeClass('s').addClass('h');
				}
			};

			explanation.init();
			
			return	explanation;
		},
		disable: function(){

			this.enable = false;
		}
	};

	QM_Question_Single = function( container ) {

		var Func = function( container ){

			var _s 				=	this;

			this.type 		=	'single';
			this.container 	= 	container;

			var inputs = container.find('input');

			var response = {
				init: function(){
					
					this._events();
					_s.init();
				},
				_events: function(){

					var _self = this;

					inputs.change(function(){

						if( !_s.enable ){ return false; }

						var data = { question: container, complete: false, type: 'single' };
						
						if( _s.is_complete() ) {

							data.complete = true;
							data.value    = _s.get_value();
						}

						container.trigger( '_qm_engine_question_change', data );
					});

					var group_radio = container.find('.group-box-radio');

					group_radio.click(function(e){

						if( !_s.enable ){ return false; }

						group_radio.removeClass('checked');

						$(this).addClass('checked');
					});
				}
			};

			response.init();

			this.get_value = function() {

				var value = inputs.filter(':checked').val();

				if( typeof value != 'undefined') {

					return parseInt(value);
				}
			}

			this.is_complete = function() {

				if( typeof this.get_value() != 'undefined' ){

					return true;
				}

				return false;
			}

			this.show_anwers = function( data ) {

				this.disable();

				var checked    =	container.find('.group-box-radio.checked');

				if( data.is_correct == 1 ) {

					checked.addClass('ans_right');

				}else{
					
					checked.addClass('ans_wrong');

					container.find('.group-box-radio').each(function(){

						var box = $(this),
							id  = $(this).data('value');

						$.each(data.answer, function( index, ans ){

							if( id == index && ans.is_correct == 1 ){

								box.addClass('ans_right');
							}

						});
					});
					
				}
			}
		}

		Func.prototype 		= QM_Question;

		return new Func( container );
	}

	QM_Question_Multiple = function( container ) {

		var Func = function( container ){

			var _s 				=	this;

			this.type 		=	'multiple';
			this.container 	= 	container;

			var inputs 	= container.find('.checkbox_input');

			var response = {
				init: function(){

					this._events();
					_s.init();
				},
				_events: function(){

					var _self = this;

					inputs.change(function(){

						if( !_s.enable ){ return false; }

						var data = { question: container, complete: false, type: 'multiple' };
						
						if( _s.is_complete() ) {

							data.complete = true;
							data.value    = _s.get_value();
						}

						container.trigger( '_qm_engine_question_change', data );
					});

					container.find('.group-box-checkbox').click(function(e){

						if( !_s.enable ){ return false; }

						if($(this).hasClass('checked')) {

							$(this).removeClass('checked');

						}else{

							$(this).addClass('checked');

						}
					});
				}
			};

			response.init();

			this.get_value = function() {

				var values = [];

				inputs.each(function(){

					if($(this).is(':checked')) {

						values.push($(this).val());
					}
				});
				
				if(values.length > 0) {

					return values;
				}
			}

			this.is_complete = function() {

				if( this.get_value() ){

					return true;
				}

				return false;
			}

			this.show_anwers = function( data ) {

				this.disable();

				var checked    =	container.find('.group-box-checkbox.checked');

				if( data.is_correct == 1 ) {

					checked.addClass('ans_right');

				}else{
					
					checked.addClass('ans_wrong');

					container.find('.group-box-checkbox').each(function(){

						var box = $(this),
							id  = $(this).data('value');

						$.each(data.answer, function( index, ans ){

							if( id == index && ans.is_correct == 1 ){

								box.addClass('ans_right');
							}

						});
					});
					
				}

			}
		}


		Func.prototype 		= QM_Question;

		return new Func( container );
	}

	QM_Question_Fillblank = function( container ) {

		var Func = function( container ){

			var _s 				=	this;

			this.type 		=	'fill_blank';
			this.container 	= 	container;

			var input = container.find('textarea');

			var response = {
				init: function(){

					this._events();
					_s.init();
				},
				_events: function(){

					var _self = this;

					input.change(function(){

						if( !_s.enable ){ return false; }

						var data = { question: container, complete: false, type: 'fill_blank' };
						
						if( _s.is_complete() ) {

							data.complete = true;
							data.value    = _s.get_value();
						}

						container.trigger( '_qm_engine_question_change', data );
					});

				}
			};

			response.init();

			this.get_value = function() {

				return input.val();
			}

			this.is_complete = function() {

				
				if( this.get_value() ){

					return true;
				}

				return false;
			}

			this.disable = function() {

				this.enable	 = false;

				container.find('textarea').attr('disabled', true);
			}

			this.show_anwers = function( data ) {

				this.disable();

				var el_answers = container.find('.group-box-fill-blank');

				if( data.is_correct == 1 ){

					el_answers.addClass('ans_right');
					
				}else{

					var result = $('<div class="result"></div>');

					result.html(data.answer);

					el_answers.addClass('ans_wrong');

					el_answers.append(result);
				}

			}
		}

		Func.prototype 		= QM_Question;

		return new Func( container );
	}

	QM_Question_Order     = function( container ) {

		var Func = function( container ){

			var _s 			=	this,
				is_change	=	false;

			this.type 		=	'order';
			this.container 	= 	container;

			var inputs = container.find('.group-order');

			var axis   = 'y';

			if( inputs.hasClass('style-line') ) {

				axis = 'x';
			}

			var response = {
				init: function(){

					this.inputs = container.find('.group-order');
					this._events();

					_s.init();
				},
				_events: function(){

					var _self = this;

					this.inputs.sortable({
						items: '> li',
						placeholder: "ui-sortable-placeholder",
						axis: axis,
						create: function( event, ui ){
							//_self.sidebar.setQuestion({ id: question.data('id'), page: page, hasData: 1 });
						},
						change: function( event, ui ){
							
							var data = { question: container, complete: false, type: 'order' };

							if( _s.is_complete() ) {

								data.complete = true;
								data.value    = _s.get_value();
							}

							is_change	=	true;

							container.trigger( '_qm_engine_question_change', data );
						}
					});

				}
			};

			response.init();

			this.get_value = function() {

				if( is_change ){
					return container.find('.group-order').sortable('toArray', { attribute: 'data-id' });
				}else{

					return false;
				}
			}

			this.is_complete = function() {
				
				if( this.get_value() ){

					return true;
				}

				return false;
			}

			this.disable = function() {

				this.enable = false;

				container.find('.group-order').sortable( "disable" );
			}

			this.show_anwers = function( data ) {

				this.disable();
				
				var el_answers = container.find('.group-order');

				if( data.is_correct == 1 ){

					el_answers.addClass('ans_right');
					
				}else{

					var result = $('<ul class="group-order result"/>');

					$.each(data.answer, function(index, value){

						result.append($('<li class="group-box-order"><div class="answers_content">' + value.content + '</div></li>'));
					});

					el_answers.addClass('ans_wrong');

					el_answers.after(result);
				}
			}
		}

		Func.prototype 		= QM_Question;

		return new Func( container );
	}

	QM_Question_Dragmatch = function( container ) {

		var Func = function( container ){

			var _s 				=	this;

			this.type 		=	'drag_match';
			this.container 	= 	container;

			var group_questions =	container.find('.match-question'),
			    group_answers	=	container.find('.match-answer');

			var response = {
				init: function(){

					this._events();
					_s.init();
				},
				_events: function(){

					var _self = this;

					group_questions.draggable({ 
							containment: container,
							revert: 'invalid',
							cursor: "move"
						});
						
					container.find('.match-question-container').droppable({
						drop: function( event, ui ) {
							
							if($(this).find('.match-question').length == 0) {
								ui.draggable.detach().css({top: 0,left: 0}).appendTo($( this ));
								
								
							}else{
								ui.draggable.draggable( "option", "revert", true );
							}
							
						}
					});

					var hasValue		=	function(){
						
						var inputAnswers	=	group_answers.find('.q'),
							isValue			=	false;
						
						if( _s.get_value().length > 0 ) {

							var data = { question: container, complete: false, type: 'drag_match' };

							if( _self.is_complete() ) {

								data.complete = true;
								data.value    = _self.get_value();
							}

							container.trigger( '_qm_engine_question_change', data );
						}


						inputAnswers.each(function(){
							
							if($(this).val() != '' && isValue == false) {
								isValue = true;

								
							}
							
						});

					}
					
					group_answers.each(function(){

						$(this).droppable({
							activeClass: "ui-state-hover",
							hoverClass: "ui-state-active",
							drop: function( event, ui ) {
							
								if($(this).find('.match-question').length == 0) {
									ui.draggable.detach().css({top: 0,left: 0}).appendTo($( this ).find('.value'));
								
									$(this).find('.q').val(ui.draggable.data('value'));
								
								}else{
									ui.draggable.draggable( "option", "revert", true );
								}
								
								$(this).addClass( "ui-state-highlight" );
								
								hasValue();
								
							},
							out: function( event, ui ){
								
								var out_value	=	ui.draggable.data('value'),
									cur_value	=	$(this).find('.q').val();
								
									if(out_value == cur_value){
										$(this).find('.q').val('');
									}
									
									hasValue();
							}
					    });
					});

				},
				set_value: function( value ) {

				},
				get_value: function() {

					var inputAnswers	=	group_answers.find('.q');

					return inputAnswers.val();
				},
				is_complete: function() {

					if( this.get_value() != '' ){

						return true;
					}

					return false;
				}
			};

			response.init();

			this.get_value = function() {

				var values = [];

				group_answers.each(function(index, value){

					var v = $(this).find('.q').val();

					if( v != '' ){

						values[$(this).data('id')] = v;
					}
				});
				
				if( values.length == group_answers.length ){
					
					return values;
				}else{

					return false;
				}
			}

			this.is_complete = function() {
				
				if( this.get_value() != '' ){

					return true;
				}

				return false;
			}

			this.disable = function() {

				this.enable = false;

				group_questions.draggable( "disable" );
				group_answers.droppable( "disable" );
			}

			this.show_anwers = function( data ) {

				this.disable();
				
				var el_answers = container.find('.group-drag-match');

				if( data.is_correct == 1 ){

					el_answers.addClass('ans_right');
					
				}else{

					var result = $('<div class="group-drag-match result"><div class="match-answers"></div></div>');
					
					$.each(data.answer, function(index, value){

						result.append($('<div class="match-answer"><div class="label">' + value.answer + '</div><div class="value"><div class="match-question">' + value.content + '</div></div></div>'));
					});

					el_answers.addClass('ans_wrong');

					el_answers.after(result);
				}
				
			}
		}

		Func.prototype 		= QM_Question;

		return new Func( container );
	}

	QM_Engine_Questions = 	function( container, options ) {

		options = $.extend({ total: 1 }, options);

		var events		=	['complete'],
			questions 	= 	[];

		return {
			init: function() {

				this._events();
			},
			_events: function() {

				var _self = this;

				container.on('_qm_engine_question_change', function( event, data ){

					if( _self.is_complete() ){

						container.trigger('_qm_engine_questions_complete', _self.get_complete());

					}

					if( data.complete ) {

						data.question.removeClass('s-0').addClass('s-1');

					}else{

						data.question.removeClass('s-1').addClass('s-0');
					}
				});
			},
			get_questions: function() {

				return questions;
			},
			add_question: function( question ) {

				var type		= question.data('type');

				switch( type ){

					case 'single':

						questions.push( new QM_Question_Single( question ) );

					break;

					case 'multiple':

						questions.push( new QM_Question_Multiple( question ) );

					break;

					case 'fill_blank':

						questions.push( new QM_Question_Fillblank( question ) );

					break;

					case 'order':

						questions.push( new QM_Question_Order( question ) );

					break;

					case 'drag_match':

						questions.push( new QM_Question_Dragmatch( question ) );

					break;
				}
			},
			get_result: function() {

				var answers = [];

				$.each(questions, function( index, question ){

					answers.push(question.get_answer());
				});

				return answers;
			},
			is_complete: function() {

				var total = options.total;

				if( total > 0 ) {

					var complete = this.get_complete();

					if( complete.length == total ) {

						return true;
					}
				}

				return false;
			},
			get_complete: function() {

				var complete = [];

				if( questions.length > 0 ) {

					$.each( questions, function( index, question ){

						if( question.is_complete() ) {

							complete.push( question );
						}

					});

				}

				return	complete;
			},
			show_anwers: function( answers ) {
				
				$.each(questions, function( index, question ){

					$.each(answers, function( index, ans ){

						if( question.get_id() == ans.id ){

							question.show_anwers( ans );
						}

					});

				});
			},
			disable: function() {

				$.each(questions, function( index, question ){

					question.disable();

				});
			},
			on: function( event, func ) {

				switch( event ){

					case 'complete':

						this.on_complete( func );

					break;

					case 'change':

						this.on_change( func );

					break;
				}

			},
			on_complete: function( func ) {

				var _self = this;

				container.on('_qm_engine_questions_complete', function(){

					func();
				});
			},
			on_change: function( func ) {

				container.on('_qm_engine_question_change', function(){

					func();
				});
			}
		}
	}

	
});