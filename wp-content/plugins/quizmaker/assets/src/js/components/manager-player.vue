<template>
    <div class="manager-player">

        <v-card>
            <v-card-text>
                <aws-player :music.sync="songs" :settings.sync="settings" ref="player"></aws-player>
                
                <v-list v-if="songs.length > 0" class="mt-3">

                    <v-list-tile avatar>
                        <v-list-tile-action>
                          <v-checkbox v-model="settings.is_background"></v-checkbox>
                        </v-list-tile-action>
                        <v-list-tile-content>
                          <v-list-tile-title>Background Music</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>

                </v-list>

                <div class="mt-3">
                    <v-btn small primary @click.stop="addMusic" class="ml-0">Add</v-btn>
                    <v-btn small @click.stop="removeMusic">Remove</v-btn>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>
<script>
    import AWSPlayer from "./aws-player.vue";

    export default {
        components: {
            'aws-player': AWSPlayer
        },
        props: {
            value: {
                type: [Object],
                default: function(){

                    return {
                        songs: [],
                        settings: {
                            is_background: true
                        }
                    };
                }
            }
        },
        data: function() {

            var params = {
                songs: [],
                settings: {
                    is_background: true
                }
            };
            

            if( typeof this.value.songs != 'undefined' ) {

                params.songs    = this.value.songs;
            }

            if( typeof this.value.settings != 'undefined' ) {

                params.settings = this.value.settings;
            }


            // console.log(this.value.settings.is_background);
            if( typeof this.value.settings.is_background != 'boolean' ) {

                params.settings.is_background = params.settings.is_background == 'true';
            }

            return params;
        },
        mounted: function() {

            var aplayer = this.$refs.player.control;
        },
        methods: {

            addMusic: function() {

                var _self = this;

                var frameWPMedia = this.frameWPMedia;

                if ( frameWPMedia ) {

                    frameWPMedia.close();
                    return;
                }
                
                frameWPMedia = wp.media({
                    title: 'Select or Upload Media Of Your Chosen Persuasion',
                    button: {
                        text: 'Use this media'
                    },
                    multiple: false
                });
                
                frameWPMedia.on( 'select', function() {

                    var attachment = frameWPMedia.state().get('selection').first().toJSON();
                    
                    if( attachment.url ) {

                        _self.songs = [{
                          title: attachment.title,
                          author: attachment.author,
                          url: attachment.url,
                          pic: attachment.thumb.src,
                          lrc: '[00:00.00]lrc here\n[00:01.00]aplayer'
                        }];
                        
                        _self.$emit('update:value', {
                            settings: _self.settings,
                            songs: _self.songs
                        });
                    }
                    
                });

                frameWPMedia.open();
            },
            removeMusic: function() {

                this.songs = [];

                this.$emit('update:value', this.songs);
            }
        }
    }


</script>