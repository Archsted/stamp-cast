<template>
    <div class="stampListWrapper">
        <dialogs-wrapper tag="div" transition-name="fade"></dialogs-wrapper>

        <div class="sidebar" v-show="showSideBar">
            <div class="pull-right">
                <button class="btn btn-default" style="margin-right: 10px;" @click="toggleShowSideBar">
                    <i class="fas fa-angle-double-left fa-lg"></i> 閉じる
                </button>
            </div>

            <h3>タグ一覧</h3>

            <div class="tagList">
                <ul>
                    <li><a class="tag" href="#list" @click="setSearchNoTag">（タグが設定されていないもの） </a></li>
                    <li v-for="tag in allTags">
                        <a class="tag" href="#list" @click="setSearchTag(tag.text)">{{ tag.text }} <span class="badge">{{ tag.count }}</span></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="main" id="list">
            <div class="pull-left" v-show="!showSideBar" style="position:relative; left:-12px;">
                <span style="line-height:36px; cursor: pointer;" @click="toggleShowSideBar">
                    <i class="fas fa-angle-double-right fa-lg"></i>
                </span>
            </div>
            <div class="btn-toolbar" role="toolbar">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-warning" @click="resetStamps">
                        <i class="fas fa-redo fa-lg"></i>
                    </button>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn" v-bind:class="stampSortClass('all')" @click="stampSort = 'all'">アップロード順</button>
                    <button type="button" class="btn" v-bind:class="stampSortClass('latest')" @click="stampSort = 'latest'">送信された順</button>
                    <button type="button" class="btn" v-bind:class="stampSortClass('count')" @click="stampSort = 'count'">回数順</button>
                </div>
                <div class="btn-group" role="group" v-if="!guest">
                    <button type="button" class="btn" v-bind:class="onlyFavoriteButtonClass" @click="onlyFavorite = !onlyFavorite">
                        <span style="color: hotpink;"><i class="fas fa-heart fa-lg"></i></span> お気に入りのみ表示
                    </button>
                </div>
            </div>

            <div class="tagViewInformation" v-show="searchTag !== ''">
                <strong>「{{ searchTag }}」</strong>タグが付いたスタンプのみ表示中 <button class="btn btn-danger" @click="resetSearchTag">解除する</button>
            </div>
            <div class="tagViewInformation" v-show="onlyNoTags">
                タグが設定されていないスタンプのみ表示中 <button class="btn btn-danger" @click="resetSearchTag">解除する</button>
            </div>

            <div class="stampList">

                <div style="margin:0; padding:0; height:140px; width:1px;"></div>

                <div class="stampForm" v-show="canUploadStamp && searchTag === ''">
                    <vue-dropzone
                        ref="myVueDropzone"
                        id="dropzone"
                        :options="dropzoneOptions"
                        v-on:vdropzone-success="uploadSuccessEvent"
                        v-on:vdropzone-error="uploadErrorEvent"
                    />
                </div>

                <div v-for="(stamp, index) in stampList" :key="index"
                     class="stampWrapper" v-bind:style="cursor" @click="sendStamp(stamp.id)"
                     @mouseover="hoverStampId = stamp.id" @mouseleave="hoverStampId = null">
                    <div class="preview" v-if="hoverPreviewStampId == stamp.id">
                        <img class="stamp" :src="stamp.name">
                    </div>
                    <div class="stampRippleWrapper" v-ripple>
                        <div class="animationFlagWrapper" v-if="stamp.is_animation">
                            <div class="animationFlag">
                                <i class="fas fa-film fa-3x" style="color: #f00"></i>
                            </div>
                        </div>
                        <img class="stamp" :src="(stamp.is_animation && hoverStampId === stamp.id) ? stamp.name : (stamp.thumbnail ? stamp.thumbnail : stamp.name)">
                    </div>
                    <div class="favoriteForm" @click.stop="toggleFavorite(stamp.id)" v-if="!guest" v-bind:class="{containsFavorite: isContainsFavorite(stamp.id)}">
                        <span v-show="!isContainsFavorite(stamp.id)"><i class="far fa-heart fa-2x"></i></span>
                        <span v-show="isContainsFavorite(stamp.id)" style="opacity: 0.8"><i class="fas fa-heart fa-2x"></i></span>
                    </div>
                    <div class="downloadForm"
                         @mouseover="hoverPreviewStampId = stamp.id" @mouseleave="hoverPreviewStampId = null"
                         @click.stop>
                        <a :href="stamp.name" target="_blank"><i class="fas fa-external-link-alt fa-2x"></i></a>
                    </div>
                    <div class="deleteForm" @click.stop="deleteStamp(stamp.id, stamp.user_id, stamp.name)" v-if="stamp.room_id && canDelete(stamp.user_id)">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div class="tagForm" @click.stop="openTagForm(stamp.id, stamp.name)">
                        <i class="fas fa-tags fa-2x"></i><span class="badge" v-if="stamp.tags.length > 0">{{ stamp.tags.length }}</span>
                    </div>
                </div>

                <infinite-loading @infinite="infiniteHandler" ref="infiniteLoading" v-if="useInfinite">
                <span slot="no-results">
                    データがありません
                </span>
                    <span slot="no-more"></span>
                </infinite-loading>
            </div>
        </div>


    </div>
