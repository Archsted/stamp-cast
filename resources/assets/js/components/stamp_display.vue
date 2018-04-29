<template>
    <div class="stampAreaWrapper">
        <vue-draggable-resizable
            :w="300"
            :h="225"
            :minw="120"
            :minh="120"
            :x="74"
            :y="0"
            :z="areaZ"
            v-on:dragging="onAreaDrag"
            v-on:resizing="onAreaResize"
            :parent="true"
            :resizable="areaDisplay"
            :draggable="areaDisplay"
            :active="areaDisplay">
            <div class="stampArea" id="display" v-bind:style="stampAreaStyle">
                <div class="stampAreaInfoWrapper" v-bind:style="{ display: informationDisplay }">
                    <div class="stampAreaInfo stampAreaTop"><span>{{ width }}</span></div>
                    <div class="stampAreaInfo stampAreaRight"><span>{{ height }}</span></div>
                    <div class="stampAreaInfo stampAreaBottom"><span>{{ width }}</span></div>
                    <div class="stampAreaInfo stampAreaLeft"><span>{{ height }}</span></div>
                </div>
            </div>
        </vue-draggable-resizable>

        <vue-draggable-resizable
            :w="200"
            :h="200"
            :minw="120"
            :minh="120"
            :x="80"
            :y="0"
            :z="sizeZ"
            v-on:resizing="onSizeResize"
            :parent="true"
            :resizable="sizeDisplay"
            :draggable="sizeDisplay"
            :active="sizeDisplay">
            <div class="stampArea" v-bind:style="stampSizeStyle">

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
            <div id="stampAreaControl" class="unselectable">
                <button class="btn btn-primary"
                        title="スタンプ表示場所調整。枠内のどこかにランダムで表示。"
                        v-show="!minControlPanel"
                        v-on:mouseover="draggableSub = false"
                        v-on:mouseout="draggableSub = true"
                        @click="toggleDisplay"
                        v-bind:style="areaButtonStyle">
                    <i class="far fa-object-group"></i>
                </button>

                <button class="btn btn-primary"
                        title="スタンプ大きさ調整。この枠に収まる大きさまで比率を保ち縮小。"
                        v-show="!minControlPanel"
                        v-on:mouseover="draggableSub = false"
                        v-on:mouseout="draggableSub = true"
                        @click="toggleSizeDisplay"
                        v-bind:style="sizeButtonStyle">
                    <span class="glyphicon glyphicon-picture"></span>
                </button>

                <button class="btn btn-primary btn-xs"
                        title="コントロールパネルの表示サイズ切り替え"
                        v-on:mouseover="draggableSub = false"
                        v-on:mouseout="draggableSub = true"
                        @click="minControlPanel = !minControlPanel">
                    <span v-show="!minControlPanel"><i class="fas fa-window-minimize"></i></span>
                    <span v-show="minControlPanel"><i class="fas fa-window-maximize"></i></span>
                </button>

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
                            v-model="stampOpacity"/>
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
                            v-model="stampDelay"/>
                    </div>
                </div>
<!--
                <div>
                    <input type="file" @change="soundFileChange">
                </div>
-->

            </div>
        </vue-draggable-resizable>
    </div>
</template>

<script>
    import Vue from 'vue'

    import VueDraggableResizable from 'vue-draggable-resizable'
    Vue.component('vue-draggable-resizable', VueDraggableResizable)

    import vueSlider from 'vue-slider-component'

    export default {
        data: function () {
            return {
                areaDisplay: true,
                sizeDisplay: false,
                stampAreaStyle: this.getVisibleStyle(),
                stampSizeStyle: this.getInvisibleStyle(),
                informationDisplay: 'block',
                width: 0,
                height: 0,
                x: 0,
                y: 0,
                stampSizeWidth: 0,
                stampSizeHeight: 0,
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
                stampOpacity:1.0,
                stampVolume: 0.2,
                stampDelay: 3.5,
                audio: null,
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
            this.x = 100;
            this.y = 100;

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
                    this.$refs.controlPanel.width = 168;
                    this.$refs.controlPanel.height = 178;
                    this.$nextTick(() => {
                        this.refreshSliders();
                    });
                }
            },
        },
        methods: {
            onAreaResize: function (x, y, width, height) {
                this.x = x;
                this.y = y;
                this.width = width;
                this.height = height;
            },

            refreshSliders: function () {
                this.$refs.opacitySlider.refresh();
                this.$refs.volumeSlider.refresh();
                this.$refs.delaySlider.refresh();
            },

            onAreaDrag: function (x, y) {
                this.x = x;
                this.y = y;
            },

            onSizeResize: function (x, y, width, height) {
                this.stampSizeWidth = width
                this.stampSizeHeight = height
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
                    let size = this.calculateAspectRatioFit(stamp.width, stamp.height, this.stampSizeWidth, this.stampSizeHeight);
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
                return Math.floor(Math.random() * (this.height - offset));
            },

            getRandomLeft: function (offset) {
                return Math.floor(Math.random() * (this.width - offset));
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

    .stampAreaWrapper {
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

    .sliderControl {
        display: flex;
        justify-content: space-between;
    }

    .SliderWrapper {
        margin: 0 6px;
    }
</style>
