<template>
    <div class="stampAreaWrapper">
        <vue-draggable-resizable
            :w="400"
            :h="300"
            :minw="120"
            :minh="120"
            :x="100"
            :y="100"
            v-on:dragging="onDrag"
            v-on:resizing="onResize"
            :parent="true"
            :resizable="display"
            :draggable="display"
            :active="display">
            <div class="stampArea" id="display" v-bind:style="stampAreaStyle">
                <div class="stampAreaInfo" v-bind:style="{ display: informationDisplay }">
                    <div class="stampAreaCenter">X: {{ x }}<br>Y: {{ y }}</div>
                    <div class="stampAreaTop">{{ width }}</div>
                    <div class="stampAreaRight">{{ height }}</div>
                    <div class="stampAreaBottom">{{ width }}</div>
                    <div class="stampAreaLeft">{{ height }}</div>
                </div>
            </div>
            <button @click="toggleDisplay">領域表示 [{{ display }}]</button>
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
                stampAreaStyle: this.getVisibleStyle(),
                informationDisplay: 'block',
                width: 0,
                height: 0,
                x: 0,
                y: 0,
                counter: 0,
                displayEl: null,
            }
        },
        created: function () {
            this.x = 100;
            this.y = 100;
        },
        mounted: function () {
            this.displayEl = document.querySelector('#display');
        },
        watch: {
            display: function (newDisplay) {
                this.stampAreaStyle = newDisplay ? this.getVisibleStyle() : this.getInvisibleStyle();
                this.informationDisplay = newDisplay ? 'block' : 'none';
            }
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

            getInvisibleStyle: function () {
                return {
                    backgroundColor: 'rgba(255, 0, 0, 0.0)',
                    border: 'dashed 1px rgba(255, 0, 0, 0.0)',
                };
            },

            getVisibleStyle: function () {
                return {
                    backgroundColor: 'rgba(255, 0, 0, 0.4)',
                    border: 'dashed 1px rgba(255, 0, 0, 0.7)',
                };
            },

            testAdd: function () {
                let stamp = {
                    id: 1,
                    path: 'bg1.png'
                };

                this.addStamp(stamp);
            },
            toggleDisplay: function() {
                this.display = !this.display;
            },
            addStamp: function(stamp) {
                let img = new Image();

                img.onload = () => {
                    // クラス、スタイルの定義
                    img.classList.add('stamp');
                    img.style.top = this.getRandomTop(img.height) + 'px';
                    img.style.left = this.getRandomLeft(img.width) + 'px';

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

                img.src = stamp.path;
            },

            getRandomTop: function (offset) {
                return Math.floor(Math.random() * (this.height - offset));
            },

            getRandomLeft: function (offset) {
                return Math.floor(Math.random() * (this.width - offset));
            },
        }
    }
</script>

<style>
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

    .stampAreaTop {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        text-align: center;
    }

    .stampAreaRight {
        position: absolute;
        top: 50%;
        right: 4px;
        margin-top: -12px;
        text-align: right;
    }

    .stampAreaBottom {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        text-align: center;
    }

    .stampAreaLeft {
        position: absolute;
        top: 50%;
        left: 4px;
        margin-top: -12px;
        text-align: left;
    }

    .stampAreaCenter {
        position: absolute;
        top: 50%;
        right: 0;
        left: 0;
        margin-top: -24px;
        text-align: center;
    }
</style>