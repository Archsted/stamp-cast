<template>
    <div class="stampList">
        <div class="stampForm">
            <vue-dropzone
                ref="myVueDropzone"
                id="dropzone"
                :options="dropzoneOptions"
                v-on:vdropzone-complete="uploadCompleteEvent"
            ></vue-dropzone>
        </div>

        <img v-for="stamp in stamps" :src="stamp.name" :key="stamp.id" @click="sendStamp(stamp.id)" class="stamp">
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
                    createImageThumbnails: false,
                    dictDefaultMessage: '<span class="glyphicon glyphicon-plus-sign"></span>'
                }
            };
        },
        props: [
            'roomId'
        ],
        mounted: function () {
            this.getStamps();
        },
        components: {
            vueDropzone: vue2Dropzone
        },
        methods: {
            getStamps() {
                // スタンプ一覧
                let url = '/api/v1/rooms/' + this.roomId + '/stamps';

                axios.get(url).then((response) => {
                    this.stamps = response.data.stamps;
                }).catch( error => { console.log(error); });
            },
            sendStamp(stamp_id) {
                // スタンプ送信
                let url = '/api/v1/rooms/' + this.roomId + '/imprints';

                axios.post(url, {
                    stamp_id: stamp_id
                }).then((response) => {

                }).catch( error => { console.log(error); });
            },
            uploadCompleteEvent(file) {
                this.getStamps();
            },
        }
    }
</script>

<style>
    .stamp {
        border: solid 2px #888;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;


        float:left;
        max-height: 140px;
        max-width: 140px;
    }

    .stampForm {
        background-color: #FFA;
        box-sizing: border-box;
        border: dashed 4px #555;
        height: 100px;
        width: 100px;
        line-height: 100px;
        text-align: center;
        font-size: 2.5em;
        overflow: hidden;
        float: left;
    }
    
    .stampList {

    }

</style>