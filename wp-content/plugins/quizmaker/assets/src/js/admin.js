
import Vue from 'vue';
import Vuetify from 'vuetify';
import VeeValidate from 'vee-validate';
import LibFormBuilder from 'formBuilder';
import Select2 from './components/select2.vue';
import WPMedia from "./components/wpmedia.vue";
import FormBuilder from './components/formbuilder.vue';
import FormField from './components/form-field.vue';

Vue.config.debug = true;
Vue.config.silent = false;
Vue.config.devtools = true;

Vue.use(Vuetify);
Vue.use(VeeValidate);
Vue.use(Select2);
Vue.use(FormField);

window.md5 = require('js-md5');

Vue.component('star-rating', {
	  props: {
	    'name': String,
	    'value': null,
	    'total': {
	    	type: [Number],
	    	default: function(){

	    		return 0;
	    	}
	    },
	    'value_t': null,
	    'id': String,
	    'disabled': Boolean,
	    'required': Boolean
	  },
	  template: '<div class="qm-star-rating">\
	        <label class="star-rating__star" v-for="rating in ratings" \
	        :class="{\'is-selected\': ((ve >= rating) && ve != null), \'is-hover\': ((vt >= rating) && vt != null), \'is-disabled\': disabled}" \
	        v-on:click="set(rating)" v-on:mouseover="star_over(rating)" v-on:mouseout="star_out"><i class="material-icons">grade</i></label><span v-if="total > 0" class="count">({{total}})</span></div>',

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

jQuery(document).ready(function() {
	
	if(jQuery('.quizmaker_page_qm-settings').length > 0) {

		new Vue({
			el: '#wpbody',
			components: {
				'formbuilder': FormBuilder
			}
		});
	}
	

});