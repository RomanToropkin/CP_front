<template>
    <div class="container">
        <h1>
            Google Map
        </h1>
        
        <gmap-map v-bind="options" id="map" :options="{minZoom :  11 , maxZoom :  17 }">
            <gmap-cluster>
                <gmap-info-window :options="infoOptions" :position="infoWindowPos" :opened="infoWinOpen" @closeclick="infoWinOpen=false">
                </gmap-info-window>
                <gmap-marker
                        :key="index"
                        v-for="(marker, index) in markers"
                        :position="marker.position"
                        :clickable="true"
                        :label="marker.label"
                        :icon="markerOptions"
                        @click="toggleInfoWindow(marker, index)"
                >
                </gmap-marker>
            </gmap-cluster>
        </gmap-map>
    </div>
</template>

<script>
    import { gmapApi } from "vue2-google-maps";
    import GmapCluster from 'vue2-google-maps/dist/components/cluster'
    import GmapCustomMarker from 'vue2-gmap-custom-marker';
    import Vue from 'vue';

    Vue.component('GmapCluster', GmapCluster);
    Vue.component('GmapCustomMarker', GmapCustomMarker);

    const mapMarker = require('../../img/marker.png');

    export default {
        name: "GoogleMap",
        data() {
            return {
                infoWindowPos: null,
                infoWinOpen: false,
                markerOptions: {
                    url: mapMarker,
                   // size: {width: 60, height: 90, f: 'px', b: 'px',},
                    scaledSize: {width: 40, height: 40, f: 'px', b: 'px',},
                },
                options: {  // опции карты 
                    zoom: 12,
                    zoomControl: true,
                    center: {
                        lat: 53.201118,
                        lng: 44.995112
                    },
                    mapTypeId: 'roadmap'
                },
                infoOptions: {   // всплывающее окно с бэклогом
                    content: '',
                    //optional: offset infowindow so it visually sits nicely on top of our marker
                    pixelOffset: {
                        width: 0,
                        height: -44
                    }
                },
                markers: [
                    {
                        position: { lat: 53.181684, lng: 45.006000 },
                        infoText: '<strong>Marker 1</strong>',
                    },
                    {
                        position: { lat: 53.221786, lng: 44.925017 },
                        infoText: '<strong>Marker 2</strong>'
                    },
                    {
                        position: { lat: 53.224440, lng: 44.945736 },
                        infoText: '<strong>Marker 3</strong>'
                    },
                    {
                        position: { lat: 53.209817, lng: 44.972125 },
                        infoText: '<strong>Marker 4</strong>'
                    },
                    {
                        position: { lat: 53.182392, lng: 45.011556 },
                        infoText: '<strong>Marker 5</strong>'
                    },
                    {
                        position: { lat: 53.218497, lng: 44.888768 },
                        infoText: '<strong>Marker 6</strong>'
                    },
                ]
            };
        },
        components: {
            gmapApi
        },
        computed: {

        },
        methods: {
            placeClick (marker) {
                console.log('this marker was clicked', marker)
            },

            toggleInfoWindow(marker, index) {
                this.infoWindowPos = marker.position;
                this.infoOptions.content = marker.infoText;
                //check if its the same marker that was selected if yes toggle
                if (this.currentMidx == index) {
                    this.infoWinOpen = !this.infoWinOpen;
                }
                //if different marker set infowindow to open and reset current marker index
                else {
                    this.infoWinOpen = true;
                    this.currentMidx = index;
                }
            }
        }
    };
</script>

<style lang="scss" scoped>
    #map {
        height: 500px;
        width: 100%;
        margin: 0 auto;
    }

</style>