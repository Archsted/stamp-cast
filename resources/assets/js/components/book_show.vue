<template>
    <div>
        <ol class="breadcrumb">
            <li><a href="/home"><i class="fas fa-home"></i></a></li>
            <li><a href="/books">スタンプ帳</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ book.name }} <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li v-for="book in books"><a :href="'/books/' + book.id">{{ book.name }}</a></li>
                </ul>
            </li>
        </ol>

        <div class="control-panel">
            <div v-if="isStampsReady && stamps.length === 0">
                現在、このスタンプ帳に追加されたスタンプはありません。<br>
                追加は、スタンプルームでスタンプにマウスを合わせ、右上に表示される本アイコンを選択します。
            </div>
            <div class="form-inline" v-if="stamps.length > 0">
                <p>各スタンプはドラッグ＆ドロップで表示される順番を変えることも出来ます。</p>
                <div class="form-group">
                    <button class="btn btn-primary" style="margin-right: 6px;" @click="checkAll">全てを選択</button>
                </div>
                <div class="form-group">
                    <button class="btn btn-danger" v-show="hasCheckedStamp" @click="checkedList = []">全ての選択解除</button>
                </div>

                <div class="form-group" v-show="hasCheckedStamp" style="margin-left: 20px;">
                    <label class="sr-only" for="action">選択したスタンプを</label>
                    <select class="form-control" name="action" id="action" v-model="action">
                        <option value="">【選択したスタンプを】</option>
                        <option value="delete">スタンプ帳から削除</option>
                        <option value="move">別のスタンプ帳に移動</option>
                        <option value="copy">別のスタンプ帳にコピー</option>
                    </select>
                </div>

                <div class="form-group" v-show="isSelectBookAction">
                    <label class="sr-only" for="targetBook">どこに？</label>
                    <select class="form-control" name="targetBook" id="targetBook" v-model="targetBook">
                        <option value="">【どのスタンプ帳に？】</option>
                        <option value="0">（新規追加してそこに）</option>
                        <option v-for="book in books" :value="book.id">{{ book.name }}</option>
                    </select>
                </div>

                <button
                    type="button"
                    class="btn btn-success"
                    v-show="hasCheckedStamp"
                    :disabled="!isActionReady"
                    @click="operate"
                >実行</button>
            </div>
        </div>

        <draggable v-model="stamps" class="draggableWrapper" @end="sortEnd">
            <div v-for="(stamp, index) in stamps" :key="index"
                 class="stampWrapper"
                 @mouseover="hoverStampId = stamp.id"
                 @mouseleave="hoverStampId = null"
                 @click="toggleChecked(stamp.id)"
            >
                <img class="stamp" v-lazy="getStampSrc(stamp)">
                <div class="preview" v-if="hoverPreviewStampId == stamp.id">
                    <img class="stamp" :src="stamp.name">
                </div>
                <div class="animationFlagWrapper" v-if="stamp.is_animation">
                    <div class="animationFlag">
                        <i class="fas fa-film fa-2x" style="color: #f00"></i>
                    </div>
                </div>
                <div class="downloadForm"
                     @mouseover="hoverPreviewStampId = stamp.id" @mouseleave="hoverPreviewStampId = null"
                     @click.stop>
                    <a :href="stamp.name" target="_blank"><i class="fas fa-external-link-alt"></i></a>
                </div>
                <div class="checkedForm" v-show="isChecked(stamp.id)">
                    <span class="fa-layers fa-fw fa-2x">
                        <i class="fas fa-circle" style="color:#bf5329"></i>
                        <i class="fa-inverse fas fa-check" data-fa-transform="shrink-6" style="color:#ffff00"></i>
                    </span>
                </div>
            </div>
        </draggable>
    </div>
</template>

