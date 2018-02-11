<template>
    <div class="litener">
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-warning" @click="resetStamps">
                    <i class="fas fa-redo fa-lg"></i>
                </button>
            </div>
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

            <div class="btn-group pull-right" role="group">
                <button type="button" class="btn btn-danger" @click="useInfinite = !useInfinite">{{ viewType }}</button>
            </div>

            <div class="pull-right" v-show="!useInfinite" style="padding-left:10px;">
                <select v-model="selectedCountPerPage" title="表示件数" class="form-control">
                    <option v-for="perPageOption in perPageOptions" v-bind:value="perPageOption.value">
                        {{ perPageOption.value }}
                    </option>
                </select>
            </div>

            <div class="pull-right" v-show="!useInfinite">
                <nav aria-label="Page navigation">
                    <paginate-links
                        for="paginateStamps"
                        :show-step-links="true"
                        class="pagination"
                    >
                    </paginate-links>
                </nav>
            </div>

        </div>

        <paginate
            name="paginateStamps"
            :list="stampList"
            :per="countPerPage"
        >
            <div class="stampList">
                <div class="stampForm" v-if="canUploadStamp">
                    <vue-dropzone
                        ref="myVueDropzone"
                        id="dropzone"
                        :options="dropzoneOptions"
                        v-on:vdropzone-success="uploadSuccessEvent"
                    />
                </div>

                <div v-for="(stamp, index) in paginated('paginateStamps')" :key="index"
                     class="stampWrapper" v-bind:style="cursor" @click="sendStamp(stamp.id)">
                    <div class="stampRippleWrapper" v-ripple>
                        <img class="stamp" :src="stamp.name">
                    </div>
                    <div class="favoriteForm" @click.stop="toggleFavorite(stamp.id)" v-if="!guest" v-bind:class="{containsFavorite: isContainsFavorite(stamp.id)}">
                        <span v-show="!isContainsFavorite(stamp.id)"><i class="far fa-heart fa-2x"></i></span>
                        <span v-show="isContainsFavorite(stamp.id)"><i class="fas fa-heart fa-2x"></i></span>
                    </div>
                    <div class="deleteForm" @click.stop="deleteStamp(stamp.id, stamp.user_id, stamp.name)" v-if="stamp.room_id && canDelete(stamp.user_id)">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>

                <infinite-loading @infinite="infiniteHandler" ref="infiniteLoading" v-if="useInfinite">
                    <span slot="no-results">
                        データがありません
                    </span>
                    <span slot="no-more"></span>
                </infinite-loading>
            </div>
        </paginate>

        <nav aria-label="Page navigation" class="text-center" v-show="!useInfinite">
            <paginate-links
                for="paginateStamps"
                :show-step-links="true"
                class="pagination"
            >
            </paginate-links>
        </nav>

    </div>
</template>

