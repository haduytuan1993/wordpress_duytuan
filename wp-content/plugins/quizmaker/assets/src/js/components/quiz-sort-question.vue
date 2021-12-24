<template>
  <div class="quiz-composer quiz-sort-question">
    <v-container fluid grid-list-lg>
      <v-layout row wrap>
        <v-flex sm12>
          <div class="question-content">
            <textarea v-model="question.content"></textarea>
          </div>
        </v-flex>
        
        <v-flex sm8>
        	<div class="sort-options">
        		
            <div class="sort-option">
              
              <div v-for="(q, index) in orderedQuestions" :key="q.id" :id="q.id" class="sort-item">
                <span class="number" v-text="parseInt(q.order)+1"></span>
                <div class="input"><input type="text" v-model="q.content"/></div>
                <v-btn @click.stop="removeQuestion(q.id)" fab small dark class="red elevation-0 close"><v-icon>close</v-icon></v-btn>
              </div>
              
            </div>
        		
            <v-btn v-if="orderedQuestions.length < 4" small round primary class="mt-3" @click="addQuestion">Add</v-btn>

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

    this.question.type = 4;
    
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

      this.question.options = [
        {id: 1, order: 0, content: 'Content 1'},
        {id: 2, order: 1, content: 'Content 2'},
        {id: 3, order: 2, content: 'Content 3'}
      ];
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
      sorting: null,
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

  	var _self = this;

  	this.sorting = jQuery(this.$el).find('.sort-option').sortable({
      stop: function(event, ui){

          var order = _self.sorting.sortable('toArray');

          var newOder = [];
          
          _.each(_self.questions, function(op, index){

            _.each(order, function(or, index){

                if( op.id == or ) {

                  op.order = index;

                  newOder.push(op);
                }
            });

          });

          _self.questions = newOder;
          
      }
    });

  },
  computed: {
    itemsMediaManager: function(){

      var videos = this.videos;

      return videos;
    },
    orderedQuestions: function(){

      return _.sortBy(this.questions, 'order');
    }
  },
  methods: {
    save: function(){

      this.question.settings.duration = this.duration;
    },
    removeQuestion: function( id ){

      if( this.questions.length > 1 ){

        var removedIndex = 0;

        _.each(this.questions, function(q, i){

          if( q.id == id ) {

            removedIndex = i;
          }
        });

        this.questions.splice(removedIndex, 1);

        this.updateOrder();

      }
    },
    addQuestion: function( index ){

      this.questions.push({
        id: _.now(),
        content: 'New Content',
        order: this.questions.length
      });
    },
    updateOrder: function(){

      _.each(this.questions, function(q, index){

          q.order = index;
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
    
  }
}
</script>