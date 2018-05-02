<template>
    <div id="stampAreaWrapper">
        <vue-draggable-resizable
            ref="stampAreaDr"
            :w="stampArea.w"
            :h="stampArea.h"
            :minw="minStampAreaW"
            :minh="minStampAreaH"
            :x="stampArea.x"
            :y="stampArea.y"
            :z="areaZ"
            v-on:dragging="onAreaDrag"
            v-on:resizing="onAreaResize"
            v-on:dragstop="saveSettings"
            v-on:resizestop="saveSettings"
            :parent="true"
            :resizable="areaDisplay"
            :draggable="areaDisplay"
            :active="areaDisplay">
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
                <div class="stampArea" v-bind:style="stampSizeStyle">

                </div>
            </vue-draggable-resizable>
            <div class="stampArea" id="display" v-bind:style="stampAreaStyle">
                <div class="stampAreaInfoWrapper" v-bind:style="{ display: informationDisplay }">
                    <div class="stampAreaInfo stampAreaTop"><span>{{ stampArea.w }}</span></div>
                    <div class="stampAreaInfo stampAreaRight"><span>{{ stampArea.h }}</span></div>
                    <div class="stampAreaInfo stampAreaBottom"><span>{{ stampArea.w }}</span></div>
                    <div class="stampAreaInfo stampAreaLeft"><span>{{ stampArea.h }}</span></div>
                </div>
            </div>
        </vue-draggable-resizable>

        <vue-draggable-resizable
            ref="controlPanel"
            :w="controlPanelWidth"
            :h="controlPanelHeight"
            :x="0"
            :y="0"
            :z="999"
            :parent="true"
            :resizable="false"
            v-on:dragstop="onControlDragStop"
            v-on:resizestop="onControlResizeStop"
            :draggable="draggableSub">
            <hsc-menu-style-white>
                <hsc-menu-context-menu>
                    <div class="menuWrapper">
                        <div id="stampAreaControl" class="unselectable">
                            <div class="buttonControl">
                                <button class="btn btn-primary"
                                        title="スタンプ表示場所調整。枠内のどこかにランダムで表示。"
                                        v-show="!minControlPanel"
                                        v-on:mouseover="draggableSub = false"
                                        v-on:mouseout="draggableSub = true"
                                        @click="toggleDisplay"
                                        v-bind:style="areaButtonStyle">
                                    位置
                                </button>

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

    import VueDraggableResizable from 'vue-draggable-resizable';
    Vue.component('vue-draggable-resizable', VueDraggableResizable);

    import * as VueMenu from '@hscmap/vue-menu';
    Vue.use(VueMenu);

    import vueSlider from 'vue-slider-component'

    export default {
        data: function () {
            return {
                areaDisplay: true,
                sizeDisplay: false,
                stampAreaStyle: this.getVisibleStyle(),
                stampSizeStyle: this.getInvisibleStyle(),
                informationDisplay: 'block',

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

                minStampAreaW: 120,
                minStampAreaH: 120,

                // コントロールパネル設定値
                stampOpacity: 0.0,
                stampVolume: 0.0,
                stampDelay: 0.0,

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
            areaZ: function () {
                return this.areaDisplay ? 1 : 0;
            },
            sizeZ: function () {
                return this.sizeDisplay ? 2 : 0;
            },
            areaButtonStyle: function () {
                return {
                    opacity: this.areaDisplay ? 1 : 0.3
                };
            },
            sizeButtonStyle: function () {
                return {
                    opacity: this.sizeDisplay ? 1 : 0.3
                };
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

            createjs.Sound.registerSound("/button16.mp3?id=2", 'receiveStamp');

            /*
            let stampSe = localStorage.getItem("SE_stamp_received");
            if (stampSe) {
                this.audio = new Audio(stampSe);
                this.audio.volume = this.stampVolume;
            } else {
                let xhr = new XMLHttpRequest();
                xhr.open('GET', '/button16.mp3', true);
                xhr.responseType = 'blob';
                xhr.onload = (e) => {
                    let fileReader = new FileReader();

                    let file = (window.URL || window.webkitURL).createObjectURL(xhr.response);
                    this.audio = new Audio(file);
                    this.audio.volume = this.stampVolume;

                    fileReader.onload = (ev) => {
                        localStorage.setItem("SE_stamp_received", ev.target.result);
                    };
                    fileReader.readAsDataURL(xhr.response);
                };
                xhr.send();
            }
            */

            // チャンネル接続
            echo.channel('room.' + this.roomId)
                .listen('StampEvent', (e) => {
                    if (this.isShow) {
                        this.addStamp(e.stamp);
                    }
                });
        },
        mounted: function () {
            this.displayEl = document.querySelector('#display');
        },
        watch: {
            areaDisplay: function (newDisplay) {
                this.stampAreaStyle = newDisplay ? this.getVisibleStyle() : this.getInvisibleStyle();
                this.informationDisplay = newDisplay ? 'block' : 'none';
            },
            sizeDisplay: function (newSizeDisplay) {
                this.stampSizeStyle = newSizeDisplay ? this.getVisibleStyle() : this.getInvisibleStyle();
            },
            minControlPanel: function (newValue) {
                if (newValue) {
                    this.$refs.controlPanel.width = 52;
                    this.$refs.controlPanel.height = 52;
                } else {
//                    this.$refs.controlPanel.width = 168;
                    this.$refs.controlPanel.width = 180;
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

                this.stampArea.w = 300;
                this.stampArea.h = 225;
                this.stampArea.x = 74;
                this.stampArea.y = 0;

                this.stampSize.w = 140;
                this.stampSize.h = 140;
                this.stampSize.x = 174;
                this.stampSize.y = 0;
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

                // スタンプ表示領域
                if (data.hasOwnProperty('stampArea')) {
                    if (data.stampArea.hasOwnProperty('x') &&
                        data.stampArea.hasOwnProperty('y') &&
                        data.stampArea.hasOwnProperty('w') &&
                        data.stampArea.hasOwnProperty('h')) {

                        let wrapper = document.querySelector('#stampAreaWrapper');

                        if (data.stampArea.w >= this.minStampAreaW && data.stampArea.h >= this.minStampAreaH &&
                            data.stampArea.x >= 0 && data.stampArea.y >= 0
                        ) {
                            // 反映
                            this.stampArea.w = data.stampArea.w;
                            this.stampArea.h = data.stampArea.h;
                            this.stampArea.x = data.stampArea.x;
                            this.stampArea.y = data.stampArea.y;
                        }
                    }
                }

                if (data.hasOwnProperty('stampSize')) {
                    if (data.stampSize.hasOwnProperty('x') &&
                        data.stampSize.hasOwnProperty('y') &&
                        data.stampSize.hasOwnProperty('w') &&
                        data.stampSize.hasOwnProperty('h')) {

                        let wrapper = document.querySelector('#stampAreaWrapper');

                        // 表示領域が全て画面内に収まる場合のみ
                        if (data.stampSize.w >= this.minStampAreaW && data.stampArea.h >= this.minStampAreaH &&
                            data.stampSize.x >= 0 && data.stampSize.y >= 0
                        // &&
                        // data.stampSize.w + data.stampSize.x <=  wrapper.clientWidth &&
                        // data.stampSize.h + data.stampSize.y <= wrapper.clientHeight
                        ) {

                            // 反映
                            this.stampSize.w = data.stampSize.w;
                            this.stampSize.h = data.stampSize.h;
                            this.stampSize.x = data.stampSize.x;
                            this.stampSize.y = data.stampSize.y;
                        }
                    }
                }

            },

            saveSettings: function () {
                let data = {
                    stampOpacity: this.stampOpacity,
                    stampVolume: this.stampVolume,
                    stampDelay: this.stampDelay,
                    stampArea: {
                        w: this.stampArea.w,
                        h: this.stampArea.h,
                        x: this.stampArea.x,
                        y: this.stampArea.y,
                    },
                    stampSize: {
                        w: this.stampSize.w,
                        h: this.stampSize.h,
                        x: this.stampSize.x,
                        y: this.stampSize.y,
                    },
                };

                Vue.ls.set('setting', JSON.stringify(data));
            },

            resetSettings: function () {
                this.setDefaultSettings();
                this.saveSettings();
                location.reload();
            },

            onAreaResize: function (x, y, width, height) {
                this.stampArea.x = x;
                this.stampArea.y = y;
                this.stampArea.w = width;
                this.stampArea.h = height;
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

            onAreaDrag: function (x, y) {
                this.stampArea.x = x;
                this.stampArea.y = y;
            },

            onSizeDrag: function (x, y) {
                this.stampSize.x = x;
                this.stampSize.y = y;
            },

            onControlDragStop: function (x, y) {
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
                };
            },

            getVisibleStyle: function () {
                return {
                    backgroundColor: 'rgba(255, 0, 0, 0.4)',
                    border: 'dashed 1px rgba(255, 0, 0, 0.7)',
                    overflow: 'hidden',
                };
            },

            toggleDisplay: function() {
                this.areaDisplay = !this.areaDisplay;
            },

            toggleSizeDisplay: function() {
                this.sizeDisplay = !this.sizeDisplay;
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
                    img.style.top = this.getRandomTop(size.height) + 'px';
                    img.style.left = this.getRandomLeft(size.width) + 'px';

                    // 要素の追加
                    this.displayEl.appendChild(img);

                    // カウンターを増やす
                    this.counter++;

                    let basicTimeLine = animejs.timeline({
                        begin: () => {
                            if (!this.isMute) {
                                let soundInstance = createjs.Sound.createInstance('receiveStamp');
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

                    // スタンプのライムライン定義
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

                };

                img.src = stamp.name;
            },

            getRandomTop: function (offset) {
                return Math.floor(Math.random() * (this.stampArea.h - offset));
            },

            getRandomLeft: function (offset) {
                return Math.floor(Math.random() * (this.stampArea.w - offset));
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

    .stampAreaInfoWrapper {
        margin: 0;
        padding: 0;
    }

    .unselectable {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .stampAreaInfo {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .stampAreaInfo > span {
        background-color: #ffffff;
        color: #000000;
        padding: 3px 4px;
        border: solid 1px #555555;
    }

    .stampAreaTop {
        position: absolute;
        top: 20px;
        right: 0;
        left: 0;
        text-align: center;
    }
    .stampAreaTop:before{
        content: "";
        position: absolute;
        top: -12px;
        left: 50%;
        margin-left: -6px;
        border: 6px solid transparent;
        border-bottom: 6px solid #555555;
        z-index: 1;
    }
    .stampAreaTop:after{
        content: "";
        position: absolute;
        top: -10px;
        left: 50%;
        margin-left: -5px;
        border: 5px solid transparent;
        border-bottom: 5px solid #ffffff;
        z-index: 2;
    }

    .stampAreaRight {
        position: absolute;
        top: 50%;
        right: 20px;
        margin-top: -12px;
        text-align: right;
    }
    .stampAreaRight:before{
        content: "";
        position: absolute;
        right: -11px;
        top: 50%;
        margin-top: -6px;
        border: 6px solid transparent;
        border-left: 6px solid #555555;
        z-index: 1;
    }
    .stampAreaRight:after{
        content: "";
        position: absolute;
        right: -9px;
        top: 50%;
        margin-top: -5px;
        border: 5px solid transparent;
        border-left: 5px solid #ffffff;
        z-index: 2;
    }

    .stampAreaBottom {
        position: absolute;
        right: 0;
        bottom: 20px;
        left: 0;
        text-align: center;
    }
    .stampAreaBottom:before{
        content: "";
        position: absolute;
        bottom: -12px;
        left: 50%;
        margin-left: -6px;
        border: 6px solid transparent;
        border-top: 6px solid #555555;
        z-index: 1;
    }
    .stampAreaBottom:after{
        content: "";
        position: absolute;
        bottom: -10px;
        left: 50%;
        margin-left: -5px;
        border: 5px solid transparent;
        border-top: 5px solid #ffffff;
        z-index: 2;
    }

    .stampAreaLeft {
        position: absolute;
        top: 50%;
        left: 20px;
        margin-top: -12px;
        text-align: left;
    }
    .stampAreaLeft:before{
        content: "";
        position: absolute;
        left: -11px;
        top: 50%;
        margin-top: -6px;
        border: 6px solid transparent;
        border-right: 6px solid #555555;
        z-index: 1;
    }
    .stampAreaLeft:after{
        content: "";
        position: absolute;
        left: -9px;
        top: 50%;
        margin-top: -5px;
        border: 5px solid transparent;
        border-right: 5px solid #ffffff;
        z-index: 2;
    }

    #stampAreaControl {
        display: inline-flex;
        justify-content: space-around;
        align-content: space-around;
        flex-wrap: wrap;
        align-items: center;
        background-color:rgba(100, 100, 255, 0.2);
        width: 100%;
        height: 100%;
        /*padding: 15px 5px;*/
        padding: 0;
        box-sizing: border-box;
        border: solid 2px rgba(0, 0, 200, 0.7);
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
        margin: 10px 6px;
        display: flex;
        justify-content: space-between;
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

    .fixed {
        z-index: 10000;
    }
</style>
