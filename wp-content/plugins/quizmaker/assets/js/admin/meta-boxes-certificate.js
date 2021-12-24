jQuery( function( $ ) {
	
	var QM_Certificate = function() {
		
		return {
			init: function() {
				
				this.setupEvironment();
				this.setupData( meta_boxes_certificate.data );
				this.events();
			},
			events: function(){

				this.subPanelEvent();
				this.previewEvent();
				this.addBackgroundEvent();
				this.addNameEvent();
				this.addDateEvent();
				this.addRankEvent();
				this.addScoreEvent();
				this.addCustomTextEvent();
				this.addTestNameEvent();
				this.addCertIDEvent();
				this.addRandomCertIDEvent();

				this.addUserMetaEvent();
				
			},
			setupEvironment: function() {

				this.data  		=	[],
				this.metaBox	=	$('#quizmaker-certificate-data');
				this.container	=	$('#qm_certificate_compose_scrollpanel');
				this.subPanel	=	this.metaBox.find('.qm_certificate_compose_action_sub-panel');
				this.panel		=	this.container.find('.jspPane');

				this.container.jScrollPane();
			},
			setupData: function( data ){
				if( data.length > 0 ){
					
					var _self	=	this;
										
					$.each(data, function( index, value ){
						
						switch(value.type) {
							case 'name':
								_self.addName( value );
							break;
							case 'date':
								_self.addDate( value );
							break;
							case 'rank':
								_self.addRank( value );
							break;
							case 'text':
								_self.addCustomText( value );
							break;
							case 'test_name':
								_self.addTestName( value );
							break;
							case 'usermeta':
								_self.addUserMeta( value );
							break;
							case 'id':
								_self.addID( value );
							break;
							case 'random_cert_id':
								_self.addRandomCertID( value );
							break;
						}
						
					});
				}
			},
			reset: function(){
				this.metaBox.find('.element_certificate').remove();
			},
			refresh: function(){
				var data = this.container.data('jsp');
				
				if(typeof data != 'undefined') {
					data.reinitialise();
				}
			},
			saveData: function(){
				
				QM_Model().save({ action: 'quizmaker_save_certificate', id: meta_boxes_certificate.post_id, data: this.data }).then(function( response ){
					
					QM().Message().inform('Save Success');
					
				});
			},
			addData: function( data ){
				
				this.data.push( data );
				
				return this.data.length;
			},
			removeData: function( index ) {
				
				this.data.splice( index, 1 );
				
				var updatedData	=	this.data;
				
				this.data = [];
				
				this.reset();
				
				this.setupData( updatedData );

			},
			removeAll: function(){
				this.data = [];
				
				this.reset();
			},
			updateData: function( index, data ){
				
				data = $.extend(this.data[index], data);
				
				this.data[index]	=	data;
			},
			getElement: function( index ) {
				
				if( typeof index == 'undefined' ){
					index = this.selectedIndex;
				}
				
				var element = $('#qm_certificate_compose_scrollpanel .jspPane').children('.element_certificate').filter(function(){
					
					return $(this).data('index') == index;
				});
				
				if(element.length > 0) { 
					
					return element;
					
				}else{
					
					return false;
				}
			},
			subPanelEvent: function(){
				var _self	=	this;
				
				this.subPanel.find('input, select').change(function(){
					
					_self.data[_self.selectedIndex][$(this).data('name')]	=	$(this).val();
					
					var element	=	_self.getElement();
					
					if( element ) {
						_self.updateElement(element, _self.data[_self.selectedIndex]);
						
						$('.element_certificate').draggable( "option", "refreshPositions", true );
					}
					
				});
				
				this.subPanel.find('#qm_certificate_color').wpColorPicker({
					defaultColor: '#000000',
					change: function(event, ui, data){
						
						_self.data[_self.selectedIndex][$(this).data('name')]	=	ui.color.toString();
						
						var element	=	_self.getElement();
							
							if( element ) {
								
								_self.updateElement(element, _self.data[_self.selectedIndex]);
							}
					}
				});
				
				this.metaBox.find('.qm_certificate_save').click(function(e){
					e.preventDefault();
					
					_self.saveData();
				});
				
				this.metaBox.find('.qm_certificate_remove').click(function(e){
					e.preventDefault();
					
					_self.removeData( _self.selectedIndex );
				});
			},
			previewEvent: function() {

				var _self = this;

				$('.post-type-certificate #post-preview').unbind('click').click(function(e){
					e.preventDefault();

					_self.preview();

					return false;
				});
			},
			preview: function() {

				var loading = QM().Loading();

				loading.show();

				QM_Model().get({ 
						action: 'quizmaker_preview_certificate',
						id: meta_boxes_certificate.post_id
				}).then(function( response ){

					$('<img src="' + response.url + '" alt="Preview Certficate"/>').imagesLoaded().done( function( instance ) {
  						
  						var width 	= instance.images[0].img.width,
  							height 	= instance.images[0].img.height;
  							
  						loading.hide();

  						QM_Dialog(
  							$('<div style="width:100%; height:100%; overflow:scroll;"><img src="' + response.url + '" alt="Preview Certficate" style="width:100%; height:auto"/></div>'), 
  							{
  								title: 'Certificate Preview',
  								width: width,
  								height: height
  							}
  						).show();

					});
					
				});
			},
			updateTextPanel: function( index ) {
				
				this.subPanel.css('display', 'none');
				
				var subpanel 		=	this.subPanel.filter('.text'),
					fontSizeInput	=	subpanel.find('#qm_certificate_font_size'),
					fontFamilyInput	=	subpanel.find('#qm_certificate_font_family'),
					textInput		=	subpanel.find('#qm_certificate_text'),
					textAlignInput	=	subpanel.find('#qm_certificate_text_align'),
					colorInput		=	subpanel.find('#qm_certificate_color');
				
					subpanel.css('display', 'block');
					
					fontSizeInput.val(this.data[index].font_size);
					fontFamilyInput.val(this.data[index].font_name);
					textAlignInput.val(this.data[index].text_align);
					colorInput.val(this.data[index].color);
										
					if(typeof this.data[index].content != 'undefined'){
						textInput.val(this.data[index].content);
					}else{
						textInput.val('');
					}
			},
			addElement: function( element, params ) {
				
				var _self	=	this;
				
				params = $.extend({ x: 10, y: 10 }, params);
				
				QM_Model().post({ 
						action: 'quizmaker_get_text_boudingbox_certificate', 
						data: params
				}).then(function( response ){
					
					if(response){
						params.width  		=	response.width;
						params.height 		=	response.height;
						params.image_base64	=	response.image_base64;

						_self._addElement( element, params );	
					}

				});
								
				return element;
			},
			_addElement: function( element, params) {

				var _self	=	this;

				element	=	this.applyStyle( element, params);
				element	=	this.attachDragEvent( element );
				
				var index	=	parseInt(this.addData( params )) - 1;
				
				element.data('index', index);
				element.data('type', params.type);
								
				element.click(function(e){
					e.preventDefault();
					
					_self.selectedIndex	=	$(this).data('index');
					_self.updateTextPanel( _self.selectedIndex );
					
					$('.element_certificate').removeClass('active');
					$(this).addClass('active');
				});
				
				this.selectedIndex	=	index;
				this.updateTextPanel( this.selectedIndex );
				
				this.container.find('.jspPane').append(element);
								
				return element;
			},
			updateElement: function( element, params ) {

				var _self	=	this;

				QM_Model().post({ 
						action: 'quizmaker_get_text_boudingbox_certificate', 
						data: params
				}).then(function( response ){
					
					if(response){
						params.width  		=	response.width;
						params.height 		=	response.height;
						params.image_base64	=	response.image_base64;

						_self.applyStyle( element, params);	
					}

				});

			},
			applyStyle: function( element, params ) {
				
				params	=	$.extend({x: 10, y: 10, color: '#000', font_name: 'Arial', font_size: 30 + 'px'}, params);
				
				element.css({
					position: 	'absolute',
					top: 		parseInt(params.y),
					left: 		parseInt(params.x),
					height: 	parseInt(params.height),
					'font-size':  params.font_size + 'px',
					color: 		params.color
				});

				element.html('<img src="data:image/png;base64,' + params.image_base64 + '"/>');

				var content = element.find('img');
				
				switch( params.text_align ){
					case 'center':
						
						content.css({ left: -(parseInt(params.width)/2) + 'px' });

					break;
					case 'right':
						
						content.css({ left: -(parseInt(params.width)) + 'px' });

					break;
					default:
						
						content.css({ left: 0 });

					break;
				}

				return element;
			},
			attachDragEvent: function( element ) {
				
				var _self	=	this;
				
				element.draggable({
					containment: this.container.find('.jspContainer'),
					cursor: "move",
					stop: function( event, ui ) {
						
						var index	=	element.data('index'),
							data	=	{ x: ui.position.left, y: ui.position.top };
							
						_self.updateData(index, data);
						
					}
				});
				
				return element;
			},
			addBackgroundEvent: function(){
				var _self				=	this, frame,
					metaBox				=	$('#quizmaker-certificate-data'), // Your meta box id here
					addImgLink			=	metaBox.find( '.qm_media_upload' ),
					delImgLink			=	metaBox.find( '.delete-custom-img' ),
					composeContainer	=	metaBox.find( '.qm_certificate_compose_container' ),
					imgIdInput			=	metaBox.find( '#qm_certificate_data_background' );
				
					addImgLink.click(function(e){
						e.preventDefault();
		
						if ( frame ) {
							frame.open();
							return;
						}
		
						frame = wp.media({
							title: 'Select or Upload Media Of Your Chosen Persuasion',
							button: {
								text: 'Use this media'
							},
							multiple: false
						});
		
						frame.on( 'select', function() {
			
							var attachment = frame.state().get('selection').first().toJSON();
							
							_self.removeAll();
							_self.addBackground(attachment);
							
							setTimeout(function(){
								_self.refresh();
							}, 500);
							
						});
		
						frame.open();
					});
			},
			addBackground: function( params ) {
				
				var metaBox				=	$('#quizmaker-certificate-data'),
					composeContainer	=	metaBox.find( '.qm_certificate_compose_container .background' ),
					imgIdInput			=	metaBox.find( '#qm_certificate_background' );
				
				var backgroundImage		=	$('<img/>');

				backgroundImage.attr('src', params.url);
				
				composeContainer.html(backgroundImage);
				imgIdInput.val( params.id );
				
			},
			addCustomTextEvent: function() {
				var _self	=	this;
				
				this.metaBox.find('.qm_add_text-custom').click(function(e){
					e.preventDefault();
					_self.addCustomText();
				});
			},
			addCustomText: function( params ) {
				
				params = $.extend({ type: 'text', font_size: 30, font_name: 'opensans_regular', content: 'Custom Text', color: '#000000', text_align: 'left' }, params);
								
				this.addElement( 
					$('<div class="element_certificate element_certificate-text"></div>'), params );
			},
			addNameEvent: function(){
				var _self	=	this;
				
				this.metaBox.find('.qm_add_text-name').click(function(e){
					e.preventDefault();
					_self.addName();
				});
			},
			addName: function( params ) {

				params = $.extend({ 
					type: 'name', 
					content: '[name]',
					font_size: 30, 
					font_name: 'opensans_regular', 
					color: '#000000', 
					text_align: 'left'}, params);
				
				this.addElement( 
					$('<div class="element_certificate element_certificate-name align-' + params.text_align + '"/>'), params );
			},
			addDateEvent: function() {
				var _self	=	this;
				
				this.metaBox.find('.qm_add_text-date').click(function(e){
					e.preventDefault();
					_self.addDate();
				});
			},
			addDate: function( params ) {

				params = $.extend({ 
					type: 'date', 
					content: '[date]',
					font_size: 30, 
					font_name: 'opensans_regular', 
					color: '#000000', 
					text_align: 'left'}, params);
				
				var element	=	$('<div class="element_certificate element_certificate-date align-' + params.text_align + '"><span class="el center"></span></div>');
				
				element = this.addElement( element, params );
			},
			addRankEvent: function(){
				var _self	=	this;
				
				this.metaBox.find('.qm_add_text-rank').click(function(e){
					e.preventDefault();
					_self.addRank();
				});
			},
			addRank: function( params ) {
				
				params = $.extend({ 
					type: 'rank', 
					content: '[rank]',
					font_size: 30, 
					font_name: 'opensans_regular', 
					color: '#000000', 
					text_align: 'left'}, params);
				
				this.addElement( 
					$('<div class="element_certificate element_certificate-rank align-' + params.text_align + '">[rank]</div>'), params );
			},
			addTestName: function( params ) {
				
				params = $.extend({ 
					type: 'test_name', 
					content: '[Test Name]',
					font_size: 30, 
					font_name: 'opensans_regular', 
					color: '#000000', 
					text_align: 'left'}, params);
								
				this.addElement( 
					$('<div class="element_certificate element_certificate-test-name">[Test Name]</div>'), params );
			},
			addScore: function( params ) {
				
				params = $.extend({ 
					type: 'score', 
					content: '[score]',
					font_size: 30, 
					font_name: 'opensans_regular', 
					color: '#000000', 
					text_align: 'left'}, params);
				
				this.addElement( 
					$('<div class="element_certificate element_certificate-score align-' + params.text_align + '">[score]</div>'), params );
			},
			addUserMeta: function( params ) {

				params = $.extend({ 
					type: 'usermeta', 
					content: '',
					name: '',
					font_size: 30, 
					font_name: 'opensans_regular', 
					color: '#000000', 
					text_align: 'left'}, params);

				this.addElement( $('<div class="element_certificate element_certificate-usermeta">[' + params.content + ']</div>'), params );
			},
			addID: function( params ) {

				params = $.extend({ 
					type: 'id', 
					content: '[Certificate ID]',
					font_size: 30, 
					font_name: 'opensans_regular', 
					color: '#000000', 
					text_align: 'left'}, params);

				this.addElement( $('<div class="element_certificate element_certificate-id">[ID]</div>'), params );
			},
			addRandomCertID: function( params ) {

				params = $.extend({ 
					type: 'random_cert_id', 
					content: '[User Cert ID]',
					font_size: 30, 
					font_name: 'opensans_regular', 
					color: '#000000', 
					text_align: 'left'}, params);

				this.addElement( $('<div class="element_certificate element_certificate-random-cert-id">[User Cert ID]</div>'), params );
			},
			addScoreEvent: function(){
				var _self	=	this;
				
				this.metaBox.find('.qm_add_text-score').click(function(e){
					e.preventDefault();
					_self.addScore();
				});
			},
			addTestNameEvent: function(){

				var _self	=	this;
				
				this.metaBox.find('.qm_add_text-test-name').click(function(e){

					e.preventDefault();

					_self.addTestName();
				});
			},
			addCertIDEvent: function(){

				var _self	=	this;
				
				this.metaBox.find('.qm_add_text-id').click(function(e){

					e.preventDefault();

					_self.addID();
				});
			},
			addRandomCertIDEvent: function(){

				var _self	=	this;
				
				this.metaBox.find('.qm_add_text-random-cert-id').click(function(e){

					e.preventDefault();

					_self.addRandomCertID();
				});
			},
			addUserMetaEvent: function(){

				var _self	=	this;
				
				this.metaBox.find('.qm_add_text-usermeta').click(function(e){
					e.preventDefault();

					_self.addUserMeta({
						content: '[' + $(this).data('label') + ']',
						name: $(this).data('name')
					});
					
				});
			}
		}
	}

	var QM_Certificate_Tests = function() {

		return {
			init: function(){

				this.table_data = new AWS_Table_Data($('.qm-list-certificate-tests'), { delay: 1 });
				
				this.events();

				this.table_data.init();
			},
			events: function(){

				var _self	=	this;
				
				$('#add-test-to-certificate').click(function(e){
					
					var ids = $('#input-search-tests').val();
					
					if( ids ){

						$.when(_self.table_data.action( 'add', { data: { ids: ids } } )).then(function(){
							
							_self.table_data.refresh();
							$('#input-search-tests').val('').trigger('change');
						});
						
					}
					
					e.preventDefault();
				});

				$('#remove-test-from-certificate').click(function(e){
					
					var user_ids	=	_self.table_data.get_selected(),
						btn			=	$(this);
					
						btn.addClass('disabled');
					
					if(user_ids.length > 0){

						$.when(_self.table_data.action( 'remove', { data: { ids: user_ids } } )).then(function(){

							_self.table_data.refresh();
							btn.removeClass('disabled');
						});
					}
					
					e.preventDefault();
				});

				$('.qm-list-certificate-tests').on('aws-table-data-after-loading-data', function(){

					$(this).find('.pass').change(function(){

						var pass = parseInt($(this).val()),
							id   = parseInt($(this).data('id'));

						$.when(_self.table_data.action( 'add_pass', { data: { id: id, pass: pass } } )).then(function(){
							
						});
						
					});

				});
			}
		};
	}

	$(document).ready(function() {

		QM_Certificate().init();
		QM_Certificate_Tests().init();
	});
});