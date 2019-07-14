<template>
    <div class="container" v-bind:style="{ height: (this.windowHeight) + 'px' }">
        <transition name="fade">
            <div id="background_black" v-if="second_menu_show"></div>
        </transition>
        <transition name="slide-fade">
            <div id="second_menu" v-if="second_menu_show" v-bind:style="{ height: (this.windowHeight) + 'px' }">
                <div id="second_menu_header">
                    <img src="../../img/logo.png" width="72px" height="72px" >
                    <div id="second_menu_header_h">Система принятия общественных решений</div>
                    <div id="second_menu_header_btn" @click="second_menu_show = false"><img
                            src="../../img/left_btn_2.png" width="32px" height="32px"/></div>
                </div>
                <div id="second_menu_second">
                    <div class="second_menu_second_content">
                        <img src="../../img/petit.png"/>
                        <a @click="menu_select('Все петиции')">Все петиции</a>
                    </div>
                    <div class="second_menu_second_content">
<<<<<<< Updated upstream
                        <img src="../../img/pen.png"/>
                        <a>Вы подписали</a>
=======
                        <img src="../../img/add.svg"/>
                        <a @click="menu_select('Создать петицию')">Создать петицию</a>
>>>>>>> Stashed changes
                    </div>
                    <div class="second_menu_second_content">
                        <img src="../../img/email.png"/>
                        <a @click="menu_select('Созданные вами петиции')">Созданные вами петиции</a>
                    </div>
<<<<<<< Updated upstream
=======
                    <div class="second_menu_second_content">
                        <img src="../../img/pen.png"/>
                        <a @click="menu_select('Вы подписали')">Вы подписали</a>
                    </div>
