<template>
    <div class="stampList">
        <div class="stampUserWrapper" v-for="(imprintByUser, userKey) in imprints" :key="'key' + userKey">

            <div class="stampUserContainer">
                <p><button type="button" class="btn btn-danger" @click="addBlackList(imprintByUser[0].id)">これらのスタンプ送信者をブラックリストに追加する</button></p>

                <div class="stampUser">
                    <div v-for="(imprint, index) in imprintByUser" :key="index" class="stampWrapper"
                         @mouseover="hoverStampId = imprint.stamp.id" @mouseleave="hoverStampId = null">
                        <div class="stampRippleWrapper">
                            <div class="animationFlagWrapper" v-if="imprint.stamp.is_animation">
                                <div class="animationFlag">
                                    <i class="fas fa-film fa-3x" style="color: #f00"></i>
                                </div>
                            </div>
                            <img class="stamp" :src="(imprint.stamp.is_animation && hoverStampId === imprint.stamp.id) ?
                     imprint.stamp.name : (imprint.stamp.thumbnail ? imprint.stamp.thumbnail : imprint.stamp.name)">
                        </div>
                        <div class="stampCount">
                            x {{ imprint.count }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
 </template>

<script>
    import Vue from "vue"

    // ダイアログ
    import VuejsDialog from "vuejs-dialog"
    Vue.use(VuejsDialog);

    export default {
        data: function () {
            return {
                hoverStampId: null,
            }
        },
        props: [
            'imprints',
        ],
        methods: {
            addBlackList: function (imprintId) {
                this.$dialog.confirm(
                    '<div style="text-align: center"><p>これらのスタンプを送信したユーザを<br>ブラックリストに追加しますか？</p></div>',
                    {
                        html: true,
                        okText: '削除する',
                        cancelText: 'キャンセル',
                        animation: 'fade',
                    })
                    .then(dialog => {
                        let url = '/api/v1/blackLists/imprints/' + imprintId;

                        axios.post(url)
                            .then( response => {
                                alert('追加しました。')
                            })
                            .catch( error => {

                            });
                    })
                    .catch(error => {
                        // キャンセル時
                    });

            },
        }
    }
</script>

<style>

    .stampList {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        min-height: 140px;
    }

    .stampUser {
        display: flex;
        flex-wrap: wrap;
    }

    .deleteConfirmStamp {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .stampUserContainer {
        border: solid 1px #666;
        padding: 7px;
        margin-bottom: 20px;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
    }

    .stampWrapper {
        position: relative;
        margin-left: 3px;
        margin-right: 3px;
        margin-bottom: 6px;
        height: 140px;
        min-width: 90px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;

        border: solid 2px #888;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;

        /* 市松模様 */
        background-color: #f9f9f9;
        -webkit-background-size: 30px 30px;
        -moz-background-size: 30px 30px;
        background-size: 30px 30px;
        background-position: 0 0, 15px 15px;
        background-image: -webkit-linear-gradient(45deg,  #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%),
        -webkit-linear-gradient(-135deg, #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%);
        background-image: -moz-linear-gradient(45deg,  #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%),
        -moz-linear-gradient(-135deg, #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%);
        background-image: -ms-linear-gradient(45deg,  #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%),
        -ms-linear-gradient(-135deg, #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%);
        background-image: -o-linear-gradient(45deg,  #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%),
        -o-linear-gradient(-135deg, #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%);
        background-image: linear-gradient(45deg,  #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%),
        linear-gradient(-135deg, #ddd 25%, #ddd 25%, transparent 25%, transparent 75%, #ddd 75%, #ddd 75%);
    }

    .stampRippleWrapper {
        position: relative;
        height: 100%;
        text-align: center;
    }

    .stamp {
        height: 100% !important;

        /*border: solid 2px #888;*/
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .favoriteForm {
        opacity: 0;
        transition: .4s ease;
        position: absolute;
        right: 4px;
        top: 4px;
        text-align: right;
        color: hotpink;
    }

    .containsFavorite {
        opacity: 1 !important;
    }

    .stampCount {
        transition: .5s ease;
        position: absolute;
        left: 0;
        top: 0;
        text-align: left;
        background-color: #fff;
        color: #ff4c4c;
        font-size: 150%;
        font-weight: bold;
        padding-right: 8px;
        padding-left: 8px;
        border-right: solid 2px #555;
        border-bottom: solid 2px #555;
        -webkit-border-radius: 10px 0 6px 0;
        -moz-border-radius: 10px 0 6px 0;
        border-radius: 10px 0 6px 0;
    }

    .deleteForm {
        opacity: 0;
        transition: .5s ease;
        position: absolute;
        right: 6px;
        bottom: 4px;
        text-align: right;
        color: #ff4c4c;
    }

    .stampForm {
        background-color: #FFA;
        box-sizing: border-box;
        border: dashed 4px #555;
        height: 140px;
        width: 140px;
        text-align: center;
        overflow: hidden;
        font-size: 0.8em;
        padding-top: 25px;
        cursor: pointer;
    }

    .stampWrapper:hover > div {
        opacity: 1;
    }
    .stampWrapper:hover .animationFlagWrapper {
        opacity: 0;
    }

    .toasted svg {
        margin-right: 10px;
    }

    .animationFlagWrapper {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .animationFlag {
        background-color: rgba(255, 255, 255, 0.7);
        box-sizing: border-box;
        border: solid 2px rgba(255, 0, 0, 0.5);
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        padding: 4px 13px;
    }

</style>