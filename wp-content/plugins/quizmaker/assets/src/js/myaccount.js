"use strict";
import Vue from 'vue';


import helper from './helper';
global.helper = helper;
import config from './config';
global.config = config;

import router from './router';
import i18n from './i18n/';

import Vuetify from 'vuetify';
import VeeValidate from 'vee-validate';

import VueResource from 'vue-resource';

import store from './store/';
global.store = store;

// import i18n from './i18n/';

// var VueResource = require('vue-resource');

import FrontAppMyaccount from './FrontAppMyaccount.vue';

Window.Vue = Vue;

Vue.config.debug = false;
Vue.config.silent = true;
Vue.config.devtools = false;


Vue.use(Vuetify);
Vue.use(VeeValidate);
Vue.use(VueResource);

Vue.component('FrontAppMyaccount', FrontAppMyaccount);

jQuery(document).ready(function() {

	if(jQuery('#qm-page-myaccount').length > 0){

		new Vue({
			i18n,
			router,
			store,
			render: h => h(FrontAppMyaccount),
			http: {
			    root: quizmaker.site_url
			},
			data: {
				home_url: '',
				is_show_sidebar: false,
				pages: 1,
				page: 1,
				is_last_page: false,
				confirmSubmitAction: false
			},
			created: function(){

				jQuery('html').addClass('hide-scroll');

				// global.$t = this.$t;

				// fetch menu from server
			    // this.$http.get('/menu').then(({data}) => {
			    //   this.$store.commit('setMenu', data)
			    // })
			    this.$store.dispatch('checkPageTitle', this.$route.path)
			    // this.$store.dispatch('checkAuth')

				
			}
		}).$mount('#qm-page-myaccount-view');
	}
	
});