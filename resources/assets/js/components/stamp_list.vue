<template>
    <div class="stampList">
        <div class="stampForm" v-if="canUploadStamp">
            <vue-dropzone
                ref="myVueDropzone"
                id="dropzone"
                :options="dropzoneOptions"
                v-on:vdropzone-complete="uploadCompleteEvent"
            ></vue-dropzone>
        </div>

        <div v-for="stamp in stamps" :key="stamp.id" class="stampWrapper" v-bind:style="cursor">
            <img :src="stamp.name" @click="sendStamp(stamp.id)" class="stamp">
            <!--
            <div class="favoriteForm">
                <span class="glyphicon glyphicon-star-empty"></span>
            </div>
            -->
        </div>
    </div>
</template>

<script>
    import vue2Dropzone from 'vue2-dropzone'
//    import 'vue2-dropzone/dist/vue2Dropzone.css'

    export default {
        data: function () {
            return {
                stamps: [],
                dropzoneOptions: {
                    url: '/api/v1/rooms/' + this.roomId + '/stamps',
                    params: {
                        user_id: this.userId
                    },
                    createImageThumbnails: false,
                    dictDefaultMessage: '<span class="glyphicon glyphicon-plus-sign"></span>'
                }
            };
        },
        props: [
            'roomId',
            'imprinterLevel',
            'uploaderLevel',
            'userId'
        ],
        mounted: function () {
            this.getStamps();
        },
        components: {
            vueDropzone: vue2Dropzone
        },
        computed: {
            canUploadStamp: function () {
                return (this.uploaderLevel === '1' || (this.uploaderLevel === '2' && this.userId));
            },
            canSendStamp: function () {
                return (this.imprinterLevel === '1' || (this.imprinterLevel === '2' && this.userId));
            },
            cursor: function () {
                return this.canSendStamp ? {
                    cursor: 'pointer'
                } : {
                    cursor: 'not-allowed'
                };
            },
        },
        methods: {
            canUploadStamp() {
                return (this.uploaderLevel === '1' || (this.uploaderLevel === '2' && this.userId));
            },
            getStamps() {
                // スタンプ一覧
                let url = '/api/v1/rooms/' + this.roomId + '/stamps';

                axios.get(url).then((response) => {
                    this.stamps = response.data.stamps;
                }).catch( error => { console.log(error); });
            },
            sendStamp(stamp_id) {
                if (this.canSendStamp) {
                    // スタンプ送信
                    let url = '/api/v1/rooms/' + this.roomId + '/imprints';

                    axios.post(url, {
                        stamp_id: stamp_id,
                        user_id: this.userId,
                    }).then((response) => {

                    }).catch( error => {
                        console.log(error);
                    });
                } else {
                    // 送れない場合の説明文章

                }
            },
            uploadCompleteEvent(file) {
                this.getStamps();
            },
        }
    }
</script>

<style>
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
    }

    .stamp {
        height: 100%;
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
        font-size: 2em;
    }

    .stampForm {
        background-color: #FFA;
        box-sizing: border-box;
        border: dashed 4px #555;
        height: 140px;
        width: 140px;
        line-height: 140px;
        text-align: center;
        font-size: 2.5em;
        overflow: hidden;
        float: left;
    }
</style>