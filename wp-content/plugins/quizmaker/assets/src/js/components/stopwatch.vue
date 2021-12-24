<template>
  <div class="aws-stopwatch">
      <span class="aws-stopwatch-value">{{ currentSeconds | twoDigits }}</span>
  </div>
</template>

<script>
import Vue from 'vue';

Vue.filter('twoDigits', function(value){

    if ( value.toString().length <= 1 ) {

        return '0' + value.toString();
    }

    return value.toString();
});

export default {
    props: ['seconds', 'passed'],

    data: function() {
        return {
            currentInterval: null,
            currentSeconds: this.seconds
        }
    },

    mounted: function() {

        var _self = this;

        if( this.currentSeconds > 0 ) {

          this.currentSeconds -= this.passed;
        }

        this.currentInterval = setInterval(function(){

            if( _self.currentSeconds == 0 ){

              clearInterval(_self.currentInterval);

              _self.$emit('complete');

            }else{
              
              _self.currentSeconds--;
            }
            
        }, 1000);
    }
}
</script>