>>>>>>> Stashed changes
                </div>
                <div id="second_menu_third">
                    <div class="second_menu_second_content">
                        <img src="../../img/people.png"/>
                        <a>Профиль</a>
                    </div>
                    <div class="second_menu_second_content">
                        <img src="../../img/exit.png"/>
                        <a>Выход</a>
                    </div>
                </div>
            </div>
        </transition>
        <transition name="slide-fade">
            <div id="modal_cont" v-if="modal_show">

                <div class="modal" v-if="all_petition">                                                             <!-- все петиции -->
                    <div class="top">
                        <div class="menu" @click="second_menu_show = true"><img src="../../img/menu.png"/></div>
                        <div class="search"><input type="search" name="search" placeholder="Поиск петиции"
                                                v-model="search"></div>
                    </div>
                    <div id="table">
                        <div id="important">Важность</div>
                        <div id="description">Описание петиции</div>
                    </div>
                    <div id="modal_container" v-bind:style="{ height: (this.windowHeight - 101) + 'px' }">
                        <div class="modal_item" v-for="(list_problem, index) in searchList">
                            <div class="modal_item_squre">{{list_problem.content[0].number}}</div>
                            <div class="modal_item_text_container">
                                <div class="modal_item_text_header">{{list_problem.content[1].header}}</div>
                                <div class="modal_item_text_a">{{cut_text(list_problem.content[2].text)}}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal" v-if="create_petition">                                                          <!-- созданные петиции -->
                    <div class="top">
                        <div class="menu" @click="second_menu_show = true"><img src="../../img/menu.png"/></div>
                        <div class="top_header"><a>Создание петиции</a></div>
                        <div class="modal_create_petition_close_btn" @click="menu_select('Все петиции')"><img src="../../img/icon_close.png"/></div>
                    </div>
                    <div class="create_petition_container" v-bind:style="{ height: (this.windowHeight - 60) + 'px' }">
                        <a>Название петиции</a>
                        <input  placeholder="Введите название петиции" v-model="create_petition_name">
                        <a>Описание</a>
                        <textarea v-model="create_petition_description" placeholder="Опишите вашу проблему"></textarea>
                        <a>Загрузить изображение</a>
                        <input type="file" name="image">
                        <a v-if="this.add_position.lat !== ''">Координаты выбранной точки:</a>
                        <a v-else>Выберите точку на карте где находится проблема</a>
                        <input v-if="this.add_position.lat !== ''" :value="this.add_position.lat + ', ' + this.add_position.lng" disabled>
                        <input v-else disabled>
                    </div>
                    <div id="create_petition_publicated">Опубликовать</div>
                </div>


                <div id="modal_show_btn" @click="modal_show = !modal_show"><img src="../../img/left_btn.png"/></div>
            </div>
        </transition>
        <transition name="fade">
            <div id="modal_show_btn_open" @click="modal_show = !modal_show" v-if="modal_show === false"><img
                    src="../../img/right_btn.png"/></div>
        </transition>

       <!-- <gmap-place-input label="Add a marker at this place" :select-first-on-enter="true" @place_changed="updatePlace($event)"></gmap-place-input>   -->

        <gmap-map v-bind="options" id="map"
                  @click="map_cluster_add($event)"
                  :options="{minZoom :  11 , maxZoom :  17, panControl: false, mapTypeControl: false, overviewMapControl: false, streetViewControl: false, fullscreenControl: false}">
            <gmap-cluster :grid-size="gridSize" :styles="clusterStyles">
                <gmap-info-window :options="infoOptions" :position="infoWindowPos" :opened="infoWinOpen"
                                  @closeclick="infoWinOpen=false">
                </gmap-info-window>
                <div v-if="!create_petition">
                    <gmap-marker
                            :key="index"
                            v-for="(list_problem, index) in searchList"
                            :position="list_problem.content[3].position"
                            :clickable="true"
                            :label="list_problem.content[3].label"
                            :icon="markerOptions"
                            @click="toggleInfoWindow(list_problem, index)"
                    >
                    </gmap-marker>
                </div>
            </gmap-cluster>

            <div v-if="create_petition">
                <gmap-marker
                        :key="1234"
                        :position="add_position"
                        :clickable="true"
                        :icon="markerOptions"
                >
                </gmap-marker>
            </div>
        </gmap-map>
        <transition name="fade">
            <div class="filter_btn_show_left" v-if="!filter_show" @click="filter_show = true"><img
                    src="../../img/filter.svg"/></div>
        </transition>
        <transition name="right">
            <div id="filter" v-if="filter_show">
                <div class="h1">
                    <img src="../../img/filter.svg" >
                    <a class="header_1">Фильтры</a>
                    <div class="filter_btn_show" @click="filter_show = false"><img src="../../img/right_btn_2.png"/>
                    </div>
                </div>
                <div class="district_box">
                    <div class="h3">
                        <a class="header_2">Район</a>
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
                        <a class="header_2">Тематика</a>
                    </div>
                    <div id="filter_2">
                        <div class="filter_2_container" v-for="(types, index) in type">
                            <label :for="'filter2_' + (index) + '_check'" class="label-check option">
                                <input class="label-check__input" type="checkbox" checked
                                       :id="'filter2_' + (index) + '_check'"
                                       :value="type" @click="check_all_checked_type(types)">
                                <span class="label-check__new-input"></span>
                                <a class="checkbox_text">{{types}}</a>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="sort_box">
                    <div class="h3">
                        <a class="header_2">Сортировка</a>
                    </div>
                    <div class="sort_block">
                        <label>
                            <select class="sorting" v-model="sort_block_selected">
                                <option>По важности</option>
                               <!-- <option>По удаленности</option> -->
                                <option>По дате создания</option>
                                <option>По статусу выполнения</option>
                            </select>
                        </label>
                    </div>
                </div>

            </div>
        </transition>
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

    const mapMarker = require('../../img/vector_marker.svg');
    const mapCluster34 = require('../../img/Ellipse34.png');
    const mapCluster44 = require('../../img/Ellipse44.png');
    const mapCluster64 = require('../../img/Ellipse64.png');
    // const mapCluster34 = require('../../img/circle.svg');
    // const mapCluster44 = require('../../img/circle.svg');
    // const mapCluster64 = require('../../img/circle.svg');

    export default {
        name: "GoogleMap",
        data() {
            return {
                infoWindowPos: null,
                infoWinOpen: false,
                second_menu_show: false,
                gridSize: 100,
                search: '',
                add_position: {lat: '', lng: ''},
                modal_show: true,
                filter_show: true,
                all_petition: true, // стр. все петиции
                create_petition: false, // стр. создать петицию
                you_create_petition: false, // стр. созданные вами петиции
                you_subscribe: false,    // стр. вы подписали
                sort_block_selected: '',
                create_petition_name: '', // название петиции
                create_petition_description: '',  // Описание петиции
                markerOptions: {
                    url: mapMarker,
                    // size: {width: 60, height: 90, f: 'px', b: 'px',},
                    scaledSize: {width: 40, height: 40, f: 'px', b: 'px',},
                },
                list_problems: [
                    //{content:[{number:'100'},{header:'Не хватает пешеходного перехода'},{text:'Проблематично пройти в почтовое отделение на Красной'},{position: { lat: 53.181684, lng: 45.006000 }}]},
                ],
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
                        let num = data[key].votes;
                        if (num === null) {
                            num = '-';
                        } else {
                            num += ' %';
                        }
                        this.list_problems.push({content: [{number: String(num)}, {header: String(data[key].title)}, {text: String(data[key].dascription)}, {position: { lat: Number(data[key].cord_x), lng:  Number(data[key].cord_y)}}, {id_solution: String(data[key].id_solution)}, {district: String(data[key].district)}, {type: String(data[key].type)}, {time: String(data[key].time)}, {status: String(data[key].status)}]});
                        
                    }
                })
                .catch(error => console.log(error));

        },
        mounted() {
            window.onresize = (event) => {

                this.windowHeight = window.innerHeight;

            }
        },
        components: {
            gmapApi
        },
        watch: {
            create_petition(after, before) {
                setMapOnAll(null);
                console.log("before = " + before);
                if (before === true) {
                    this.add_position.lat = '';
                    this.add_position.lng = '';
                }
            }
        },
        computed: {
            // сортировка
            searchList () {
                 if (this.sort_block_selected === 'По важности') {
                     this.list_problems.sort(function (a, b) {
                         let a1, b1;
                         if (a.content[0].number !== '-') {
                             a1 = Number(a.content[0].number.slice(0, -2))
                         } else {
                             a1 = Number('0')
                         }
                         if (b.content[0].number !== '-') {
                             b1 = Number(b.content[0].number.slice(0, -2))
                         } else {
                             b1 = Number('0')
                         }

                         if ((a1) < (b1)) {
                             return 1;
                         }
                         if ((a1) > (b1)) {
                             return -1;
                         }

                         return 0;
                     });
                 } else if (this.sort_block_selected === 'По дате создания') {
                     //7 time
                     this.list_problems.sort(function (a, b) {

                         if ((a.content[7].time) < (b.content[7].time)) {
                             return 1;
                         }
                         if ((a.content[7].time) > (b.content[7].time)) {
                             return -1;
                         }

                         return 0;
                     });
                 } else if (this.sort_block_selected === 'По статусу выполнения') {
                     //7 time
                     this.list_problems.sort(function (a, b) {
                         let a1, b1;
                         if (String(a.content[8].status) === 'Выполнен') a1 = 3;
                         if (String(a.content[8].status) === 'В процессе') a1 = 2;
                         if (String(a.content[8].status) === 'Не выполнен') a1 = 1;

                         if (String(b.content[8].status) === 'Выполнен') b1 = 3;
                         if (String(b.content[8].status) === 'В процессе') b1 = 2;
                         if (String(b.content[8].status) === 'Не выполнен') b1 = 1;

                         console.log('По статусу выполнения = ' + String(a.content[8].status).length);
                         if (a1 < b1) {
                             return 1;
                         }
                         if (a1 > b1) {
                             return -1;
                         }

                         return 0;
                     });
                 }

                    return this.list_problems.filter( item => ((item.content[1].header.toLowerCase().includes(this.search.toLowerCase())) && (this.selected_district.includes(item.content[5].district)) && (this.selected_type.includes(item.content[6].type))))
            }
        },
        methods: {
            updatePlace(place) {
                    console.log("lat = " + place.geometry.location.lat());
                    console.log("lng = " + place.geometry.location.lng());
                   // marker.position.lng = place.geometry.location.lng();

            },
             map_cluster_add(r) {
                /* console.log(r.latLng.lat());
                 console.log(r.latLng.lng());    */
                 this.add_position.lat = r.latLng.lat();
                 this.add_position.lng = r.latLng.lng();
             },
            placeMarker(event) {
                   console.log(event.get('coordPosition'));
            },
            menu_select(name) {
                this.second_menu_show = false;
                
                if (name === 'Все петиции') {
                    this.all_petition = true; // стр. все петиции
                    this.create_petition = false; // стр. создать петицию
                    this.you_create_petition = false; // стр. созданные вами петиции
                    this.you_subscribe = false;    // стр. вы подписали
                } else if (name === 'Создать петицию') {
                    this.all_petition = false; // стр. все петиции
                    this.create_petition = true; // стр. создать петицию
                    this.you_create_petition = false; // стр. созданные вами петиции
                    this.you_subscribe = false;    // стр. вы подписали
                } else if (name === 'Созданные вами петиции') {
                    this.all_petition = false; // стр. все петиции
                    this.create_petition = false; // стр. создать петицию
                    this.you_create_petition = true; // стр. созданные вами петиции
                    this.you_subscribe = false;    // стр. вы подписали
                } else if (name === 'Вы подписали') {
                    this.all_petition = false; // стр. все петиции
                    this.create_petition = false; // стр. создать петицию
                    this.you_create_petition = false; // стр. созданные вами петиции
                    this.you_subscribe = true;    // стр. вы подписали
                }
            },
            cut_text(value) {
                return (value.length >= 90) ? ((value.slice(0, 90) + "...")) : (value)

            },
            check_all_checked_type(types) {
                let include = this.selected_type.indexOf(types);

                if (include !== -1) {
                    this.selected_type.splice(include, 1);
                } else {
                    this.selected_type.push(types);
                }

                (this.selected_type.length !== this.type.length) ? (this.check_filter_publication_all = false) : (this.check_filter_publication_all = true);

                console.log(this.selected_type);
            },
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

            toggleInfoWindow(list_problem, index) {
                this.search = list_problem.content[1].header;
            }
        }
    };