<script>
    import Vue from "vue";

    // ドラッグ
    import draggable from 'vuedraggable';

    // トースト
    import Toasted from 'vue-toasted';
    Vue.use(Toasted, {
        position: 'top-center',
        duration: 3000,
        iconPack: 'fontawesome',
        singleton: true,
    });

    import VueLazyload from 'vue-lazyload';
    Vue.use(VueLazyload, {
        attempt: 1,
    });

    export default {
        data: function () {
            return {
                stamps: [],
                books: [],
                checkedList: [],
                hoverStampId: null,
                hoverPreviewStampId: null,
                action: '',
                targetBook: '',
                isStampsReady: false,
            };
        },
        components: {
            draggable,
        },
        props: {
            book: Object
        },
        created: function() {
            this.getStamps();
            this.getBooks();
        },
        methods: {
            getStamps() {
                let url = '/api/v1/books/' + this.book.id;
                axios.get(url)
                    .then(response => {
                        this.stamps = response.data.stamps;
                        this.isStampsReady = true;
                    })
                    .catch(error => {
                        this.$toasted.error('スタンプの読み込みに失敗しました。', {icon: 'exclamation-triangle'});
                    });
            },
            getBooks() {
                let url = '/api/v1/books';
                axios.get(url)
                    .then(response => {
                        this.books = response.data.filter(book => {
                            return book.id !== this.book.id;
                        });
                    })
                    .catch(error => {
                        this.$toasted.error('スタンプ帳の読み込みに失敗しました。', {icon: 'exclamation-triangle'});
                    });
            },
            getStampSrc(stamp) {
                if (stamp.is_animation && this.hoverStampId === stamp.id) {
                    return stamp.name;
                } else {
                    if (stamp.thumbnail) {
                        return stamp.thumbnail;
                    } else {
                        return stamp.name;
                    }
                }
            },

            sortEnd({oldIndex, newIndex}) {
                let url = '/api/v1/books/' + this.book.id + '/stamps/' + this.stamps[newIndex].id + '/order';

                axios.put(url, {order: newIndex + 1})
                    .then(response => {

                    })
                    .catch(error => {
                        alert('エラーが発生しました。画面を更新します。');
                        location.reload();
                    });
            },

            toggleChecked(stampId) {
                let stampIndex = this.checkedList.findIndex(el => {
                    return stampId === el;
                });

                if (stampIndex >= 0) {
                    this.checkedList.splice(stampIndex, 1);
                } else {
                    this.checkedList.push(stampId);
                }
            },

            isChecked(stampId) {
                let stampIndex = this.checkedList.findIndex(el => {
                    return stampId === el;
                });

                return stampIndex >= 0;
            },

            checkAll() {
                this.checkedList = [];

                this.stamps.forEach(stamp => {
                    this.checkedList.push(stamp.id);
                });
            },

            operate() {
                switch (this.action) {
                    case 'delete':
                        this.deleteStamps();
                        break;
                    case 'move':
                        this.moveStamps();
                        break;
                    case 'copy':
                        this.copyStamps();
                        break;
                    default:
                }
            },

            deleteStamps() {
                let url = '/api/v1/books/' + this.book.id + '/stamps';

                let stampIds = this.checkedList;

                axios.put(url, {stampIds: stampIds})
                    .then(response => {
                        this.$toasted.success('スタンプを削除しました。', {icon: 'trash-alt'});

                        this.stamps = this.stamps.filter(stamp => {
                            return !stampIds.includes(stamp.id);
                        });

                        this.checkedList = [];
                    })
                    .catch(error => {
                        alert('エラーが発生しました。画面を更新します。');
                        location.reload();
                    });
            },

            moveStamps() {
                let url = '/api/v1/books/' + this.book.id + '/stamps/move';

                let stampIds = this.checkedList;
                let targetBookId = parseInt(this.targetBook, 10);

                axios.put(url, {stampIds: stampIds, bookId: targetBookId})
                    .then(response => {
                        this.stamps = this.stamps.filter(stamp => {
                            return !stampIds.includes(stamp.id);
                        });

                        this.checkedList = [];

                        // Book新規作成だった場合はBook一覧を読み直す
                        if (targetBookId === 0) {
                            this.getBooks();
                        }

                        this.$toasted.success('スタンプを移動しました。', {icon: 'trash-alt'});
                    })
                    .catch(error => {
                        alert('エラーが発生しました。画面を更新します。');
                        location.reload();
                    });
            },

            copyStamps() {
                let url = '/api/v1/books/' + this.book.id + '/stamps/copy';

                let stampIds = this.checkedList;
                let targetBookId = parseInt(this.targetBook, 10);

                axios.put(url, {stampIds: stampIds, bookId: targetBookId})
                    .then(response => {
                        this.checkedList = [];

                        // Book新規作成だった場合はBook一覧を読み直す
                        if (targetBookId === 0) {
                            this.getBooks();
                        }

                        this.$toasted.success('スタンプをコピーしました。', {icon: 'trash-alt'});
                    })
                    .catch(error => {
                        alert('エラーが発生しました。画面を更新します。');
                        location.reload();
                    });
            },
        },
        computed: {
            hasCheckedStamp() {
                return this.checkedList.length > 0;
            },

            isSelectBookAction() {
                return (this.hasCheckedStamp && (this.action === 'move' || this.action === 'copy'));
            },

            isActionReady() {
                if (this.action === '') {
                    return false;
                }
                if (this.action === 'delete') {
                    return true;
                }

                if (this.action === 'copy' || this.action === 'move') {
                    return (this.targetBook !== '');
                } else {
                    return false;
                }
            }
        },

    }

</script>

<style scoped>

    .control-panel {
        margin-bottom: 18px;
    }

    .draggableWrapper {
        display: flex;
        flex-wrap: wrap;
    }

    .stampWrapper {
        position: relative;
        margin-left: 1px;
        margin-right: 1px;
        margin-bottom: 2px;
        /*height: 70px;*/
        min-width: 45px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        cursor: pointer;

        border: solid 1px #888;

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

    .stamp {
        height: 70px;
        max-width: 100%;
    }

    .stampWrapper:hover > div {
        opacity: 1;
    }

    .stampWrapper:hover .animationFlagWrapper {
        opacity: 0;
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
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        padding: 2px 6px;
    }

    .downloadForm {
        opacity: 0;
        transition: .5s ease;
        position: absolute;
        left: 3px;
        bottom: 0;
        text-align: left;
        color: #4c4cff;
    }

    .checkedForm {
        opacity: 1 !important;
        transition: .5s ease;
        position: absolute;
        left: 0;
        top: 3px;
        text-align: left;
    }

    .preview {
        position: absolute;
        top: -60px;
        z-index: 10;
        left: 0;
        transform: scale(2.0);
        border: solid 1px #000;
    }
</style>