<script>
    import Vue from "vue"

    // 画像アップロード
    import vue2Dropzone from 'vue2-dropzone'

    // ダイアログ
    import VuejsDialog from "vuejs-dialog"
    Vue.use(VuejsDialog);

    // 無限スクロール
    import InfiniteLoading from 'vue-infinite-loading'

    // トースト
    import Toasted from 'vue-toasted'
    Vue.use(Toasted, {
        position: 'top-center',
        duration: 3000,
        iconPack: 'fontawesome'
    });

    // リップル
    import Ripple from 'vue-ripple-directive'
    Ripple.color = 'rgba(100, 213, 103, 0.5)';
    Vue.directive('ripple', Ripple);

    // ページネーション
    import VuePaginate from 'vue-paginate'
    Vue.use(VuePaginate);

    export default {
        data: function () {
            return {
                stamps: [],
                favorites: [],
                stampSort: 'all',
                onlyFavorite: false,
                useInfinite: true,
                paginate: ['paginateStamps'],
                selectedCountPerPage: 50,
                perPageOptions: [
                    {text: '10', value: 10},
                    {text: '20', value: 20},
                    {text: '30', value: 30},
                    {text: '40', value: 40},
                    {text: '50', value: 50},
                    {text: '60', value: 60},
                    {text: '70', value: 70},
                    {text: '80', value: 80},
                    {text: '90', value: 90},
                    {text: '100', value: 100},
                    {text: '200', value: 200},
                    {text: '500', value: 500},
                    {text: '1000', value: 1000},
                    {text: '2000', value: 2000},
                ],
                dropzoneOptions: {
                    url: (this.userId === null) ?
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
            if (!this.guest) {
                this.getFavorites();
            }
        },
        components: {
            vueDropzone: vue2Dropzone,
            InfiniteLoading,
        },
        computed: {
            stampList: function () {
                if (this.onlyFavorite) {
                    return this.stamps.filter(stamp => {
                        return this.favorites.includes(stamp.id);
                    });
                } else {
                    return this.stamps;
                }
            },
            /**
             * 未ログインユーザであるかどうか
             * @returns {boolean}
             */
            guest: function () {
                return this.userId === null;
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
            viewType: function () {
                return this.useInfinite ? '無限スクロール' : 'ページ表示';
            },
            countPerPage: function () {
                return this.useInfinite ? 9999999 : this.selectedCountPerPage;
            }
        },
        methods: {
            getStamps: function () {
                let url = '/api/v1/rooms/' + this.room.id + '/stamps';

                if (this.guest) {
                    url = url + '/guest';
                }

                axios.get(url,
                    {
                        params: {
                            sort: this.stampSort,
                            onlyFavorite: (this.onlyFavorite && this.useInfinite) ? 1 : 0,
                        },
                    })
                    .then(response => {
                        this.stamps = response.data.stamps;
                    })
                    .catch(error => {

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
                        this.$toasted.success('スタンプを送信しました。', {icon: 'comment'});
                    }).catch(error => {
                        this.$toasted.error('スタンプ送信に失敗しました。', {icon: 'exclamation-triangle'});
                    });
                } else {
                    // 送れない場合の説明文章

                }
            },
            uploadSuccessEvent: function (file, response) {
                this.$toasted.success('アップロードが完了しました。', {icon: 'upload'});
                this.stamps.unshift(response.stamp);
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
                    this.$toasted.error('お気に入りの読み込みに失敗しました。', {icon: 'exclamation-triangle'});
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
                            this.$toasted.success('お気に入りに追加しました。', {icon: 'heart'});
                        })
                        .catch(error => {
                            this.$toasted.error('お気に入りの追加に失敗しました。', {icon: 'exclamation-triangle'});
                        });
                } else {
                    // 削除
                    axios.delete('/api/v1/favorites', {
                        params: {
                            stamp_id: stampId
                        }
                    }).then(response => {
                        this.favorites.splice(favoriteIndex, 1);
                        this.$toasted.success('お気に入りを解除しました。', {icon: 'minus'});
                    }).catch(error => {
                        this.$toasted.error('お気に入りの削除に失敗しました。', {icon: 'exclamation-triangle'});
                    });
                }
            },
            deleteStamp: function (stampId, uploadedUserId, stampName) {
                let isComplete = false;

                this.$dialog.confirm(
                    '<div style="text-align: center"><p>このスタンプを削除しますか？</p><div class="deleteConfirmStamp"><div class="stampWrapper"><img src="' + stampName + '" class="stamp"></div></div></div>',
                    {
                        html: true,
                        loader: true,
                        okText: '削除する',
                        cancelText: 'キャンセル',
                        animation: 'fade',
                    })
                    .then((dialog) => {
                        axios.delete('/api/v1/stamps/' + stampId)
                            .then(response => {
                                isComplete = true;
                                this.resetStamps();
                                this.$toasted.success('スタンプを削除しました。', {icon: 'trash-alt'});
                            })
                            .catch(error => {
                                this.$toasted.error('スタンプの削除に失敗しました。', {icon: 'exclamation-triangle'});
                            })
                            .finally(() => {
                                dialog.close();

                                if (this.userId !== uploadedUserId && this.userId === this.room.userId && isComplete) {
                                    setTimeout(() => {
                                        this.$dialog.confirm(
                                            "今のスタンプを投稿したユーザーをブラックリストに入れますか？",
                                            {
                                                loader: true,
                                                okText: 'ブラックリストに入れる',
                                                cancelText: '何もしない',
                                                animation: 'fade',
                                            })
                                            .then((blDialog) => {
                                                axios.post('/api/v1/blackLists', {stamp_id: stampId})
                                                    .then(response => {
                                                        this.resetStamps();
                                                        this.$toasted.success('ブラックリストに追加しました。', {icon: 'ban'});
                                                    })
                                                    .catch(error => {
                                                        this.$toasted.error('ブラックリストへの追加に失敗しました。', {icon: 'exclamation-triangle'});
                                                    })
                                                    .finally(() => {
                                                        blDialog.close();
                                                    });
                                            })
                                            .catch(() => {
                                                // ここはブラックリストへの追加をキャンセルしたとき
                                            });
                                    }, 400);
                                }

                            });
                    })
                    .catch(() => {
                        // ここはスタンプの削除をキャンセルしたとき
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
            infiniteHandler: function ($state) {
                let url = '/api/v1/rooms/' + this.room.id + '/stamps';

                if (this.guest) {
                    url = url + '/guest';
                }

                let currentPage = Math.floor(this.stamps.length / 30) + 1;

                axios.get(url, {
                    params: {
                        page: currentPage,
                        sort: this.stampSort,
                        onlyFavorite: this.onlyFavorite ? 1 : 0,
                    },
                }).then(({ data }) => {
                    if (data.stamps.length) {
                        this.stamps = this.stamps.concat(data.stamps);
                        $state.loaded();
                        if (data.stamps.length < 30) {
                            $state.complete();
                        }
                    } else {
                        $state.complete();
                    }
                }).catch(error => {
                    this.$toasted.error('スタンプの読み込みに失敗しました。現在のページ番号: ' + currentPage, {icon: 'exclamation-triangle'});
                });
            },
            resetStamps: function () {
                if (this.useInfinite) {
                    this.stamps = [];
                    this.$nextTick(() => {
                        this.$refs.infiniteLoading.$emit('$InfiniteLoading:reset');
                    });
                } else {
                    this.getStamps();
                }
            }
        },
        watch: {
            stampSort: function (newValue) {
                this.resetStamps();
            },
            useInfinite: function (newValue) {
                this.resetStamps();
            },
            onlyFavorite: function (newValue) {
                // 無限スクロールの時のみ
                if (this.useInfinite) {
                    this.resetStamps();
                }
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

    .deleteConfirmStamp {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
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

    .stampWrapper:hover div {
        opacity: 1;
    }

    .toasted svg {
        margin-right: 10px;
    }

    .paginateStamps {
        margin: 0;
    }
</style>