<template>
    <div id="stampAreaWrapper">
        <div class="stampArea" id="display"></div>
        <vue-draggable-resizable
            ref="stampSizeDr"
            :w="stampSize.w"
            :h="stampSize.h"
            :minw="minStampAreaW"
            :minh="minStampAreaH"
            :x="stampSize.x"
            :y="stampSize.y"
            :z="sizeZ"
            v-on:dragging="onSizeDrag"
            v-on:resizing="onSizeResize"
            v-on:dragstop="saveSettings"
            v-on:resizestop="saveSettings"
            :parent="true"
            :resizable="sizeDisplay"
            :draggable="sizeDisplay"
            :active="sizeDisplay">
            <div id="stampSize" class="stampArea" v-bind:style="stampSizeStyle">

            </div>
        </vue-draggable-resizable>

        <vue-draggable-resizable
            ref="controlPanel"
            :w="controlPanelWidth"
            :h="controlPanelHeight"
            :x="controlPanel.x"
            :y="controlPanel.y"
            :z="999"
            :parent="true"
            :resizable="false"
            v-on:dragstop="onControlDragStop"
            v-on:resizestop="onControlResizeStop"
            :draggable="draggableSub">
            <hsc-menu-style-white>
                <hsc-menu-context-menu>
                    <div id="stampAreaControlWrapper"
                         class="menuWrapper"
                         v-on:mouseover="isHoverControlPanel = true"
                         v-on:mouseout="isHoverControlPanel = false"
                         v-bind:style="controlPanelStyle"
                    >
                        <div id="stampAreaControl" class="unselectable">
                            <div class="buttonControl">
                                <button class="btn btn-primary"
                                        title="スタンプ大きさ調整。この枠に収まる大きさまで比率を保ち縮小。"
                                        v-show="!minControlPanel"
                                        v-on:mouseover="draggableSub = false"
                                        v-on:mouseout="draggableSub = true"
                                        @click="toggleSizeDisplay"
                                        v-bind:style="sizeButtonStyle">
                                    大きさ
                                </button>

                                <button class="btn btn-primary btn-xs"
                                        title="コントロールパネルの表示サイズ切り替え"
                                        v-on:mouseover="draggableSub = false"
                                        v-on:mouseout="draggableSub = true"
                                        @click="minControlPanel = !minControlPanel">
                                    <span v-show="!minControlPanel"><i class="fas fa-window-minimize"></i></span>
                                    <span v-show="minControlPanel"><i class="fas fa-window-maximize"></i></span>
                                </button>
                            </div>

                            <div class="sliderControl"
                                 v-show="!minControlPanel"
                                 v-on:mouseover="draggableSub = false"
                                 v-on:mouseout="draggableSub = true">
                                <i class="far fa-eye-slash fa-lg fa-fw"></i>

                                <div class="SliderWrapper">
                                    <vue-slider
                                        ref="opacitySlider"
                                        :min="0"
                                        :max="1"
                                        :interval="0.1"
                                        :width="100"
                                        tooltip="hover"
                                        :formatter="(v) => `${v * 100}%`"
                                        v-model="stampOpacity"
                                        v-on:drag-end="saveSettings"
                                    />
                                </div>

                                <i class="fas fa-eye fa-lg fa-fw"></i>
                            </div>

                            <div class="sliderControl"
                                 v-show="!minControlPanel"
                                 v-on:mouseover="draggableSub = false"
                                 v-on:mouseout="draggableSub = true">
                                <i class="fas fa-volume-off fa-lg fa-fw"></i>

                                <div class="SliderWrapper">
                                    <vue-slider
                                        ref="volumeSlider"
                                        :min="0"
                                        :max="1"
                                        :interval="0.1"
                                        :width="100"
                                        tooltip="hover"
                                        :formatter="(v) => `${v * 100}%`"
                                        v-model="stampVolume"
                                        v-on:drag-end="saveSettings"
                                    />
                                </div>

                                <i class="fas fa-volume-up fa-lg fa-fw"></i>
                            </div>

                            <div class="sliderControl"
                                 v-show="!minControlPanel"
                                 v-on:mouseover="draggableSub = false"
                                 v-on:mouseout="draggableSub = true">
                                <i class="fas fa-clock fa-lg fa-fw"></i>

                                <div class="SliderWrapper">
                                    <vue-slider
                                        ref="delaySlider"
                                        :min="0.5"
                                        :max="8"
                                        :interval="0.1"
                                        :width="122"
                                        tooltip="hover"
                                        formatter="{value}秒"
                                        v-model="stampDelay"
                                        v-on:drag-end="saveSettings"
                                    />
                                </div>
                            </div>

                            <div class="sliderControl"
                                 v-show="!minControlPanel">
                                表示
                                <select name="stampAnime"
                                        id="stampAnime"
                                        v-on:mouseover="draggableSub = false"
                                        v-on:mouseout="draggableSub = true"
                                        v-model.number="stampAnime"
                                        v-on:change="saveSettings"
                                >
                                    <option v-for="stampAnimeValue in stampAnimeList" v-bind:value="stampAnimeValue">{{stampAnimeValue}}</option>
                                </select>

                                音
                                <select name="stampSe"
                                        id="stampSe"
                                        v-on:mouseover="draggableSub = false"
                                        v-on:mouseout="draggableSub = true"
                                        v-model.number="stampSe"
                                        v-on:change="saveSettings"
                                >
                                    <option v-for="stampSeValue in stampSeList" v-bind:value="stampSeValue">{{stampSeValue}}</option>
                                </select>

                                <button class="btn btn-primary btn-xs"
                                        v-on:mouseover="draggableSub = false"
                                        v-on:mouseout="draggableSub = true"
                                        @click="addRandomStamp">
                                    テスト
                                </button>
                            </div>

                            <div class="sliderControl"
                                 v-show="!minControlPanel">
                                <input type="checkbox"
                                       v-model="isAutoHide"
                                       v-on:mouseover="draggableSub = false"
                                       v-on:mouseout="draggableSub = true"
                                       @click="toggleIsAutoHide"
                                       id="autoHideCheckbox"
                                ><label for="autoHideCheckbox" style="margin-left:5px;">操作パネルを自動的に隠す</label>
                            </div>

                            <!--
                                            <div>
                                                <input type="file" @change="soundFileChange">
                                            </div>
                            -->
                        </div>
                    </div>
                    <template slot="contextmenu">
                        <hsc-menu-item label="初期設定に戻す" @click="resetSettings" />
                    </template>
                </hsc-menu-context-menu>
            </hsc-menu-style-white>
        </vue-draggable-resizable>
    </div>
