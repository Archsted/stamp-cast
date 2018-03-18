<template>
    <div class="message-wrapper" @click.self="$close()">
        <div class="message-content">
            <div>
                <div style="text-align: center">
                    <h2>スタンプのタグ編集</h2>
                    <div class="deleteConfirmStamp">
                        <div class="stampWrapper"><img :src="image" class="stamp"></div>
                    </div>
                </div>
                <p>タグ名は1つにつき64文字まで、合計6個まで設定できます。<br>入力候補は↑↓キーで選択、Enterで決定、Escキーでキャンセルできます。</p>
                <div v-if="oldTagsLoad">
                    <tags-input element-id="tags"
                                v-model="tags"
                                placeholder="ここに入力してEnterで追加"
                                :oldTags="oldTags"
                                :existing-tags="allTagList"
                                :limit="6"
                                :typeahead="true">
                    </tags-input>

                    <div style="margin-top: 20px; text-align:center;">
                        <button @click="cancel">キャンセル</button>
                        <button @click="save">この内容で送信する</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Vue from "vue"

    // タグ入力
    import TagsInput from './TagsInput';
    Vue.component('tags-input', TagsInput);

    export default {
        data: function () {
            return {
                tag: '',
                tags: [],
                oldTags: [],
                oldTagsLoad: false,
            };
        },
        props: {
            'image': String,
            'roomId': Number,
            'stampId': Number,
            'allTags': Array,
        },
        computed: {
            allTagList: function () {
                let result = {};
                this.allTags.forEach((obj, index) => {
                    result[obj.text] = obj.text;
                });
                return result;
            }
        },
        created: function () {
            this.getOldTags();
        },
        mounted: function () {
            // タグ入力フォームの入力上限を設定

        },
        components: {

        },
        methods: {
            cancel () {
                this.$close(false)
            },
            save () {
                let url = '/api/v1/rooms/' + this.roomId + '/stamps/' + this.stampId + '/tags';

                axios.put(url, {tags: this.tags})
                    .then(response => {
                        this.$close(response.data.tags);
                    })
                    .catch(error => {
                        this.$toasted.error('タグの編集に失敗しました。', {icon: 'exclamation-triangle'});
                    });
            },
            getOldTags () {
                let url = '/api/v1/rooms/' + this.roomId + '/stamps/' + this.stampId + '/tags';

                axios.get(url)
                    .then(response => {
                        this.oldTags = response.data.tags;
                        this.oldTagsLoad = true;
                        this.$nextTick(() => {
                            let tagInput = $('input[type="text"]', '.message-content').eq(0);
                            tagInput.attr('maxlength', 64);
                            tagInput.focus();
                        });
                    })
                    .catch(error => {
                        this.$toasted.error('スタンプのタグ取得に失敗しました。', {icon: 'exclamation-triangle'});
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


    /* tag input */

    .message-wrapper .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 100%;
        font-weight: 700;
        line-height: 1;
        text-align: left;
        white-space: normal;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }

    .message-wrapper .badge:empty {
        display: none;
    }

    .message-wrapper .badge-pill {
        padding-right: 0.6em;
        padding-left: 0.6em;
        border-radius: 10rem;
    }

    .message-wrapper .badge-primary {
        color: #fff;
        background-color: #007bff;
    }

    .message-wrapper .badge-primary[href]:focus,
    .message-wrapper .badge-primary[href]:hover {
        color: #fff;
        text-decoration: none;
        background-color: #0062cc;
    }

    .message-wrapper .badge-light {
        color: #212529;
        background-color: #7cff7c;
    }

    .message-wrapper .badge-light[href]:focus,
    .message-wrapper .badge-light[href]:hover {
        color: #212529;
        text-decoration: none;
        background-color: #0cdd0c;
    }

    .message-wrapper .badge-dark {
        color: #fff;
        background-color: #343a40;
    }

    .message-wrapper .badge-dark[href]:focus,
    .message-wrapper .badge-dark[href]:hover {
        color: #fff;
        text-decoration: none;
        background-color: #1d2124;
    }

    /**
    * Original styles
    */
    .tags-input-default-class {
        padding: .5rem .25rem;

        background: #fff;

        border: 1px solid transparent;
        border-radius: .25rem;
        border-color: #dbdbdb;
        max-width: 500px;
    }

    .tags-input-remove {
        cursor: pointer;
        position: relative;
        display: inline-block;
        width: 1rem;
        height: 1rem;
        overflow: hidden;
    }

    .tags-input-remove::before, .tags-input-remove::after {
        content: '';
        position: absolute;
        width: 100%;
        top: 50%;
        left: 0;
        background: #5dc282;

        height: 2px;
        margin-top: -1px;
    }

    .tags-input-remove::before {
        transform: rotate(45deg);
    }
    .tags-input-remove::after {
        transform: rotate(-45deg);
    }

</style>