</template>

<script>
    import Vue from "vue"

    // 画像アップロード
    import vue2Dropzone from 'vue2-dropzone'

    // ダイアログ
    import VuejsDialog from "vuejs-dialog"
    Vue.use(VuejsDialog);

    // ダイアログ（タグ用）
    import ModalDialogs from 'vue-modal-dialogs'
    Vue.use(ModalDialogs);
    import MessageComponent from './tag_form'
    const message = ModalDialogs.makeDialog(MessageComponent, 'image', 'roomId', 'stampId', 'allTags');

    // 無限スクロール
    import InfiniteLoading from 'vue-infinite-loading'

    // トースト
    import Toasted from 'vue-toasted'
    Vue.use(Toasted, {
        position: 'top-center',
        duration: 3000,
        iconPack: 'fontawesome',
        singleton: true,
    });

    // リップル
    import Ripple from 'vue-ripple-directive'
    Ripple.color = 'rgba(100, 213, 103, 0.5)';
    Vue.directive('ripple', Ripple);

    // ページネーション
    import VuePaginate from 'vue-paginate'
    Vue.use(VuePaginate);

    // 遅延ロード
    import VueLazyload from 'vue-lazyload'
    Vue.use(VueLazyload);

    export default {
        data: function () {
            return {
                stamps: [],
                favorites: [],
                stampSort: 'all',
                onlyFavorite: false,
                useInfinite: true,
                hoverStampId: null,
                hoverPreviewStampId: null,
                searchTag: '',
                onlyNoTags: false,
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
                allTags: {},
                showSideBar: true,
            };
        },
        props: {
            room: Object,
            imprinterLevel: Number,
            uploaderLevel: Number,
            userId: Number,
        },
        created: function () {
            this.readSettings();

            if (!this.guest) {
                this.getFavorites();
            }

            this.getAllTags();
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

        },
        methods: {
            getStamps: function () {
                let url = '/api/v1/rooms/' + this.room.id + '/stamps';

                if (this.guest) {
                    url = url + '/guest';
                }

                let params = {
                    sort: this.stampSort,
                    onlyFavorite: (this.onlyFavorite && this.useInfinite) ? 1 : 0,
                    tag: this.searchTag,
                };

                if (this.onlyNoTags) {
                    params.onlyNoTags = 1;
                }

                axios.get(url,
                    {
                        params: params,
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
                        if (error.response.data.message === "Too Many Attempts.") {
                            this.$toasted.error('連投を検知しました、少し時間をおいてください。', {icon: 'exclamation-triangle'});
                        } else {
                            this.$toasted.error('スタンプ送信に失敗しました。', {icon: 'exclamation-triangle'});
                        }
                    });
                } else {
                    // 送れない場合の説明文章

                }
            },
            uploadSuccessEvent: function (file, response) {
                this.$toasted.success('アップロードが完了しました。', {icon: 'upload'});
                this.stamps.unshift(response.stamp);
            },
            uploadErrorEvent: function () {
                this.$toasted.error('【エラー】2MBまでの画像のみアップ可能です。ファイルに問題無い場合、少し待ってからやり直して下さい。', {icon: 'exclamation-triangle'});
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

                let params = {
                    page: currentPage,
                    sort: this.stampSort,
                    onlyFavorite: this.onlyFavorite ? 1 : 0,
                    tag: this.searchTag,
                };

                if (this.onlyNoTags) {
                    params.onlyNoTags = 1;
                }

                axios.get(url, {
                    params: params,
                }).then(({ data }) => {
                    if (data.stamps.length) {
                        this.stamps = this.stamps.concat(data.stamps);
                        $state.loaded();
                        if (data.stamps.length < 30 || this.stamps.length > 500) {
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
            },
            openTagForm: async function (stampId, stampName) {
                let newTags;
                if (newTags = await message(stampName, this.room.id, stampId, this.allTags)) {
                    this.getAllTags();

                    let stampIndex = this.stamps.findIndex(el => {
                        return stampId === el.id;
                    });

                    // 今更新したスタンプのタグ更新
                    if (stampIndex >= 0) {
                        this.stamps[stampIndex].tags = newTags;

                        // タグが設定されていないものを表示中にタグを設定した結果、
                        // タグが1つ以上になった場合はリストから削除する
                        if (this.onlyNoTags && newTags.length > 0) {
                            this.stamps.splice(stampIndex, 1);
                        }
                    }
                }
            },
            getAllTags: function () {
                let url = '/api/v1/rooms/' + this.room.id + '/tags';
                axios.get(url)
                    .then(response => {
                        this.allTags = response.data;
                    })
                    .catch(error => {
                        this.$toasted.error('タグ一覧の読み込みに失敗しました。', {icon: 'exclamation-triangle'});
                    });
            },
            setSearchTag: function (tagName) {
                this.searchTag = tagName;
                this.onlyNoTags = false;
                this.resetStamps();
            },
            setSearchNoTag: function () {
                this.searchTag = '';
                this.onlyNoTags = true;
                this.resetStamps();
            },
            resetSearchTag: function () {
                this.onlyNoTags = false;
                this.searchTag = '';
                this.resetStamps();
            },
            toggleShowSideBar: function () {
                this.showSideBar = !this.showSideBar;

                let showSideBarSetting = localStorage.getItem('showSideBar');
                if (!showSideBarSetting) {
                    showSideBarSetting = {};
                } else {
                    showSideBarSetting = JSON.parse(showSideBarSetting);
                }

                showSideBarSetting[this.room.id] = this.showSideBar ? 1 : 0;
                localStorage.setItem('showSideBar', JSON.stringify(showSideBarSetting));
            },
            readSettings: function () {
                // サイドバーの開閉状態
                let showSideBarSetting = localStorage.getItem('showSideBar');
                if (showSideBarSetting) {
                    showSideBarSetting = JSON.parse(showSideBarSetting);
                    let showSideBar = showSideBarSetting[this.room.id];

                    if (showSideBar !== undefined) {
                        this.showSideBar = showSideBar;
                    }
                }
            },
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
    .stampListWrapper {
        display: flex;
    }

    .sidebar {
        min-width: 300px;
    }

    .tag {
        cursor: pointer;
    }

    .main {
        flex-basis: 100%;
    }

    .tagViewInformation {
        margin-bottom : 10px;
    }

    .tagViewInformation strong {
        color: #aa0000;
        font-weight: bold;
    }

    .btn-group {
        margin-bottom: 12px;
    }

    .stampList {
        display: flex;
        flex-wrap: wrap;
        min-height: 140px;
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
        max-width: 100%;

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

    .downloadForm {
        opacity: 0;
        transition: .5s ease;
        position: absolute;
        left: 6px;
        bottom: 4px;
        text-align: left;
        color: #4c4cff;
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

    .preview {
        position: absolute;
        top: -110px;
        z-index: 10;
        left: 0;
        height: 140px;
        transform: scale(2.0);
        border: solid 1px #000;
    }

    .preview img {
        border-radius: 0;
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

    .tagForm {
        opacity: 0;
        transition: .5s ease;
        position: absolute;
        left: 6px;
        top: 4px;
        text-align: left;
        color: #0caa0c;
    }

    .tagForm .badge {
        background-color: #ff3c3c;
        position:relative;
        left: -12px;
        top: -7px;
        border: solid 1px #ffffff;
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

    .paginateStamps {
        margin: 0;
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


    /* modal */

    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.33s;
    }

    .fade-enter, .fade-leave-active {
        opacity: 0;
    }


</style>