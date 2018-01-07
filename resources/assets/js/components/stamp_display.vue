<template>
    <div class="stampAreaWrapper">
        <vue-draggable-resizable
            :w="400"
            :h="300"
            :minw="120"
            :minh="120"
            :x="100"
            :y="100"
            :z="areaZ"
            v-on:dragging="onDrag"
            v-on:resizing="onResize"
            :parent="true"
            :resizable="display"
            :draggable="display"
            :active="display">
            <div class="stampArea" id="display" v-bind:style="stampAreaStyle">
                <div class="stampAreaInfo" v-bind:style="{ display: informationDisplay }">
                    <!--
                    <div class="stampAreaCenter"><span>X: {{ x }} / Y: {{ y }}</span></div>
                    -->
                    <div class="stampAreaTop"><span>{{ width }}</span></div>
                    <div class="stampAreaRight"><span>{{ height }}</span></div>
                    <div class="stampAreaBottom"><span>{{ width }}</span></div>
                    <div class="stampAreaLeft"><span>{{ height }}</span></div>
                </div>
            </div>
        </vue-draggable-resizable>

        <vue-draggable-resizable
            :w="200"
            :h="200"
            :minw="120"
            :minh="120"
            :x="150"
            :y="150"
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
            :w="140"
            :h="80"
            :x="100"
            :y="410"
            :parent="true"
            :resizable="false"
            :draggable="draggableSub">
            <div id="stampAreaControl">
                <button class="btn btn-primary"
                        v-on:mouseover="draggableSub = false"
                        v-on:mouseout="draggableSub = true"
                        @click="toggleDisplay"
                        v-bind:style="areaButtonStyle">
                    <span class="glyphicon glyphicon-modal-window"></span>
                </button>

                <button class="btn btn-primary"
                        v-on:mouseover="draggableSub = false"
                        v-on:mouseout="draggableSub = true"
                        @click="toggleSizeDisplay"
                        v-bind:style="sizeButtonStyle">
                    <span class="glyphicon glyphicon-picture"></span>
                </button>
            </div>
        </vue-draggable-resizable>
    </div>
</template>

<script>
    import Vue from 'vue'
    import VueDraggableResizable from 'vue-draggable-resizable'

    Vue.component('vue-draggable-resizable', VueDraggableResizable)

    export default {
        data: function () {
            return {
                display: true,
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
            }
        },
        computed: {
            areaZ: function () {
                return this.display ? 1 : 0;
            },
            sizeZ: function () {
                return this.sizeDisplay ? 2 : 0;
            },
            areaButtonStyle: function () {
                return {
                    opacity: this.display ? 1 : 0.3
                };
            },
            sizeButtonStyle: function () {
                return {
                    opacity: this.sizeDisplay ? 1 : 0.3
                };
            },
        },
        props: [
            'roomId'
        ],
        created: function () {
            this.x = 100;
            this.y = 100;

            // チャンネル接続
            echo.channel('room.' + this.roomId)
                .listen('StampEvent', (e) => {
                    this.addStamp(e.stamp);
                });
        },
        mounted: function () {
            this.displayEl = document.querySelector('#display');
        },
        watch: {
            display: function (newDisplay) {
                this.stampAreaStyle = newDisplay ? this.getVisibleStyle() : this.getInvisibleStyle();
                this.informationDisplay = newDisplay ? 'block' : 'none';
            },
            sizeDisplay: function (newSizeDisplay) {
                this.stampSizeStyle = newSizeDisplay ? this.getVisibleStyle() : this.getInvisibleStyle();
            },
        },
        methods: {
            onResize: function (x, y, width, height) {
                this.x = x
                this.y = y
                this.width = width
                this.height = height
            },

            onDrag: function (x, y) {
                this.x = x
                this.y = y
            },

            onSizeResize: function (x, y, width, height) {
                this.stampSizeWidth = width
                this.stampSizeHeight = height
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
                this.display = !this.display;
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
                            opacity: 1.0,
                            easing: 'easeInOutSine'
                        })
                        .add({
                            targets: img,
                            scale: 0.5,
                            duration: 500,
                            opacity: 0,
                            delay: 3000,
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

    .stampAreaInfo {
        margin: 0;
        padding: 0;
    }

    .stampAreaInfo > div > span {
        background-color: #ffffff;
        color: #000000;
        padding: 3px 4px;
        border: solid 1px #555555;
    }

    .stampAreaTop {
        position: absolute;
        top: 10px;
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
        right: 10px;
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
        bottom: 10px;
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
        left: 10px;
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

    .stampAreaCenter {
        position: absolute;
        top: 50%;
        right: 0;
        left: 0;
        margin-top: -12px;
        text-align: center;
    }

    #stampAreaControl {
        display: inline-flex;
        justify-content: space-around;
        align-items: center;
        background-color:rgba(100, 100, 255, 0.2);
        width: 100%;
        height: 100%;
        padding: 15px 5px;
        box-sizing: border-box;
        border: solid 2px rgba(0, 0, 200, 0.7);
        border-radius: 6px;
    }

    #stampAreaControl button {
        font-size:1.4em;
    }

</style>