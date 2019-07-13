<template>
    <div class="container" v-bind:style="{ height: (this.windowHeight) + 'px' }">
        <transition name="slide-fade">
            <div id="modal_cont" v-if="modal_show">
                    <div id="modal">
                        <div id="top">
                            <div id="menu"><img src="../../img/menu.png"/></div>
                            <div id="search"><input type="search" name="search" placeholder="Поиск петиции" v-model="search"></div>
                        </div>
                        <div id="modal_container" v-bind:style="{ height: (this.windowHeight - 56) + 'px' }">
                            <div class="modal_item" v-for="(list_problem, index) in searchList">
                                <div class="modal_item_squre">{{list_problem.content[0].number}}</div>
                                <div class="modal_item_text_container">
                                    <div class="modal_item_text_header">{{list_problem.content[1].header}}</div>
                                    <div class="modal_item_text_a">{{list_problem.content[2].text}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <div id="modal_show_btn" @click="modal_show = !modal_show"><img src="../../img/left_btn.png"/></div>
            </div>
        </transition>
        <transition name="fade"><div id="modal_show_btn_open" @click="modal_show = !modal_show" v-if="modal_show === false"><img src="../../img/right_btn.png"/></div></transition>

        <gmap-map v-bind="options" id="map" :options="{minZoom :  11 , maxZoom :  17, panControl: false, mapTypeControl: false, overviewMapControl: false, streetViewControl: false, fullscreenControl: false}">
            <gmap-cluster :grid-size="gridSize" :styles="clusterStyles">
                <gmap-info-window :options="infoOptions" :position="infoWindowPos" :opened="infoWinOpen" @closeclick="infoWinOpen=false">
                </gmap-info-window>
                <gmap-marker
                        :key="index"
                        v-for="(list_problem, index) in list_problems"
                        :position="list_problem.content[3].position"
                        :clickable="true"
                        :label="list_problem.content[3].label"
                        :icon="markerOptions"
                        @click="toggleInfoWindow(list_problem, index)"
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
    
    import axios from 'axios';
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
                search: '',
                modal_show: true,
                markerOptions: {
                    url: mapMarker,
                   // size: {width: 60, height: 90, f: 'px', b: 'px',},
                    scaledSize: {width: 40, height: 40, f: 'px', b: 'px',},
                },
                list_problems: [
                    {content:[{number:'100'},{header:'Не хватает пешеходного перехода'},{text:'Проблематично пройти в почтовое отделение на Красной'},{position: { lat: 53.181684, lng: 45.006000 }}]},
                ],
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
        created() {
            /* api get */
            axios
                .get('http://projectcenter.ddns.net/api.php?module=get&mode=points')
                .then(response => {

                    let data = response.data.result;
                    console.log(data);
                    for (let key in data) {
                        this.list_problems.push({content: [{number: String(data[key].votes)}, {header: String(data[key].title)}, {text: String(data[key].dascription)}, {position: { lat: Number(data[key].cord_x), lng:  Number(data[key].cord_y)}}, {id_solution: String(data[key].id_solution)}]});
                        
                    }
                })
                .catch(error => console.log(error));

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
            searchList () {
                    return this.list_problems.filter( item => item.content[1].header.toLowerCase().includes(this.search.toLowerCase()))
            }
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

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */ {
        opacity: 0;
    }

    .slide-fade-enter-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .slide-fade-leave-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active до версии 2.1.8 */ {
        transform: translateX(-500px);
      //  opacity: 0;
    }

    #modal_show_btn_open {
        position: absolute;
        z-index: 1;
        width: 40px;
        height: 40px;
        background-color: #ffffff;
        border: 1px solid #bbb;
        margin: 8px 0 0 4px;
        border-radius: 5px;
        display:flex;
        align-items: center;
        justify-content: center;
        text-align: center;

        img {
            width: 36px;
            height: 36px;

        }
    }

    #modal_show_btn_open:hover {
        cursor: pointer;
    }

    // левое меню 1
    #modal_cont {
        position: absolute;
        z-index: 5;
        display: flex;
        flex-direction: row;
    }

    #modal {
      //  position: absolute;
      //  z-index: 5;
        top: 0;
        left: 0;
        width: 500px;
        //height: 400px;
        background-color: #fff;
        font-size: 16px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);

        #top {
            background-color: #434240;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 0 20px;

            #search input {
                height: 40px;
                width: 400px;
                margin: 8px 0 8px 20px;
                padding-right: 10px;
                font-size: 16px;
                padding-left: 10px;
            }

        }

        #menu {
            width: 30px;
            height: 30px;
            img {
                width: 30px;
                height: 30px;
            }
        }

        #modal_container {
            overflow: auto;
            
            .modal_item {
                display: flex;
                flex-direction: row;
                padding: 10px 0;
                border-bottom: 1px solid #bbb;
                
                .modal_item_squre {
                    width: 60px;
                    height: 60px;
                    background-color: #3300FF;    // default #3300FF
                    border-radius: 50px;
                    border: 1px solid #bbb;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    color: #fff;
                    margin: 0 20px;
                }

                .modal_item_text_container {
                    margin-right: 20px;
                    width: 320px;

                    .modal_item_text_header {
                        font-weight: bold;
                    }
                    .modal_item_text_a {
                        color: #a5a5a5;
                    }
                }


            }
        }
    }

    #modal_show_btn {
        width: 40px;
        height: 40px;
        background-color: #ffffff;
        border: 1px solid #bbb;
        margin: 8px 0 0 4px;
        border-radius: 5px;
        display:flex;
        align-items: center;
        justify-content: center;
        text-align: center;

        img {
            width: 36px;
            height: 36px;
           
        }
    }

    #modal_show_btn:hover {
        cursor: pointer;
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