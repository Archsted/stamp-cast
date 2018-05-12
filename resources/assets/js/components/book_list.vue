<template>
    <div class="book-list">
        <SortableList
            lockAxis="y"
            v-model="bookList"
            :useDragHandle="true"
            @sortEnd="onSortEnd($event)"
        >
            <SortableItem v-for="(book, index) in bookList" :index="index" :key="index" :book="book" />
        </SortableList>
    </div>
</template>

<script>
    import Vue from 'vue';
    import {ContainerMixin, ElementMixin, HandleDirective} from 'vue-slicksort';

    const SortableList = {
        mixins: [ContainerMixin],
        template: `
    <div class="list-group">
        <slot />
    </div>
  `,
    };

    const SortableItem = {
        data: function () {
            return {
                hoverRoomId: null,
            }
        },
        mixins: [ElementMixin],
        directives: { handle: HandleDirective },
        props: ['book'],
        methods: {
            deleteBook() {
                if (window.confirm('本当にこのスタンプ帳を削除しますか？')) {
                    axios.delete('/api/v1/books/' + this.book.id)
                        .then(response => {
                            location.reload();
                        })
                        .catch(error => {
                            alert(error.response.data.message);
                        });
                }
            },
        },
        template: `
        <a :href="'/books/' + book.id"
            class="list-group-item"
            @mouseover="hoverRoomId = book.id"
            @mouseleave="hoverRoomId = null"
        >
            <div class="book-item-wrapper">
                <div class="book-item-handle">
                    <span v-handle class="handle"><i class="fas fa-bars"></i></span>
                </div>
                <div class="book-item-body">
                    {{ book.name }} <span class="badge badge-danger">{{ book.stamps_count }}</span><span class="book-description" v-if="book.description != null">{{ book.description }}</span>
                </div>
                <div class="book-item-button" v-show="hoverRoomId === book.id">
                    <a class="btn btn-xs btn-warning" :href="'/books/' + book.id + '/edit'"><i class="fas fa-edit"></i> 名前変更</a>
                    <button class="btn btn-xs btn-danger" @click.prevent.stop="deleteBook"><i class="fas fa-trash-alt"></i> 削除</button>
                </div>
            </div>
        </a>
  `,
    };

    export default {
        data: function () {
            return {
                bookList: []
            };
        },
        props: {
            books: Array,
        },
        computed: {

        },
        created: function () {
            this.bookList = this.books;
        },
        methods: {
            onSortEnd: function ({ newIndex, oldIndex}) {
                if (newIndex === oldIndex) {
                    return false;
                }

                let url = '/api/v1/books/' + this.bookList[oldIndex].id + '/order';

                axios.put(url, {order: newIndex + 1})
                    .then(response => {

                    })
                    .catch(error => {
                        alert('エラーが発生しました。画面を更新します。');
                        location.reload();
                    });
            },
        },
        components: {
            SortableItem,
            SortableList,
        },
    }
</script>

<style>
    .book-item-wrapper {
        display: flex;
        position: relative;
    }

    .book-item-wrapper .badge {
        float: none !important;
    }

    .book-item-handle {
        align-self: center;
        margin-right: 10px;
    }

    .book-item-handle .handle {
        cursor: ns-resize;
    }

    .book-description {
        margin: 0 0 0 12px;
        padding: 0;
        font-size: 84%;
        color: #999999;
    }

    .book-item-button {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        text-align: right;
    }
</style>