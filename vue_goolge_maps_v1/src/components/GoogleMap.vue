<template>
    <div class="container" v-bind:style="{ height: (this.windowHeight) + 'px' }">
        <gmap-map v-bind="options" id="map" :options="{minZoom :  11 , maxZoom :  17, panControl: false, mapTypeControl: false, overviewMapControl: false, streetViewControl: false, fullscreenControl: false}">
            <gmap-cluster :grid-size="gridSize" :styles="clusterStyles">
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

        <div id="filter">
            <h4>Фильтры</h4>
            <h5>Район</h5>
            <div id="filter_1">
                <div class="filter_1_container" v-for="(districts, index) in district">
                    <label :for="'filter_' + (index) + '_check'" class="label-check option">
                        <input class="label-check__input" type="checkbox" checked :id="'filter_' + (index) + '_check'" :value="districts" @click="check_all_checked(districts)">
                        <span class="label-check__new-input"></span>
                        <a>{{districts}}</a>
                    </label>
                </div>
            </div>
            <h5>Тематика</h5>
            <div id="filter_2">
                <div class="filter_2_cont"></div>
            </div>
            <h4>Сортировка</h4>

        </div>
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
    const mapCluster34 = require('../../img/Ellipse34.png');
    const mapCluster44 = require('../../img/Ellipse44.png');
    const mapCluster64 = require('../../img/Ellipse64.png');

    export default {
        name: "GoogleMap",
        data() {
            return {
                infoWindowPos: null,
                infoWinOpen: false,
                gridSize: 100,
                markerOptions: {
                    url: mapMarker,
                   // size: {width: 60, height: 90, f: 'px', b: 'px',},
                    scaledSize: {width: 40, height: 40, f: 'px', b: 'px',},
                },
                selected_district: ["Октябрьский","Железнодорожный","Ленинский","Первомайский"],
                district: ["Октябрьский","Железнодорожный","Ленинский","Первомайский"],
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
                ],
                clusterStyles: [
                    {
                        textColor: '#fff',
                        url: mapCluster34,
                        height: 34,
                        width: 34,
                        
                    },
                    {
                        textColor: '#fff',
                        url: mapCluster44,
                        height: 44,
                        width: 44
                    },
                    {
                        textColor: '#fff',
                        url: mapCluster64,
                        height: 64,
                        width: 64
                    }
                ],
                windowHeight: window.innerHeight,
            };
        },
        mounted () {
            window.onresize = (event) => {

                this.windowHeight = window.innerHeight;

                }
        },
        components: {
            gmapApi
        },
        computed: {

        },
        methods: {
            check_all_checked(value) {
                let include = this.selected_district.indexOf(value);

                if (include !== -1) {
                    this.selected_district.splice(include, 1);
                } else {
                    this.selected_district.push(value);
                }

                (this.selected_district.length !== this.district.length)?(this.check_filter_publication_all = false):(this.check_filter_publication_all = true);

              /*  if (this.selected_district.length !== this.types_of_publication.length) {
                    this.check_filter_publication_all = false;
                    document.getElementById('filter_check_all').checked = true;      // disable checkbox
                } else {
                    this.check_filter_publication_all = true;
                    document.getElementById('filter_check_all').checked = false;      // enable checkbox
                }    */

                 console.log(this.selected_district);
                // console.log("include = " + include);
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
    @import "../styles_fonts";
    
    body, h1, h2, h3, h4, h5, h6 {
        margin: 0;
        padding: 0;
        font-family: Roboto-Light, "Roboto-Light", sans-serif;
    }

    #map {
        width: 100%;
        height: 100%;
        margin: 0 auto;
    }

    #filter {
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        min-height: 400px;
        background-color: #fff;

        h4 {
            font-size: 24px;
           
        }

        .filter_1_container {
            padding: 5px 10px;
        }

        // чекбоксы
        .label-check {
            cursor: pointer;
            margin-left: 35px;
            position: relative
        }

        /* Cкрываем стандартный чекбокс */
        .label-check__input {
            display: none;
        }

        /* Новый чекбокс */
        .label-check__new-input {
            position: absolute;
            left: -35px;
            top: 4px;
            width: 28px;
            height: 28px;
            border: 1px solid #cfcfd1;
            border-radius: 3px;
        }

        /* Новый чекбокс при выборе */
        .label-check__input:checked + .label-check__new-input {
            background: #2f4857;
        }

        .label-check__input:checked + .label-check__new-input {
            background: url('../../img/check.png') no-repeat center;
            animation: animation-checkbox 0.4s ease-in-out;
        }
        @keyframes animation-checkbox {
            0% {
                transform: scale(0.8);
            }
            70% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }
        
    }

</style>