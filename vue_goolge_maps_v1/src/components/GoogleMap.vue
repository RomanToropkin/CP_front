<template>
    <div class="container" v-bind:style="{ height: (this.windowHeight) + 'px' }">
        <gmap-map v-bind="options" id="map"
                  :options="{minZoom :  11 , maxZoom :  17, panControl: false, mapTypeControl: false, overviewMapControl: false, streetViewControl: false, fullscreenControl: false}">
            <gmap-cluster :grid-size="gridSize" :styles="clusterStyles">
                <gmap-info-window :options="infoOptions" :position="infoWindowPos" :opened="infoWinOpen"
                                  @closeclick="infoWinOpen=false">
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
            <div class="h1">
                <r class="header_1">Фильтры</r>
            </div>
            <div class="district_box">
                <div class="h3">
                    <r class="header_2">Район</r>
                </div>
                <div id="filter_1">
                    <div class="filter_1_container" v-for="(districts, index) in district">
                        <label :for="'filter1_' + (index) + '_check'" class="label-check option">
                            <input class="label-check__input" type="checkbox" checked
                                   :id="'filter1_' + (index) + '_check'" :value="districts"
                                   @click="check_all_checked(districts)">
                            <span class="label-check__new-input"></span>
                            <a class="checkbox_text">{{districts}}</a>
                        </label>
                    </div>
                </div>

            </div>

            <div class="type_box">
                <div class="h3">
                    <r class="header_2">Тематика</r>
                </div>
                <div id="filter_2">
                    <div class="filter_2_container" v-for="(types, index) in type">
                        <label :for="'filter2_' + (index) + '_check'" class="label-check option">
                            <input class="label-check__input" type="checkbox" checked
                                   :id="'filter2_' + (index) + '_check'"
                                   :value="type" @click="check_all_checked(types)">
                            <span class="label-check__new-input"></span>
                            <a class="checkbox_text">{{types}}</a>
                        </label>
                    </div>
                </div>
            </div>

            <div class="sort_box">
                <div class="h3">
                    <r class="header_2">Сортировка</r>
                </div>
                <div class="sort_block">
                    <label>
                        <select class="sorting">
                            <option>По удаленности</option>
                            <option>По актуальности</option>
                            <option>По дате создания</option>
                            <option>По статусу выполнения</option>
                        </select>
                    </label>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import {gmapApi} from "vue2-google-maps";
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
                selected_district: ["Октябрьский", "Железнодорожный", "Ленинский", "Первомайский"],
                district: ["Октябрьский", "Железнодорожный", "Ленинский", "Первомайский"],

                selected_type: ['Образование', 'Транспорт', 'Экология', 'Социум', 'Безопасность'],
                type: ['Образование', 'Транспорт', 'Экология', 'Социум', 'Безопасность'],

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
                        position: {lat: 53.181684, lng: 45.006000},
                        infoText: '<strong>Marker 1</strong>',
                    },
                    {
                        position: {lat: 53.221786, lng: 44.925017},
                        infoText: '<strong>Marker 2</strong>'
                    },
                    {
                        position: {lat: 53.224440, lng: 44.945736},
                        infoText: '<strong>Marker 3</strong>'
                    },
                    {
                        position: {lat: 53.209817, lng: 44.972125},
                        infoText: '<strong>Marker 4</strong>'
                    },
                    {
                        position: {lat: 53.182392, lng: 45.011556},
                        infoText: '<strong>Marker 5</strong>'
                    },
                    {
                        position: {lat: 53.218497, lng: 44.888768},
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
        mounted() {
            window.onresize = (event) => {

                this.windowHeight = window.innerHeight;

            }
        },
        components: {
            gmapApi
        },
        computed: {},
        methods: {
            check_all_checked(value) {
                let include = this.selected_district.indexOf(value);

                if (include !== -1) {
                    this.selected_district.splice(include, 1);
                } else {
                    this.selected_district.push(value);
                }

                (this.selected_district.length !== this.district.length) ? (this.check_filter_publication_all = false) : (this.check_filter_publication_all = true);

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

    $text_color: #222;
    $second_color: #888888;



    body, h1, h2, h3, h4, h5, h6 {
        margin: 0;
        padding: 0;
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
        height: 100%;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        background-color: #f5f5f5;
        color: $text_color;
        -ms-user-select: none;
        -moz-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;

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
            width: 20px;
            height: 20px;
            margin-top: -2px;
            border: 1px solid #cfcfd1;
        }

        .label-check__input:checked + .label-check__new-input {
            text-align: center;
            background: url('../../img/check-symbol.png') no-repeat center #0039e7;
            background-size: 10px;
            animation: animation-checkbox 0.4s ease-in-out;
        }

        h4 {
            font-size: 16px;
            font-family: ptr, "PTSans-Regular", sans-serif;
            margin-top: 10px;
        }

        h5 {
            font-size: 14px;
            margin-top: 10px;
            margin-bottom: 8px;
            font-family: rml, "PTSans-Regular", sans-serif;
        }

        /*.label-check__input {*/
            /**/
        /*}*/

        .filter_1_container {
            margin-top: 10px;
        }

        .filter_2_container {
            margin-top: 10px;
        }

        .title_filter {
            border-bottom: 1px solid #BBBBBB;
            padding-bottom: 10px;
        }

        .title_district {
            border-top: 1px solid #BBBBBB;
            padding-top: 10px;
            padding-right: 16px;
        }


        .sort_block {
            margin-top: 10px;
        }

        .sorting {
            font-family: rml, "PTSans-Regular", sans-serif;
            width: 100%;
        }

        .sorting option {
            font-family: rml, "PTSans-Regular", sans-serif;
            color: #BBBBBB
        }

        .header_1 {
            font-size: 22px;
            font-family: ptr, "PTSans-Regular", sans-serif;
        }

        .header_2 {
            font-size: 18px;
            font-family: ptr, "PTSans-Regular", sans-serif;
        }

        .h1 {
            border-bottom: 1px solid #dddddd;
            padding-top: 16px;
            padding-right: 24px;
            padding-bottom: 12px;
            padding-left: 24px;
        }

        .h2 {
            padding-top: 8px;
        }

        .h3 {
        }

        .checkbox_text {
            font-family: ptr, "PTSans-Regular", sans-serif;
            font-size: 14px;
        }

        .h1::before {
            background-image: url("../img/filter.svg");
            display: inline-block;
            margin-right: 10px;
            background-repeat: no-repeat;
            width: 20px;
            height: 18px;
            content: '';
        }

        .district_box {
            padding-top: 12px;
            padding-right: 24px;
            padding-bottom: 12px;
            padding-left: 24px;
            background: #fff;
            border-bottom: 1px solid #dddddd;
        }

        .type_box {
            border-top: 1px solid #dddddd;
            margin-top: 12px;
            padding-top: 12px;
            padding-right: 24px;
            padding-bottom: 12px;
            padding-left: 24px;
            background: #fff;
            border-bottom: 1px solid #dddddd;
        }

        .sort_box {
            border-top: 1px solid #dddddd;
            margin-top: 12px;
            padding-top: 12px;
            padding-right: 24px;
            padding-bottom: 12px;
            padding-left: 24px;
            background: #fff;
            border-bottom: 1px solid #dddddd;
            margin-bottom: 12px;
        }

    }

</style>