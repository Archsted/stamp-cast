<template>
    <div class="litener">
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group" role="group">
                <button type="button" class="btn" v-bind:class="stampSortClass('all')" @click="stampSort = 'all'">全て</button>
                <button type="button" class="btn" v-bind:class="stampSortClass('latest')" @click="stampSort = 'latest'">送信された順</button>
                <button type="button" class="btn" v-bind:class="stampSortClass('count')" @click="stampSort = 'count'">回数順</button>
            </div>
            <div class="btn-group" role="group" v-if="!guest">
                <button type="button" class="btn" v-bind:class="onlyFavoriteButtonClass" @click="onlyFavorite = !onlyFavorite">
                    <span style="color: hotpink;"><i class="fas fa-heart fa-lg"></i></span> お気に入りのみ表示
                </button>
            </div>
        </div>


        <div class="stampList">
            <div class="stampForm" v-if="canUploadStamp">
                <vue-dropzone
                    ref="myVueDropzone"
                    id="dropzone"
                    :options="dropzoneOptions"
                    v-on:vdropzone-complete="uploadCompleteEvent"
                />
            </div>

            <div v-for="stamp in stampList" :key="stamp.id" class="stampWrapper" v-bind:style="cursor">
                <img class="stamp" :src="stamp.name" @click="sendStamp(stamp.id)">
                <div class="favoriteForm" @click="toggleFavorite(stamp.id)" v-if="!guest">
                    <span v-show="!isContainsFavorite(stamp.id)"><i class="far fa-heart fa-2x"></i></span>
                    <span v-show="isContainsFavorite(stamp.id)"><i class="fas fa-heart fa-2x"></i></span>
                </div>
                <div class="deleteForm" @click="deleteStamp(stamp.id)" v-if="stamp.room_id && canDelete(stamp.user_id)">
                    <i class="fas fa-trash-alt"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import vue2Dropzone from 'vue2-dropzone'
