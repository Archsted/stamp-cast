<template>
    <div class="message-wrapper" @click.self="$close()">
        <div class="message-content">
            <div class="form-body">
                <div style="text-align: center">
                    <h2>スタンプ帳への追加</h2>
                    <div class="deleteConfirmStamp">
                        <div class="stampWrapper"><img :src="image" class="stamp"></div>
                    </div>
                </div>

                <p>追加するスタンプ帳を選択してください。</p>

                <select class="form-control" name="book_id" id="book_id" v-model="selected">
                    <option disabled value="">選択して下さい</option>
                    <option v-for="book in books" v-bind:value="book.id" :disabled="findStampId(book.stamps)">
                        <template v-if="findStampId(book.stamps)">【登録済】</template>{{ book.name }}
                    </option>
                </select>
            </div>

            <div class="form-button">
                <button class="btn btn-default" @click="cancel">キャンセル</button> <button class="btn btn-success" @click="save" :disabled="selected === '' || selected === undefined">追加する</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                books: [],
                selected: '',
            };
        },
        props: {
            'image': String,
            'stampId': Number,
        },
        computed: {

        },
        created: function () {
            let url = '/api/v1/books';

            axios.get(url)
                .then(response => {
                    this.books = response.data;

                    if (this.books.length > 0) {
                        this.selected = this.books[0];
                    }

                }).catch(error => {
                    this.$toasted.error('スタンプ帳の読み込みに失敗しました。', {icon: 'exclamation-triangle'});
                });
        },
        mounted: function () {

        },
        components: {

        },
        methods: {
            cancel () {
                this.$close(false)
            },
            findStampId (stamps) {
                return stamps.find( (stamp) => {
                    return stamp.id === this.stampId;
                }) !== undefined;
            },
            save () {
                let url = '/api/v1/books/' + this.selected + '/stamps';

                axios.post(url, {'stamp_id': this.stampId})
                    .then(response => {
                        this.$close();
                    })
                    .catch(error => {
                        this.$toasted.error('スタンプ帳への追加に失敗しました。', {icon: 'exclamation-triangle'});
                    });
            },
        },
    }
</script>

<style>
    .message-wrapper {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.33);
        z-index: 9999;
    }

    .message-content {
        padding: 32px;
        border-radius: 2px;
        margin: 0 16px;
        background-color: white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.33);
        display: block;
        width: 550px;
    }

    .form-button {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .form-button button {
        margin: 0 10px;
    }

</style>