</script>

<style lang="scss">
    @import "../styles_fonts";

    $text_color: #222222;
    $second_text_color: #717171;
    $def_font: PTSans-Regular, "PTSans-Regular", sans-serif;
    $bold_font: PTsans-Bold, "PTSans-Bold", sans-serif;
    $bg_color: #f5f5f5;

    body, h1, h2, h3, h4, h5, h6 {
        margin: 0;
        padding: 0;
        font-family: $def_font;
        overflow: hidden;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }

    .fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */
    {
        opacity: 0;
    }

    .slide-fade-enter-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-leave-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active до версии 2.1.8 */
    {
        transform: translateX(-500px);
        //  opacity: 0;
    }

    #table {
        display: flex;
        flex-direction: row;
        height: 40px;
        width: 100%;
        border-bottom: 1px solid #5f5f5f;
        background-color: #cecdc7;
        font-family: $def_font;
    }


    #important {
        width: 20%;
        height: 100%;
        border-right: 1px solid #5f5f5f;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #description {
        width: 80%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal {
        height: 60px;
        font-family: PTsans-Bold, "PTSans-Bold", sans-serif;
    }

    .top_header {
        padding-left: 20px;
        font-size: 19px;
        color: #fff;
    }


    .right-enter-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
        overflow: hidden;
    }

    .right-leave-active {
        transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
        overflow: hidden;
    }

    .right-enter, .right-leave-to
        /* .slide-fade-leave-active до версии 2.1.8 */
    {
        transform: translateX(500px);
        //  opacity: 0;
        overflow: hidden;
    }

    #background_black {
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.51);
        position: absolute;
        z-index: 8;
    }

    // 2-е меню

    #second_menu {
        position: absolute;
        z-index: 10;
        top: 0;
        left: 0;
        width: 450px;
        background-color: $bg_color;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        font-family: $bold_font;

        #second_menu_header, #second_menu_second {
            border-bottom: 1px solid #bbb;
        }

        #second_menu_second, #second_menu_third{

            padding-left: 16px;
            padding-top: 8px;

            img {
                width: 24px;
                height: 24px;
            }
        }

        #second_menu_header_h{
            display: flex;
            align-items: center;
            justify-content: center;
            padding-left: 16px;
            padding-right: 16px;
            font-size: 21px;
            width: 100%;
        }

        #second_menu_header_btn{

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .second_menu_second_content{

            display: flex;
            flex-direction: row;
            padding-bottom: 20px;

            a{
                display: flex;
                align-items: center;
                justify-content: center;
                padding-left: 30px;
            }

            a:visited {
                color: #551a8b;
            }
            a:hover {
                color: #06e;
                cursor: pointer;
            }
            a:focus {
                outline: thin dotted;
            }
            a:hover, a:active {
                outline: 0;
            }

            a, a:visited, a:active {
                text-decoration: none;
                color: $text_color;
                -webkit-transition: all .1s ease-in-out;
            }

        }

        .second_menu_second_content.hover{
            color: blue;
        }

        #second_menu_header {
            display: flex;
            flex-direction: row;
            padding: 16px;
            height: 60px;
            #second_menu_header_btn:hover {
                cursor: pointer;
            }
        }

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
        display: flex;
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

    .modal {
        //  position: absolute;
        //  z-index: 5;
        top: 0;
        left: 0;
        width: 500px;
        //height: 400px;
        background-color: #fff;
        font-size: 16px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);

        // 2-е меню
        .create_petition_container {
            display: flex;
            flex-direction: column;
            color: #222;
            padding: 5px 30px;
            background-color: #fff;


            a {
                font-size: 18px;
                font-family: $def_font;
                margin: 10px 0;
            }

            input {
                height: 30px;
                padding-right: 10px;
                font-size: 16px;
                padding-left: 10px;
                border-radius: 5px;
                outline:none;
                border: 1px solid #9a9a9a;
            }

            textarea {
                height: 300px !important;
                border-radius: 5px;
                font-size: 16px;
                outline:none;
                border: 1px solid #9a9a9a;
                padding: 6px 10px;
            }

            input[name="image"] {
                height: 30px;
                padding-right: 10px;
                font-size: 16px;
                padding-left: 10px;
                border-radius: 5px;
                outline:none;
                border: none;
            }

            .create_petition_coords_a {
                display: flex;
                justify-content: center;
                padding-top: 10px;
            }
        }



        .search {
            width: 100%;
            margin-left: 20px;
        }

        .top {
            background-color: #434240;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding-left: 20px;
            padding-right: 20px;
            padding-top: 10px;
            padding-bottom: 10px;

            .modal_create_petition_close_btn {
                position: absolute;
                right: 70px;
                img {
                    width: 30px;
                    height: 30px;
                    margin-top: 4px;

                }
            }

            .modal_create_petition_close_btn:hover {
                cursor: pointer;
            }

            .search input {
                height: 40px;
                width: 100%;
                padding-right: 10px;
                font-size: 16px;
                padding-left: 10px;
            }

        }

        .menu {
            width: 30px;
            height: 30px;

            img {
                width: 30px;
                height: 30px;
            }
        }

        .menu:hover {
            cursor: pointer;
        }

        #modal_container {
            overflow: auto;
            font-family: $def_font;
            background-color: #f5f5f5;

            .modal_item {
                display: flex;
                flex-direction: row;
                padding: 10px 0;
                border-bottom: 1px solid #bbb;

                .modal_item_squre {
                    width: 60px;
                    height: 60px;
                    background-color: #000096; // default #3300FF
                    border-radius: 50px;
                    border: 1px solid #826df3;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    color: #fff;
                    margin: 0 20px;
                    box-shadow: 5px 5px 10px 0px rgba(189, 189, 189, 1);
                    -webkit-box-shadow: 5px 5px 10px 0px rgba(189, 189, 189, 1);
                    -moz-box-shadow: 5px 5px 10px 0px rgba(189, 189, 189, 1);
                }

                .modal_item_text_container {
                    margin-right: 20px;
                    width: 380px;

                    .modal_item_text_header {
                        font-size: 16px;
                        color: $text_color;
                    }

                    .modal_item_text_a {
                        color: $second_text_color;
                        font-size: 14px;
                        margin-top: 6px;
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
        display: flex;
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
        overflow: hidden;
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
            font-family: $def_font;
            width: 100%;
        }

        .sorting option {
            font-family: $def_font;
            color: $second_text_color
        }

        .header_1 {
            font-size: 22px;
            padding-left: 16px  ;
            font-family: $def_font;
        }

        .header_2 {
            font-size: 18px;
            font-family: $def_font;
        }

        .h1 {
            border-bottom: 1px solid #dddddd;
            padding-top: 16px;
            padding-right: 24px;
            padding-bottom: 12px;
            padding-left: 24px;
            display: flex;
            flex-direction: row;

            img {
                width: 30px;
                height: 30px;
            }

            .filter_btn_show {
                position: absolute;
                right: 20px;

            }

            .filter_btn_show:hover {
                cursor: pointer;
            }
        }

        .h2 {
            padding-top: 8px;
        }

        .h3 {
        }

        .checkbox_text {
            font-family: $def_font;
            font-size: 14px;
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

    .filter_btn_show_left {
        position: absolute;
        background-color: #ffffff;
        border: 1px solid #bbb;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        right: 4px;
        top: 8px;
        width: 40px;
        height: 40px;

        img {
            width: 30px;
            height: 30px;
        }
    }

    .filter_btn_show_left:hover {
        cursor: pointer;
    }

</style>