//    import 'vue2-dropzone/dist/vue2Dropzone.css'

    // import into project
    import Vue from "vue"
    import VuejsDialog from "vuejs-dialog"

    // Tell Vue to install the plugin.
    Vue.use(VuejsDialog);

    export default {
        data: function () {
            return {
                stamps: [],
                favorites: [],
                stampSort: 'all',
                onlyFavorite: false,
                dropzoneOptions: {
                    url: (this.guest) ?
                        '/api/v1/rooms/' + this.room.id + '/stamps/guest' :
                        '/api/v1/rooms/' + this.room.id + '/stamps',
                    createImageThumbnails: false,
                    withCredentials: true,
                    headers: {
                        'X-CSRF-TOKEN': window.axios.defaults.headers.common['X-CSRF-TOKEN']
                    },
                    dictDefaultMessage: '<p>好きな画像をアップ</p><p><i class="fas fa-upload fa-2x" style="color:#000;"></i></p><p>この枠内にD&Dでも可</p>'
                },
            };
        },
        props: {
            room: Object,
            imprinterLevel: Number,
            uploaderLevel: Number,
            userId: Number,
        },
        created: function () {
            this.getStamps();
            if (!this.guest) {
                this.getFavorites();
            }
        },
        components: {
            vueDropzone: vue2Dropzone
        },
        computed: {
            /**
             * 未ログインユーザであるかどうか
             * @returns {boolean}
             */
            guest: function () {
                return this.userId === null;
            },
            stampList: function () {
                if (this.onlyFavorite) {
                    return this.stamps.filter(stamp => {
                        return this.favorites.includes(stamp.id);
                    });
                } else {
                    return this.stamps;
                }
            },
            canUploadStamp: function () {
                return (this.uploaderLevel === 1 || (this.uploaderLevel === 2 && !this.guest));
            },
            canSendStamp: function () {
                return (this.imprinterLevel === 1 || (this.imprinterLevel === 2 && !this.guest));
            },
            cursor: function () {
                return this.canSendStamp ? {
                    cursor: 'pointer'
                } : {
                    cursor: 'not-allowed'
                };
            },
            onlyFavoriteButtonClass: function () {
                return this.onlyFavorite ? 'btn-primary' : 'btn-default';
            },
        },
        methods: {
            getStamps: function () {
                // スタンプ一覧
                let url = '/api/v1/rooms/' + this.room.id + '/stamps';

                axios.get(url, {
                    params: {
                        sort: this.stampSort
                    }
                }).then(response => {
                    this.stamps = response.data.stamps;
                }).catch(error => {
                    console.log(error);
                });
            },
            sendStamp: function (stamp_id) {
                if (this.canSendStamp) {
                    // スタンプ送信
                    let url;

                    if (this.guest) {
                        url = '/api/v1/rooms/' + this.room.id + '/imprints/guest';
                    } else {
                        url = '/api/v1/rooms/' + this.room.id + '/imprints';
                    }

                    axios.post(url, {
                        stamp_id: stamp_id
                    }).then(response => {

                    }).catch(error => {
                        console.log(error);
                    });
                } else {
                    // 送れない場合の説明文章

                }
            },
            uploadCompleteEvent: function (file) {
                this.getStamps();
            },
            getFavorites: function () {
                // お気に入り一覧
                let url = '/api/v1/favorites';

                axios.get(url, {
                    params: {
                        room_id: this.room.id
                    }
                }).then(response => {
                    this.favorites = response.data.favorites.map(favorite => {
                        return favorite.id;
                    });
                }).catch(error => {
                    console.log(error);
                });
            },
            stampSortClass: function (sortType) {
                return this.stampSort === sortType ? 'btn-primary' : 'btn-default';
            },
            isContainsFavorite: function (stampId) {
                let favoriteIndex = this.favorites.findIndex((el, index) => {
                    return stampId === el;
                });

                return favoriteIndex >= 0;
            },
            toggleFavorite: function (stampId) {
                // 現在のお気に入りに含んでいるか確認、含んでいた場合はそのインデックスを取得
                let favoriteIndex = this.favorites.findIndex((el) => {
                    return stampId === el;
                });

                if (favoriteIndex === -1) {
                    // 追加
                    axios.post('/api/v1/favorites', {stamp_id: stampId})
                        .then(response => {
                            this.favorites.push(stampId);
                        })
                        .catch(error => {
                            console.log(error);
                        });
                } else {
                    // 削除
                    axios.delete('/api/v1/favorites', {
                        params: {
                            stamp_id: stampId
                        }
                    }).then(response => {
                        this.favorites.splice(favoriteIndex, 1);
                    }).catch(error => {
                        console.log(error);
                    });
                }
            },
            deleteStamp: function (stampId) {
                this.$dialog.confirm(
                    "このスタンプを削除しますか？",
                    {
                        loader: true,
                        okText: '削除する',
                        cancelText: 'キャンセル',
                        animation: 'fade',
                    })
                    .then((dialog) => {

                        axios.delete('/api/v1/stamps/' + stampId)
                            .then(response => {
                                this.getStamps();
                            })
                            .catch(error => {

                            })
                            .finally(() => {
                                dialog.close();
                            });
                    })
                    .catch(() => {
                        console.log('Delete aborted');
                    });
            },
            /**
             * スタンプを削除可能か判定
             *
             * @param userId スタンプ投稿ユーザID
             * @returns {boolean}
             */
            canDelete: function (userId) {
                return !this.guest && (this.userId === this.room.userId || this.userId === userId);
            },
        },
        watch: {
            stampSort: function (newValue) {
                this.getStamps();
            },
        }
    }
</script>

<style>
    .btn-group {
        margin-bottom: 12px;
    }

    .stampList {
        display: flex;
        flex-wrap: wrap;
    }

    .stampWrapper {
        position: relative;
        margin-left: 3px;
        margin-right: 3px;
        margin-bottom: 6px;
        height: 140px;
        min-width: 50px;
        box-sizing: border-box;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .stamp {
        height: 100% !important;
        border: solid 2px #888;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .favoriteForm {
        position: absolute;
        right: 4px;
        top: 4px;
        text-align: right;
        color: hotpink;
    }

    .deleteForm {
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
</style>