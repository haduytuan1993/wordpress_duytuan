<template>

	<div></div>

</template>
<script>

import Vue from 'vue';

export default {
	created () {

		Vue.http.get('?qm-ajax=account_resource&method=logout').then(response => {
			
			
			response.json().then(function( result ){

				window.location = result.redirect;
			});

		}, response => {
			

		});	
	}
}
</script>