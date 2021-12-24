<template>
  <div class="quiz-composer quiz-scale-question">
    <v-container fluid grid-list-lg>
      <v-layout row wrap>
        <v-flex sm12>
          <div class="question-content">
            <textarea v-model="question.content"></textarea>
          </div>
        </v-flex>
        
        <v-flex sm8>
        	<div class="slider-options">
        		
        		<div class="slider-range">
        			<div class="custom-handle ui-slider-handle">
        				<span class="value"></span>
        			</div>
        		</div>
				
				<div class="mt-3">
        			<v-text-field v-model="question.options.min" label="Min" dark></v-text-field>
        			<v-text-field v-model="question.options.max" label="Max" dark></v-text-field>
        			<v-text-field v-model="question.options.steps" label="Steps" dark></v-text-field>
        		</div>
        	</div>
        </v-flex>

        <v-flex sm4>
					
    			<wpmedia :value.sync="question.image" size="medium"></wpmedia>

    		</v-flex>
        
      </v-layout>

    </v-container>

    <div class="bottom-toolbar">
      <v-container fluid grid-list-lg>
      <v-layout row wrap>
        <v-flex sm10>
          <v-slider hide-details prepend-icon="alarm" v-model="settings.duration" thumb-label step="10" min="0" max="60" snap></v-slider>
        </v-flex>
        <v-flex sm2>
          <div class="question-points">
            <input type="text" value="1" v-model="settings.points"/>
            <span>Points:</span>
          </div>
        </v-flex>
      </v-layout>
      </v-container>
    </div>
    
    <v-expansion-panel expand>
        <v-expansion-panel-content>
          <div slot="header"><strong>Music Manager</strong></div>
          <v-card>
              <v-card-text class="grey lighten-3">

                <manager-player :value.sync="question.musics"></manager-player>

            </v-card-text>
          </v-card>
        </v-expansion-panel-content>
        <v-expansion-panel-content>
        <div slot="header"><strong>Video Manager</strong></div>
        <v-card>
          <v-card-text class="grey lighten-3">
            <v-data-table
                  v-bind:headers="headersMediaManager"
                  :items="itemsMediaManager"
                  hide-actions
                  class="elevation-1 mb-2" 
                  item-key="id"
                >
                <template slot="items" scope="props">
                  <tr @click="props.expanded = !props.expanded">
                  <td class="text-xs-left">{{ props.item.title }}</td>
                  <td class="text-xs-center">{{ props.item.status }}</td>
                  <td class="text-xs-left"><v-btn fab small icon @click="removeVideo(props.item.id)"><v-icon>delete</v-icon></v-btn></td>
                    </tr>
                </template>
                <template slot="expand" scope="props">
                  <v-card flat>
                    <v-card-text>
                      
                      <v-text-field label="Title" hide-details v-model="props.item.title"></v-text-field>
                  <v-text-field v-model="props.item.code" label="Embed Code" textarea></v-text-field>
                  
                  <v-switch label="Status" false-value="0" true-value="1" v-model="props.item.status"></v-switch>

                    </v-card-text>
                  </v-card>
                </template>
            </v-data-table>
            
            <v-text-field label="Title" hide-details v-model="embedVideo.title"></v-text-field>
            <v-text-field v-model="embedVideo.code" label="Embed Code" textarea></v-text-field>
                  
            <v-btn @click="addVideo" dark small class="primary white--text ml-0">
                  <v-icon left class="white--text">add</v-icon>Add Video 
                </v-btn>
          </v-card-text>
        </v-card>
      </v-expansion-panel-content>

      <v-expansion-panel-content>
        <div slot="header"><strong>Advanced Settings</strong></div>
        <v-list two-line subheader>
              <v-list-tile href="javascript:;">
                <v-list-tile-action>
                  <v-switch v-model="settings.enable"></v-switch>
                </v-list-tile-action>
                <v-list-tile-content @click="settingEnable = !settingEnable">
                  <v-list-tile-title>Enable</v-list-tile-title>
                  <v-list-tile-sub-title>Enable or Disable this question</v-list-tile-sub-title>
                </v-list-tile-content>
              </v-list-tile>
            </v-list>
      </v-expansion-panel-content>
    </v-expansion-panel>

  </div>
</template>
<script>