</template>

<script>
    import Vue from 'vue';

    import Storage from 'vue-ls';
    Vue.use(Storage, {
        namespace: 'broadcaster__', // key prefix
        name: 'ls', // name variable Vue.[ls] or this.[$ls],
        storage: 'local', // storage name session, local, memory
    });

    import qs from 'query-string';

    import VueDraggableResizable from 'vue-draggable-resizable';
    Vue.component('vue-draggable-resizable', VueDraggableResizable);

    import * as VueMenu from '@hscmap/vue-menu';
    Vue.use(VueMenu);

    import vueSlider from 'vue-slider-component'

    import util from '../util'

    export default {
        data: function () {
            return {
                sizeDisplay: false,
                stampSizeStyle: this.getInvisibleStyle(),

                uiType: 1,
                counter: 0,
                displayEl: null,
                draggableSub: true,
                isShow: true,
                isMute: false,
                minControlPanel: true,
                controlPanelWidth: 52, // min
//                controlPanelWidth: 168, // max
                controlPanelHeight: 52, // min
//                controlPanelHeight: 178, // max
                audio: null,

                stampAnime: 1,
                stampAnimeList: [1, 2],

                stampSe: 1,
                stampSeList: [1, 2],

                minStampAreaW: 120,
                minStampAreaH: 120,

                isAutoHide: false,
                isHoverControlPanel: false,

                // コントロールパネル設定値
                stampOpacity: 0.0,
                stampVolume: 0.0,
                stampDelay: 0.0,

                // コントロールパネル表示位置
                controlPanel: {
                    x: 0,
                    y: 0,
                },

                // スタンプ表示領域
                stampArea: {
                    w: 0,
                    h: 0,
                    x: 0,
                    y: 0,
                },

                // スタンプサイズ領域
                stampSize: {
                    w: 0,
                    h: 0,
                    x: 0,
                    y: 0,
                },
            }
        },
        computed: {
            sizeZ: function () {
                return this.sizeDisplay ? 2 : 0;
            },
            sizeButtonStyle: function () {
                return {
                    opacity: this.sizeDisplay ? 1 : 0.3
                };
            },
            autoHideButtonStyle: function () {
                return {
                    opacity: this.isAutoHide ? 1 : 0.3
                }
            },
            controlPanelStyle: function () {
                return {
                    opacity: (this.uiType !== 0 && (!this.isAutoHide || this.isHoverControlPanel)) ? 1 : 0
                }
            },
            delay: function () {
                return this.stampDelay * 1000;
            }
        },
        props: [
            'roomId'
        ],
        components: {
            vueSlider
        },
        created: function () {
            // 初期値設定読み込み
            this.setDefaultSettings();
            this.loadSettings();

            this.setSettingsFromParams();

            createjs.Sound.registerSound("/button16.mp3?rev=1", 'receiveStamp1');
            createjs.Sound.registerSound("/pop11_2.mp3?rev=1", 'receiveStamp2');

            // チャンネル接続
            echo.channel('room.' + this.roomId)
                .listen('StampEvent', (e) => {
                    if (this.isShow) {
                        this.addStamp(e.stamp);
                    }
                })
                .listen('TextStampEvent', (e) => {
                    if (this.isShow) {
                        this.addTextStamp(e.stamp);
                    }
                });
        },
        mounted: function () {
            this.displayEl = document.querySelector('#display');
        },
        watch: {
            sizeDisplay: function (newSizeDisplay) {
                this.stampSizeStyle = newSizeDisplay ? this.getVisibleStyle() : this.getInvisibleStyle();
            },
            minControlPanel: function (newValue) {
                if (newValue) {
                    this.$refs.controlPanel.width = 52;
                    this.$refs.controlPanel.height = 52;
                } else {
//                    this.$refs.controlPanel.width = 168;
                    this.$refs.controlPanel.width = 220;
                    this.$refs.controlPanel.height = 200;
                    this.$nextTick(() => {
                        this.refreshSliders();
                    });
                }
            },
        },
        methods: {

            setDefaultSettings: function () {
                this.stampOpacity = 1.0;
                this.stampVolume = 0.2;
                this.stampDelay = 3.5;

                this.stampArea.w = document.documentElement.clientWidth; // 全画面
                this.stampArea.h = document.documentElement.clientHeight; // 全画面
                this.stampArea.x = 0;
                this.stampArea.y = 0;

                this.stampSize.w = document.documentElement.clientWidth; // スタンプ表示エリア同等（＝全画面）
                this.stampSize.h = document.documentElement.clientHeight; // スタンプ表示エリア同等（＝全画面）
                this.stampSize.x = 174;
                this.stampSize.y = 0;

                this.controlPanel.x = 0;
                this.controlPanel.y = 0;

                this.stampAnime = 1;
                this.stampSe = 1;
            },

            setSettingsFromParams: function () {
                const params = qs.parse(location.search);

                if ('opacity' in params) {
                    if (util.isFloat(params.opacity)) {
                        const paramOpacity = Number(params.opacity);
                        if (paramOpacity >= 0 && paramOpacity <= 1) {
                            this.stampOpacity = paramOpacity;
                        }
                    }
                }

                if ('volume' in params) {
                    if (util.isFloat(params.volume)) {
                        const paramVolume = Number(params.volume);
                        if (paramVolume >= 0 && paramVolume <= 1) {
                            this.stampVolume = paramVolume;
                        }
                    }
                }

                if ('delay' in params) {
                    if (util.isFloat(params.delay)) {
                        const paramDelay = Number(params.delay);
                        if (paramDelay >= 0.5 && paramDelay <= 8) {
                            this.stampDelay = paramDelay;
                        }
                    }
                }

                if ('anime' in params) {
                    if (util.isNumber(params.anime)) {
                        const paramAnime = Number(params.anime);
                        if (this.stampAnimeList.includes(paramAnime)) {
                            this.stampAnime = paramAnime;
                        }
                    }
                }

                if ('se' in params) {
                    if (util.isNumber(params.se)) {
                        const paramSe = Number(params.se);
                        if (this.stampSeList.includes(paramSe)) {
                            this.stampSe = paramSe;
                        }
                    }
                }

                if ('w' in params) {
                    if (util.isNumber(params.w)) {
                        this.stampSize.w = Number(params.w);
                    }
                }

                if ('h' in params) {
                    if (util.isNumber(params.h)) {
                        this.stampSize.h = Number(params.h);
                    }
                }

                // UIタイプ 0は非表示
                // 現状は1種類しかないので0か1
                if ('ui' in params) {
                    if (util.isNumber(params.ui)) {
                        const paramUi = Number(params.ui);

                        if ([0, 1].includes(paramUi)) {
                            this.uiType = paramUi;
                        }
                    }
                }

            },

            loadSettings: function () {
                let data = JSON.parse(Vue.ls.get('setting', '{}'));

                // ホワイトリスト形式で設定値を上書き

                // スタンプ透過値
                if (data.hasOwnProperty('stampOpacity')) {
                    if (data.stampOpacity >= 0 && data.stampOpacity <= 1) {
                        this.stampOpacity = data.stampOpacity;
                    }
                }

                // スタンプ通知音量
                if (data.hasOwnProperty('stampVolume')) {
                    if (data.stampVolume >= 0 && data.stampVolume <= 1) {
                        this.stampVolume = data.stampVolume;
                    }
                }

                // スタンプ表示時間
                if (data.hasOwnProperty('stampDelay')) {
                    if (data.stampDelay >= 0.5 && data.stampDelay <= 8) {
                        this.stampDelay = data.stampDelay;
                    }
                }

                // スタンプサイズ
                if (data.hasOwnProperty('stampSize')) {
                    if (data.stampSize.hasOwnProperty('x') &&
                        data.stampSize.hasOwnProperty('y') &&
                        data.stampSize.hasOwnProperty('w') &&
                        data.stampSize.hasOwnProperty('h')) {

                        // 表示領域が全て画面内に収まる場合のみ
                        if (data.stampSize.w >= this.minStampAreaW && data.stampArea.h >= this.minStampAreaH &&
                            data.stampSize.x >= 0 && data.stampSize.y >= 0
                        ) {
                            // 反映
                            this.stampSize.w = data.stampSize.w;
                            this.stampSize.h = data.stampSize.h;
                            this.stampSize.x = data.stampSize.x;
                            this.stampSize.y = data.stampSize.y;
                        }
                    }
                }

                if (data.hasOwnProperty('controlPanel')) {
                    if (data.controlPanel.hasOwnProperty('x') &&
                        data.controlPanel.hasOwnProperty('y')) {

                        // 表示領域が全て画面内に収まる場合のみ
                        if (data.controlPanel.x >= 0 && data.controlPanel.y >= 0) {
                            // 反映
                            this.controlPanel.x = data.controlPanel.x;
                            this.controlPanel.y = data.controlPanel.y;
                        }
                    }
                }

                // スタンプ表示タイプ
                if (data.hasOwnProperty('stampAnime')) {
                    if (this.stampAnimeList.includes(data.stampAnime)) {
                        this.stampAnime = data.stampAnime;
                    }
                }

                // スタンプ音
                if (data.hasOwnProperty('stampSe')) {
                    if (this.stampSeList.includes(data.stampSe)) {
                        this.stampSe = data.stampSe;
                    }
                }

            },

            saveSettings: function () {
                let data = {
                    stampOpacity: this.stampOpacity,
                    stampVolume: this.stampVolume,
                    stampDelay: this.stampDelay,
                    stampArea: {
                        w: document.documentElement.clientWidth,
                        h: document.documentElement.clientHeight,
                        x: 0,
                        y: 0,
                    },
                    stampSize: {
                        w: this.stampSize.w,
                        h: this.stampSize.h,
                        x: this.stampSize.x,
                        y: this.stampSize.y,
                    },
                    controlPanel: {
                        x: this.controlPanel.x,
                        y: this.controlPanel.y,
                    },
                    stampAnime: this.stampAnime,
                    stampSe: this.stampSe,
                };

                Vue.ls.set('setting', JSON.stringify(data));
            },

            resetSettings: function () {
                this.setDefaultSettings();
                this.saveSettings();
                location.reload();
            },

            onSizeResize: function (x, y, width, height) {
                this.stampSize.x = x;
                this.stampSize.y = y;
                this.stampSize.w = width;
                this.stampSize.h = height;
            },

            refreshSliders: function () {
                this.$refs.opacitySlider.refresh();
                this.$refs.volumeSlider.refresh();
                this.$refs.delaySlider.refresh();
            },

            onSizeDrag: function (x, y) {
                this.stampSize.x = x;
                this.stampSize.y = y;
            },

            onControlDragStop: function (x, y) {
                this.controlPanel.x = x;
                this.controlPanel.y = y;

                this.saveSettings();

                this.refreshSliders();
            },
            onControlResizeStop: function (x, y, width, height) {
                this.refreshSliders();
            },

            getInvisibleStyle: function () {
                return {
                    backgroundColor: 'rgba(255, 0, 0, 0.0)',
                    border: 'dashed 1px rgba(255, 0, 0, 0.0)',
                    overflow: 'hidden',
                    zIndex: 0,
                };
            },

            getVisibleStyle: function () {
                return {
                    backgroundColor: 'rgba(255, 0, 0, 0.4)',
                    border: 'dashed 1px rgba(255, 0, 0, 0.7)',
                    overflow: 'hidden',
                    zIndex: 1,
                };
            },

            toggleSizeDisplay: function() {
                this.sizeDisplay = !this.sizeDisplay;
            },

            toggleIsAutoHide: function () {
                this.isAutoHide = !this.isAutoHide;
            },

            addRandomStamp: function() {
                switch(Math.floor(Math.random() * 5)) {
                    case 0:
                        this.addStamp({
                            'name': '/images/ex02.png',
                            'width': 380,
                            'height' : 300,
                        });
                        break;
                    case 1:
                        this.addStamp({
                            'name': '/images/ex03.png',
                            'width': 330,
                            'height' : 288,
                        });
                        break;
                    case 2:
                        this.addStamp({
                            'name': '/images/ex04.png',
                            'width': 1267,
                            'height' : 664,
                        });
                        break;
                    case 3:
                        this.addStamp({
                            'name': '/images/ex05.gif',
                            'width': 210,
                            'height' : 140,
                        });
                        break;
                    case 4:
                        this.addStamp({
                            'name': '/images/ex01.png',
                            'width': 320,
                            'height' : 320,
                        });
                        break;

                    default:
                        this.addStamp({
                            'name': '/images/ex01.png',
                            'width': 320,
                            'height' : 320,
                        });
                        break;
                }
            },

            addStamp: function(stamp) {
                let img = new Image();

                img.onload = () => {
                    // 領域を超えないようにサイズ調整
                    let size = this.calculateAspectRatioFit(stamp.width, stamp.height, this.stampSize.w, this.stampSize.h);
                    img.width = size.width;
                    img.height = size.height;

                    // クラス、スタイルの定義
                    img.classList.add('stamp');

                    let randomTop = this.getRandomTop(size.height);
                    let randomLeft = this.getRandomLeft(size.width);

                    img.style.top = `${randomTop}px`;
                    img.style.left = `${randomLeft}px`;

                    // 要素の追加
                    this.displayEl.appendChild(img);

                    // カウンターを増やす
                    this.counter++;

                    let basicTimeLine = animejs.timeline({
                        begin: () => {
                            if (!this.isMute) {
                                let soundInstance = createjs.Sound.createInstance(`receiveStamp${this.stampSe}`);
                                soundInstance.setVolume(this.stampVolume);
                                soundInstance.play();

                                //    this.audio.play();
                            }
                        },
                        complete: () => {
                            // タイムラインが全て終わったら自分自身を削除し、カウンターを減らす
                            this.displayEl.removeChild(img);
                            this.counter--;
                        }
                    });

                    // スタンプアニメーション
                    switch (this.stampAnime) {
                        case 1:
                            // グラブル風
                            basicTimeLine
                                .add({
                                    targets: img,
                                    scale: {
                                        value: [0.2, 1],
                                    },
                                    duration: 200,
                                    opacity: this.stampOpacity,
                                    easing: 'easeInOutSine'
                                })
                                .add({
                                    targets: img,
                                    scale: 0.5,
                                    duration: 500,
                                    opacity: 0,
                                    delay: this.delay,
                                    easing: 'easeInOutSine'
                                });

                            break;
                        case 2:
                            // デレステ風
                            basicTimeLine
                                .add({
                                    targets: img,
                                    height: {
                                        value: [`${size.height}px`, `${size.height + 40}px`],
                                    },
                                    top: {
                                        value: [`${randomTop}px`, `${randomTop - 60}px`],
                                    },
                                    duration: 150,
                                    opacity: this.stampOpacity,
                                    easing: 'easeOutExpo'
                                })
                                .add({
                                    targets: img,
                                    height: {
                                        value: [`${size.height + 40}px`, `${size.height}px`],
                                    },
                                    top: {
                                        value: [`${randomTop - 60}px`, `${randomTop}px`],
                                    },
                                    duration: 200,
                                    opacity: this.stampOpacity,
                                    easing: 'easeInQuad'
                                })
                                .add({
                                    targets: img,
                                    height: {
                                        value: [`${size.height}px`, `${size.height + 10}px`],
                                    },
                                    top: {
                                        value: [`${randomTop}px`, `${randomTop - 15}px`],
                                    },
                                    duration: 100,
                                    opacity: this.stampOpacity,
                                    easing: 'easeOutExpo'
                                })
                                .add({
                                    targets: img,
                                    height: {
                                        value: [`${size.height + 10}px`, `${size.height}px`],
                                    },
                                    top: {
                                        value: [`${randomTop - 15}px`, `${randomTop}px`],
                                    },
                                    duration: 150,
                                    opacity: this.stampOpacity,
                                    easing: 'easeInQuad'
                                })
                                .add({
                                    targets: img,
                                    translateY: -20,
                                    duration: 300,
                                    opacity: 0,
                                    delay: this.delay,
                                    easing: 'easeInOutSine'
                                });
                            break;

                        default:
                            // 一応指定
                            basicTimeLine
                                .add({
                                    targets: img,
                                    scale: {
                                        value: [0.2, 1],
                                    },
                                    duration: 200,
                                    opacity: this.stampOpacity,
                                    easing: 'easeInOutSine'
                                })
                                .add({
                                    targets: img,
                                    scale: 0.5,
                                    duration: 500,
                                    opacity: 0,
                                    delay: this.delay,
                                    easing: 'easeInOutSine'
                                });
                    }

                };

                img.src = stamp.name;
            },

            addTextStamp: function(imprint) {
                // console.log(imprint.comment, imprint.user)
            },

            getRandomTop: function (offset) {
                return Math.floor(Math.random() * (document.documentElement.clientHeight - offset));
            },

            getRandomLeft: function (offset) {
                return Math.floor(Math.random() * (document.documentElement.clientWidth - offset));
            },

            calculateAspectRatioFit: function (srcWidth, srcHeight, maxWidth, maxHeight) {
                // 画像の大きさが最大サイズに満たない場合はそのままのサイズを返す
                if (srcWidth <= maxWidth && srcHeight <= maxHeight) {
                    return {
                        width: srcWidth,
                        height: srcHeight
                    };
                } else {
                    // 比率を求める
                    let ratio = Math.min(maxWidth / srcWidth, maxHeight / srcHeight);

                    return {
                        width: srcWidth * ratio,
                        height: srcHeight * ratio
                    };
                }
            },

            /*
            soundFileChange: function (event) {
                let file = (window.URL || window.webkitURL).createObjectURL(event.target.files[0]);
                this.audio = new Audio(file);
                this.audio.volume = this.stampVolume;

                let fileReader = new FileReader();
                fileReader.onload = (ev) => {
                    localStorage.setItem("SE_stamp_received", ev.target.result);
                };
                fileReader.readAsDataURL(event.target.files[0]);
            },
            */

            /*
            changeVolume: function (volume) {
                // volumeはバインディングされているため、代入せずとも良い
                this.$nextTick(() => {
                   this.audio.volume = this.stampVolume;
                });
            }
            */
        }
    }
