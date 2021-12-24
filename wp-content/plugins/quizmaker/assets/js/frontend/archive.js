jQuery( function( $ ) {
	
	function qm_archive_order(){
		
		var order_form	=	$('.quizmaker-ordering'),
			orderby		=	order_form.find('.orderby'),
			memberships	=	order_form.find('.memberships');
			
			orderby.change(function(){
				
				order_form.submit();
			});
			
			memberships.change(function(){
				
				order_form.submit();
			});
	}
	
	$(document).ready(function() {
		
		qm_archive_order();
		
	});
});