import WPMedia from "./wpmedia.vue";
import ManagerPlayer from "./manager-player.vue";

export default {
  components: {
    'wpmedia': WPMedia,
    'manager-player': ManagerPlayer
  },
  props: {
    order: {
      type: Number, 
      default: function(){ return 0; }
    },
    question: {
      type: Object
    }
  },
  data: function(){

    this.question.type = 3;
    
    if( typeof this.question.order == 'undefined' ) {

      this.question.order = this.order;
    }

    if( typeof this.question.id == 'undefined' ) {

      this.question.id = _.now();
    }

    if( typeof this.question.content == 'undefined' ) {

      this.question.content = '';
    }

    if( typeof this.question.options == 'undefined' ) {

      this.question.options = {
      	min: 1,
      	max: 10,
      	steps: 1,
      	value: 5
      };
    }

    if( typeof this.question.settings == 'undefined' ) {

      this.question.settings = {
        enable: true,
        points: 1,
        duration: 5,
        media: []
      };
    }

    if( typeof this.question.musics == 'undefined' ) {

      this.question.musics = undefined;
    }

    if( typeof this.question.videos == 'undefined' ) {

      this.question.videos = [];
    }
        
    return {
      slider: null,
      questions: this.question.options,
      settings: this.question.settings,
      videos: this.question.videos,
      embedVideo: { title:'', code:'' },
      rightQuestion: 1,
      trackColor: 'white',
      settingEnable: null,
      headersMediaManager: [
        { 
          text: 'Title', 
          value: 'title',
          align: 'left',
          sortable: false
        },
        { 
          text: 'Status', 
          value: 'id',
          align: 'center',
          sortable: false
        },
        { 
          text: 'Actions', 
          value: 'action',
          align: 'left',
          sortable: false
        }
      ]
    };
  },
  mounted: function(){

  	var _self = this, handle = jQuery(this.$el).find( ".custom-handle" );

  	this.slider = jQuery(this.$el).find('.slider-range').slider({
		min: parseInt(_self.questions.min),
		max: parseInt(_self.questions.max),
		step: parseInt(_self.questions.steps),
		value: parseInt(_self.questions.value),
		create: function() {
        	handle.find('.value').text( jQuery( this ).slider( "value" ) );
      	},
		slide: function( event, ui ) {

			handle.find('.value').text( ui.value );

			_self.questions.value = ui.value;

		}
  	});

  },
  computed: {
    itemsMediaManager: function(){

      var videos = this.videos;

      return videos;
    }
  },
  watch: {
  	'questions.min': function( min ){

  		if( parseInt(min) && parseInt(min) >= 0 ) {

  			this.slider.slider('option', 'min', parseInt(min));
  		}
  	},
  	'questions.max': function( max ){

  		if( parseInt(max) && parseInt(max) >= 0 ) {

  			this.slider.slider('option', 'max', parseInt(max));
  		}
  	},
  	'questions.steps': function( steps ){

  		if( parseInt(steps) && parseInt(steps) >= 1 ) {

  			this.slider.slider('option', 'step', parseInt(steps));
  		}
  	}
  },
  methods: {
    save: function(){

      this.question.settings.duration = this.duration;
    },
    setRightQuestion: function(){

      var _self = this;

      _.each( this.questions, function( question, index ){

        if( _self.rightQuestion == question.id ){

          question.is_correct = 1;
        }else{

          question.is_correct = 0;
        }
      });
    },
    removeQuestion: function( index ){

      if( this.questions.length > 1 ){

        this.questions.splice( index, 1 );
      }
    },
    addQuestion: function( index ){

      this.questions.push({
        id: _.now(),
        content: 'New Question',
        is_correct: 0
      });
    },
    addVideo: function(){

      this.videos.push({
        id:   _.now(),
        title:  this.embedVideo.title,
        code:   this.embedVideo.code,
        status: 1
      });

      this.embedVideo = {
        title: '',
        code: ''
      };
    },
    removeVideo: function( id ){

      var index = _.findIndex( this.videos, {id: id});

      this.videos.splice(index, 1);
    },
    isCorrected: function( index ){

      if( _.isEmpty(this.questions) ) { return; }

      _.map(this.questions, function( q ){

        return q.is_correct = 0;
      });

      this.questions[index].is_correct = 1;


    }
  }
}
</script>