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
  body {
    margin: 0;
    padding: 0;
  }
</style>