</script>

<style>
    .stampArea {
        width: 100%;
        height: 100%;
    }

    #stampAreaWrapper {
        position: absolute;
        top: 0;
        right: 0;
        bottom:0;
        left: 0;
        padding: 0;
        margin: 0;
    }

    .unselectable {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    #stampAreaControl {
        display: inline-flex;
        justify-content: space-around;
        align-content: space-around;
        flex-wrap: wrap;
        align-items: center;
        background-color:rgba(255, 255, 255, 1);
        width: 100%;
        height: 100%;
        /*padding: 15px 5px;*/
        padding: 0;
        box-sizing: border-box;
        border: solid 2px rgba(0, 0, 200, 1);
        border-radius: 6px;
        overflow: hidden;
    }

    #stampAreaControl button {
        font-size:1.4em;
    }

    img.stamp {
        position: absolute;
        opacity: 0;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }


    .handle {
        z-index: 1;
    }

    #stampAreaWrapper + .draggable + .handle {
        z-index: 2;
    }

    .handle-tl {
        top: 0 !important;
        left: 0 !important;
    }
    .handle-tm {
        top: 0 !important;
    }
    .handle-tr {
        top: 0 !important;
        right: 0 !important;
    }
    .handle-bl {
        bottom: 0 !important;
        left: 0 !important;
    }
    .handle-bm {
        bottom: 0 !important;
    }
    .handle-br {
        bottom: 0 !important;
        right: 0 !important;
    }
    .handle-ml {
        left: 0 !important;
    }
    .handle-mr {
        right: 0 !important;
    }

    .buttonControl {
        width: 100%;
        margin: 6px;
        display: flex;
        justify-content: space-between;
    }

    .buttonControl > button {
        font-size: 1em !important;
    }

    .sliderControl {
        margin: 6px 6px;
        display: flex;
        justify-content: space-between;
    }

    .sliderControl > button {
        font-size: 1em !important;
    }

    .SliderWrapper {
        margin: 0 6px;
    }

    #stampAreaWrapper .label {
        font-size: 100%;
        font-weight: normal;
        line-height: 1;
        color: inherit;
    }

    #stampAnime {
        margin-left: 4px;
        margin-right: 4px;
    }
    #stampSe {
        margin-left: 4px;
        margin-right: 12px;
    }

    #stampAreaControlWrapper {
        -webkit-transition: all .8s;
        -moz-transition: all .8s;
        -ms-transition: all .8s;
        -o-transition: all .8s;
        transition: all .8s;
    }

    .fixed {
        z-index: 10000;
    }
</style>
