import Vue from 'vue'
import App from './App.vue'
import * as VueGoogleMaps from "vue2-google-maps";

Vue.use(VueGoogleMaps, {
    load: {
        key: "AIzaSyCvAgYH24trnhlT7Qef2AY8LpT2X0TMyRI",
        libraries: "places", // necessary for places input
        region: "ru,uk,en"
    }
});

Vue.config.productionTip = false;

new Vue ({
  el: '#app',
  render: h => h(App)
})
