jQuery( function( $ ) {
	
	// Type box
	$( '.meta-type-box' ).appendTo( '#quizmaker-answers-data .hndle span' );

	
	$( function() {
		
		$( '#quizmaker-answers-data' ).find( '.hndle' ).unbind( 'click.postboxes' );

		jQuery( '#quizmaker-answers-data' ).on( 'click', '.hndle', function( event ) {
			
			if ( $( event.target ).filter( 'input, option, label, select' ).length ) {
				return;
			}

			$( '#quizmaker-answers-data' ).toggleClass( 'closed' );
		});
	});
	
	$( 'select#answer-type' ).change( function () {
		
		var select_val = $( this ).val();
		
		$('.answer-type-panel').hide();
		$('#answer-type-' + select_val).show();
		
	}).change();
	
	var Answer	=	{
		answer_media_image: function( params ){
			
			params	=	$.extend({ is_remove: false }, params);
			
			var _self	=	this;
			
			if(!params.is_remove) {
				QM().WPMedia({
					selection: function( media ) {
						
						params	=	$.extend({ size: 'thumbnail' }, params);
					
						var image	=	params.container.find('img');
					
						if(image.length > 0) {
							image.attr('src', media.sizes[params.size].url);
						}else{
						
							image	=	$('<img/>');
							image.attr('src', media.sizes[params.size].url);
							
							removeElement	=	$('<span class="qm-answer-remove-image"><i class="material-icons">cancel</i></span>');
							removeElement.click(function(e){
								e.preventDefault();
								
								_self.answer_media_image({ is_remove: true, container: params.container });
								
								$(this).remove();
							});
							
							params.container.append(image);
							params.container.before(removeElement);
						}
					
						$("input[name='" + params.name + "']").val(media.id);
						
					}
				});
			}else{
				
				params.container.find('img').remove();
				
				$("input[name='" + params.name + "']").val('');
			}
		},
		single_template: function(index){
			
			if(typeof index == 'undefined') { index = 0; }
			
			var _self		=	this,
				template	=	$('<tr>' +
								'<td class="qm-td-c-a"><input type="radio" value="' + index + '" class="ir-is-correct" name="answers_single_is-correct"/></td>' +
								'<td>'+
									'<div class="qm-answer-info">'+
									'<span class="qm-answer-image" data-name="answers_single[' + index + '][image]">'+
									'<input type="hidden" name="answers_single[' + index + '][image]"/>'+
									'</span>'+
									'</div>'+
									'<div class="qm-answer-desc"><textarea name="answers_single[' + index + '][content]" class="qm-s-wide qm-answer-desc__editor"></textarea></div>'+
								'</td>'+
								'<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>'+
								'</tr>');
			
			template.find('.qm-remove').click(function(e){
				
				template.remove();
				
				e.preventDefault();
			});
			
			template.find('.qm-answer-image').click(function(e){
				
				_self.answer_media_image({name: $(this).data('name'), container: $(this)});
				
				e.preventDefault();
			});
			
			return template;
		},
		multiple_template: function(index){
			
			if(typeof index == 'undefined') { index = 0; }
			
			var _self		=	this,
				template	=	$('<tr>' +
								'<td class="qm-td-c-a"><input type="checkbox" value="' + index + '" class="ir-is-correct" name="answers_multiple_is-correct[]"/></td>' +
								'<td>' +
									'<div class="qm-answer-info">'+
									'<span class="qm-answer-image" data-name="answers_multiple[' + index + '][image]">'+
									'<input type="hidden" name="answers_multiple[' + index + '][image]"/>'+
									'</span>'+
									'</div>'+
									'<div class="qm-answer-desc"><textarea name="answers_multiple[' + index + '][content]" class="qm-s-wide qm-answer-desc__editor"></textarea></div>'+
								'</td>'+
								'<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>'+
								'</tr>');
			
			template.find('.qm-remove').click(function(e){

				template.remove();

				e.preventDefault();
			});
			
			template.find('.qm-answer-image').click(function(e){
				
				_self.answer_media_image({name: $(this).data('name'), container: $(this)});
				
				e.preventDefault();
			});
			
			return template;
		},
		drag_match_template: function(index){
			
			if(typeof index == 'undefined') { index = 0; }
			
			var template	=	$('<tr>' +
								'<td><textarea name="answers_drag_match[' + index + '][content]" class="qm-s-wide"></textarea>' +
									'<div class="qm-group-input-label"><label class="center">Answer</label><div class="g-input"><input type="text" name="answers_drag_match[' + index + '][answer]"/></div></div>' +
								'</td>'+
								'<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>'+
								'</tr>');
			
			template.find('.qm-remove').click(function(e){
				
				template.remove();
				
				e.preventDefault();
			});
			
			return template;
		},
		group_match_template: function(index){
			
			if(typeof index == 'undefined') { index = 0; }
			
			var template	=	$('<tr>' +
								'<td>' +
									'<div class="qm-group-input-label"><label class="center">Group</label><div class="g-input"><input type="text" name="answers_group_match[' + index + '][group]"/></div></div>' +
									'<div class="qm-group-input-label"><label class="center">Answer</label><div class="g-input"><input type="text" name="answers_group_match[' + index + '][item]"/></div></div>' +
								'</td>'+
								'<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>'+
								'</tr>');
			
			template.find('.qm-remove').click(function(e){
				
				template.remove();
				
				e.preventDefault();
			});
			
			return template;
		},
		order_template: function(index){
			
			if(typeof index == 'undefined') { index = 0; }
			
			var _self		=	this,
				template	=	$('<tr>' +
								'<td class="qm-td-c-a"><span class="answers_order-drag">Drag</span></td>' +
								'<td>'+
									'<div class="qm-answer-info">'+
									'<span class="qm-answer-image" data-name="answers_order[' + index + '][image]">'+
									'<input type="hidden" name="answers_order[' + index + '][image]"/>'+
									'</span>'+
									'</div>'+
									'<div class="qm-answer-desc"><textarea name="answers_order[' + index + '][content]" class="qm-s-wide"></textarea></div>'+
								'</td>'+
								'<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>'+
								'</tr>');

			template.find('.qm-remove').click(function(e){
	
				template.remove();
	
				e.preventDefault();
			});
			
			template.find('.qm-answer-image').click(function(e){
	
				_self.answer_media_image({name: $(this).data('name'), container: $(this)});
	
				e.preventDefault();
			});

			return template;
		},
		guess_word_template: function(index){
			
			if(typeof index == 'undefined') { index = 0; }
			
			var _self		=	this,
				template	=	$('<tr>' +
								'<td class="qm-td-c-a"><input type="checkbox" value="' + index + '" class="ir-is-correct" name="answers_guess_word_is-correct[]"/></td>' +
								'<td>' +
									'<div class="qm-answer-desc no-pad"><input name="answers_guess_word[' + index + '][content]" class="qm-s-wide"/></div>'+
								'</td>'+
								'<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>'+
								'</tr>');
			
			template.find('.qm-remove').click(function(e){

				template.remove();

				e.preventDefault();
			});
			
			return template;
		},
		keywords_template: function(index){
			
			if(typeof index == 'undefined') { index = 0; }
			
			var _self		=	this,
				template	=	$('<tr>' +
								'<td>' +
									'<div class="qm-answer-desc no-pad"><input name="answers_keywords[' + index + '][content]" class="qm-s-wide"/></div>'+
								'</td>'+
								'<td class="qm-td-c-a"><button class="qm-remove"><i class="material-icons">cancel</i></button></td>'+
								'</tr>');
			
			template.find('.qm-remove').click(function(e){

				template.remove();

				e.preventDefault();
			});
			
			return template;
		}
	};
	
	$('#add-new-single-answer').click(function(e){
		
		var tbody		=	$('#answer-type-single .answers-box tbody'),
			new_answer	=	Answer.single_template(tbody.children('tr').length);
				
		new_answer.appendTo(tbody);

		tinymce.init({
		  selector: 'textarea.qm-answer-desc__editor',
		  theme: 'modern',
		  height: 200,
		  menubar: false,
		  toolbar: 'bold italic underline strikethrough forecolor backcolor superscript subscript | alignleft aligncenter alignright | image media',
		  insert_toolbar: 'quickimage',
		  plugins: "tabfocus,paste,media,wordpress,wpeditimage,wpgallery,wpdialogs,textcolor"
		});
		
		e.preventDefault();
	});
	
	$('#add-new-multiple-answer').click(function(e){
		
		var tbody		=	$('#answer-type-multiple .answers-box tbody'),
			new_answer	=	Answer.multiple_template(tbody.children('tr').length);
			
		new_answer.appendTo(tbody);

		tinymce.init({
		  selector: 'textarea.qm-answer-desc__editor',
		  theme: 'modern',
		  height: 200,
		  menubar: false,
		  toolbar: 'bold italic underline strikethrough forecolor backcolor superscript subscript | alignleft aligncenter alignright | image media',
		  insert_toolbar: 'quickimage',
		  plugins: "tabfocus,paste,media,wordpress,wpeditimage,wpgallery,wpdialogs,textcolor"
		});
	
		e.preventDefault();
	});
	
	$('#add-new-drag-match').click(function(e){
		
		var tbody		=	$('#answer-type-drag_match .answers-box tbody'),
			new_answer	=	Answer.drag_match_template(tbody.children('tr').length);
			
		new_answer.appendTo(tbody);
		
		e.preventDefault();
	});

	$('#add-new-group-match').click(function(e){
		
		var tbody		=	$('#answer-type-group_match .answers-box tbody'),
			new_answer	=	Answer.group_match_template(tbody.children('tr').length);
			
		new_answer.appendTo(tbody);
		
		e.preventDefault();
	});
	
	$('#add-new-order').click(function(e){
		
		var tbody		=	$('#answer-type-order .answers-box tbody'),
			new_answer	=	Answer.order_template(tbody.children('tr').length);
				
		new_answer.appendTo(tbody);
		
		e.preventDefault();
	});

	$('#add-new-guess-word-answer').click(function(e){
		
		var tbody		=	$('#answer-type-guess_word .answers-box tbody'),
			new_answer	=	Answer.guess_word_template(tbody.children('tr').length);
			
		new_answer.appendTo(tbody);
	
		e.preventDefault();
	});

	$('#add-new-keywords-answer').click(function(e){
		
		var tbody		=	$('#answer-type-keywords .answers-box tbody'),
			new_answer	=	Answer.keywords_template(tbody.children('tr').length);
			
		new_answer.appendTo(tbody);
	
		e.preventDefault();
	});
	
	$('.answer-type-panel .qm-remove').click(function(e){
		
		$(this).parents('tr').remove();
		
		e.preventDefault();
	});
	
	$('.answer-type-panel .qm-answer-image').click(function(e){

		Answer.answer_media_image({name: $(this).data('name'), container: $(this)});

		e.preventDefault();
	});

	$('.answer-type-panel .qm-answer-remove-image').click(function(e){

		var container	=	$(this).next('.qm-answer-image'),
			name		=	container.data('name');

		Answer.answer_media_image({is_remove: true, container: container, name: name});
		$(this).remove();

		e.preventDefault();
	});

	tinymce.init({
	  selector: 'textarea.qm-answer-desc__editor',
	  theme: 'modern',
	  height: 200,
	  menubar: false,
	  toolbar: 'bold italic underline strikethrough forecolor backcolor superscript subscript | alignleft aligncenter alignright | image media',
	  insert_toolbar: 'quickimage',
	  plugins: "tabfocus,paste,media,wordpress,wpeditimage,wpgallery,wpdialogs,textcolor"
	});
	
	$('.answer-type-panel .answers-box.order tbody').sortable({
		items: '> tr'
	});
});