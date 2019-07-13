<template>
    <div class="container">
        <h1>
            Google Map
        </h1>

        <ul class="list-inline">
            <li class="list-inline-item">
                <a href="#" class="btn btn-success" data-location="Location 1" @click="myclick('Location 1')" >Location 1</a>
            </li>
            <li class="list-inline-item">
                <a href="#" class="btn btn-success" data-location="Location 2" @click="myclick('Location 2')">Location 2</a>
            </li>
            <li class="list-inline-item">
                <a href="#" class="btn btn-success" data-location="Location 3" @click="myclick('Location 3')">Location 3</a>
            </li>
        </ul>

        <gmap-map v-bind="options" id="map">
            <gmap-cluster>
                <gmap-marker
                        :key="index"
                        v-for="(m, index) in markers"
                        :position="m.position"
                        :clickable="true"
                        :draggable="true"
                        :label="m.label"
                />
            </gmap-cluster>
        </gmap-map>
    </div>
</template>

<script>
    import { gmapApi } from "vue2-google-maps";
    import GmapCluster from 'vue2-google-maps/dist/components/cluster'
    import Vue from 'vue'

    Vue.component('GmapCluster', GmapCluster);

    export default {
        name: "GoogleMap",
        data() {
            return {
                options: {
                    zoom: 5,
                    center: {
                        lat: 10.0,
                        lng: 10.0
                    },
                    mapTypeId: 'roadmap'
                },
                markers: [
                    {
                        position: { lat: 10.0, lng: 10.0 }
                    },
                    {
                        position: { lat: 11.0, lng: 11.0 }
                    },
                    {
                        position: { lat: 11.0, lng: 10.0 }
                    },
                    {
                        position: { lat: 10.5, lng: 10.5 }
                    },
                ]
            };
        },
        computed: {
            gmapApi
        },
        methods: {
            myclick() {
                console.log(1111111111111111)
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