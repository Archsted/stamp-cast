require('./bootstrap');

const app = new Vue({
    el: '#app',
    data: {
        stamps: [],
        active: true,
    },
    mounted: function () {
        this.getStamps();
    },
    methods: {
        getStamps: function () {
            // スタンプ一覧
            let url = '/api/v1/stamps/samples';

            axios.get(url).then((response) => {
                this.stamps = response.data.stamps;
                this.addRandomStamp();
            }).catch( error => { console.log(error); });
        },

        addRandomStamp: function () {
            if (this.active) {
                this.addStamp(this.stamps[Math.floor(Math.random() * this.stamps.length)]);
            }
            setTimeout( () => {
                this.addRandomStamp();
            }, Math.floor(Math.random() * 3000) + 2000);
        },

        addStamp: function(stamp) {
            let img = new Image();

            img.onload = () => {
                // 領域を超えないようにサイズ調整
                let size = this.calculateAspectRatioFit(stamp.width, stamp.height, 300, 200);
                img.width = size.width;
                img.height = size.height;

                // クラス、スタイルの定義
                img.classList.add('stamp');
                img.style.top = this.getRandomTop(size.height) + 'px';
                img.style.left = this.getRandomLeft(size.width) + 'px';

                // 要素の追加
                app.$el.appendChild(img);

                let basicTimeLine = animejs.timeline({
                    complete: () => {
                        app.$el.removeChild(img);
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
                        opacity: 0.2,
                        easing: 'easeInOutSine'
                    })
                    .add({
                        targets: img,
                        scale: 0.5,
                        duration: 500,
                        opacity: 0,
                        delay: 3500,
                        easing: 'easeInOutSine'
                    });

            };

            img.src = stamp.name;
        },

        getRandomTop: function (offset) {
            return Math.floor(Math.random() * (app.$el.clientHeight - offset));
        },

        getRandomLeft: function (offset) {
            return Math.floor(Math.random() * (app.$el.clientWidth - offset));
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
});

$(()=>{
    $(window).focus(() => {
        app.active = true;
    });
    $(window).blur(() => {
        app.active = false;
    });
});
