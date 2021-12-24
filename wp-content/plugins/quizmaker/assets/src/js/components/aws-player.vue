<template>
    <div class="aplayer"></div>
</template>
<script>
    import APlayer from 'aplayer';
    export default {
        props: {
            narrow: {
                type: Boolean,
                default: false
            },
            autoplay: {
                type: Boolean,
                default: false
            },
            showlrc: {
                type: Number,
                default: 0
            },
            mutex: {
                type: Boolean,
                default: false
            },
            theme: {
                type: String,
                default: '#b7daff'
            },
            mode: {
                type: String,
                default: 'circulation'
            },
            preload: {
                type: String,
                default: 'auto'
            },
            listmaxheight: String,
            music: {
                type: [Object, Array],
                required: true,
                default: function(){

                    return [];
                },
                validator: function(value) {
                    // let songs;
                    // if (!(value instanceof Array)) {
                    //     songs = [value];
                    // } else {
                    //     songs = value;
                    // }
                    // for (let i = 0; i < songs.length; i++) {
                    //     let song = songs[i];
                    //     if (!song.url || !song.title || !song.author) {
                    //         song.title = song.title || 'Untitled';
                    //         song.author = song.author || 'Unknown';
                    //         return false;
                    //     }
                    // }
                    return true;
                }
            },
            settings: {
                type: [Object],
                default: function(){

                    return {
                        is_background: false
                    }
                }
            } 
        },
        data: function() {
            return {
                
            };
        },
        watch: {
            music: function(music) {
                
                if( typeof this.music != 'undefined' && music.length > 0 ){

                    this.control = new APlayer({
                        element: this.$el,
                        narrow: this.narrow,
                        autoplay: this.autoplay,
                        showlrc: this.showlrc,
                        mutex: this.mutex,
                        theme: this.theme,
                        preload: this.preload,
                        mode: this.mode,
                        listmaxheight: this.listmaxheight,
                        music: music
                    });

                    if(this.autoplay) {
                        this.control.play();
                    }

                }else{
                    
                    if(  this.control != null && typeof this.control != 'undefined' && typeof this.control.destroy == 'function' ){

                        this.control.destroy();
                    }
                }
            }
        },
        mounted: function() {
            
            if( typeof this.music != 'undefined' && this.music.length > 0 ){

                var _self  = this;

                var player = this.control = new APlayer({
                    element: this.$el,
                    narrow: this.narrow,
                    autoplay: this.autoplay,
                    showlrc: this.showlrc,
                    mutex: this.mutex,
                    theme: this.theme,
                    preload: this.preload,
                    mode: this.mode,
                    listmaxheight: this.listmaxheight,
                    music: this.music
                });

                player.on('play', function(){
                    _self.$emit('play');
                });
                player.on('pause', function(){
                    _self.$emit('pause');
                });
                player.on('canplay', function(){
                    _self.$emit('canplay');
                });
                player.on('playing', function(){
                    _self.$emit('playing');
                });
                player.on('ended', function(){
                    _self.$emit('ended');
                });
                player.on('error', function(){
                    _self.$emit('error');
                });

                if( this.settings.is_background == 'true' ) {

                    player.play();

                    jQuery(this.$el).css('display', 'none');
                }
            }
        },
        beforeDestroy: function() {

            if( this.control != null && typeof this.control.destroy == 'function'){

                this.control.destroy();
            }
        }
    }


</script>