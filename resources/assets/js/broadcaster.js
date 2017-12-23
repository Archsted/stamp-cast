require('./bootstrap');

const app = new Vue({
    el: '#app',
    data: {
        counter: 0,
    },
    created: function () {
        echo.channel('general')
            .listen('StampEvent', (e) => {
                // console.log(e);
                this.add();
            });
    },
    methods: {
        add: function () {
            let stamp = {
                id: 1,
                path: 'bg1.png'
            };

            this.addStamp(stamp);
        },
        addStamp: function(stamp) {
            let img = new Image();

            img.onload = () => {
                // クラス、スタイルの定義
                img.classList.add('stamp');
                img.style.top = this.getRandomTop(img.height) + 'px';
                img.style.left = this.getRandomLeft(img.width) + 'px';

                // 要素の追加
                document.body.appendChild(img);

                // カウンターを増やす
                this.counter++;

                let basicTimeLine = animejs.timeline({
                    complete: () => {
                        // タイムラインが全て終わったら自分自身を削除し、カウンターを減らす
                        document.body.removeChild(img);
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
            return Math.floor(Math.random() * (document.documentElement.clientHeight - offset));
        },

        getRandomLeft: function (offset) {
            return Math.floor(Math.random() * (document.body.clientWidth - offset));
        },

        sendStamp: function () {
            axios.get('/doStamp')
                .then(function (response) {
                    console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    },
});

