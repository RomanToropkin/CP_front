<template>
  <div id="app">
    <google-map />
  </div>
</template>

<script>
import GoogleMap from "./components/GoogleMap.vue";

export default {
  name: 'app',
  data () {
      return {

      };
  },
    components: {
        GoogleMap
    }
}
</script>

<style lang="scss">
  #app {
    font-family: Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin-top: 60px;
  }
</style>
