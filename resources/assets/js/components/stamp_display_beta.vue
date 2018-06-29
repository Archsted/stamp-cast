<template>
    <div id="canvas"></div>
</template>

<script>
    import Vue from 'vue';

    import * as PIXI from 'pixi.js'

    export default {
        data: function () {
            return {

            }
        },
        computed: {

        },
        props: [

        ],
        components: {

        },
        created: function () {

        },
        mounted: function () {

            const app = new PIXI.Application({
                transparent: true,
                width: window.innerWidth,
                height: window.innerHeight,
            });


            document.getElementById('canvas').appendChild(app.view);

            PIXI.loader.add('sta', '/storage/stamps/AlhcfTSsHcvRqWgFe5jRQDVrIVquat0Q3YBgYWBY.gif').load((loader, resources) => {
                const sta = new PIXI.Sprite(resources.sta.texture);

                sta.x = app.renderer.width / 2;
                sta.y = app.renderer.height / 2;

                sta.anchor.x = 0.5;
                sta.anchor.y = 0.5;

                app.stage.addChild(sta);

                let mask = new PIXI.Graphics();
                mask.x = app.renderer.width / 2;
                mask.y = app.renderer.height / 2;
                mask.beginFill(0xffffff);
                mask.drawCircle(50, 50, 50);
                mask.endFill();
                app.stage.addChild(mask);

//                sta.mask = mask;

                app.ticker.add(() => {
                    sta.rotation += 0.01;
                });
            });

            window.onresize = () => {
                app.renderer.view.width = window.innerWidth;
                app.renderer.view.height = window.innerHeight;
            };

            /*

            var stage = new PIXI.Stage(0xFFFFFF, true);

            var bg = PIXI.Sprite.fromImage("/storage/stamps/ro8rHoAq0jZltl3lejJLiMUdEIaMLSiD6SPqIP2q.png");
            bg.anchor.x = 0.5;
            bg.anchor.y = 0.5;

            bg.position.x = 620 / 2;
            bg.position.y = 380 / 2;

            stage.addChild(bg);

            // create a renderer instance
            var renderer = PIXI.autoDetectRenderer(620, 380);

            renderer.view.style.position = "absolute";
            renderer.view.style.marginLeft = "-310px";
            renderer.view.style.marginTop = "-190px";
            renderer.view.style.top = "50%";
            renderer.view.style.left = "50%";
            renderer.view.style.display = "block";

            // add render view to DOM
            document.getElementById('canvas').appendChild(renderer.view);

            requestAnimationFrame(animate);


            function animate() {
                bg.rotation += 0.01;
                renderer.render(stage);
                requestAnimationFrame(animate);
            }
            */
        },
        watch: {

        },
        methods: {

        }
    }
</script>

<style>

</style>
