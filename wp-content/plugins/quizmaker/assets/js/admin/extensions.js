jQuery( function( $ ) {

	var is_installing = false;
		
	$('.install-now').click(function(e){
		e.preventDefault();
		
		if( !is_installing ){

			is_installing = true;

			var _self 		=	$(this);

			var model		=	new QM_Model(),
				id			=	parseInt($(this).data('id')),
				security	= 	$(this).data('security'),
				slug 		=	$(this).data('slug');

			_self.addClass('disabled');

			model.post({ action: 'install_extension', id: id, slug: slug, security: security }).then(function(response){
				
				_self.removeClass('disabled').addClass('button-primary');
				
				is_installing = false;
			});

		}

	});
	
});