<template>
    <div class="image-attachment-menu">
        <label class="checkbox-inline compress-images-button">
            <input type="checkbox" v-model="compressImages" v-on:change="toggleCompressImages">
            Compress Images
        </label>
        <dropzone id="imageDropzone"
                  ref="imageDropzone"
                  url="/chat/post-attachment-image"
                  param-name="image"
                  accepted-file-types="image/*"
                  v-bind:max-number-of-files="15"
                  v-bind:max-file-size-in-m-b="50"
                  v-on:vdropzone-success="imageUploaded"
                  v-on:vdropzone-removed-file="imageRemoved">
            <input type="hidden" name="_token" v-bind:value="csrfToken">
        </dropzone>
    </div>
</template>
<style>

</style>
<script>
    import Dropzone from 'vue2-dropzone';

    import {bus} from '../../chat.js';

    export default{
        data(){
            return {
                csrfToken: $('meta[name=csrf-token]').attr('content'),
                compressImages: false,
                deleteStoredImagesOnDeleteEvent: true,
            }
        },
        mounted (){
            let self = this;
            bus.$on('attachments-cleared', function () {
                self.clearDropzone();
            })
        },
        props: ['attachments'],
        components: {
            Dropzone
        },
        methods: {
            toggleCompressImages: function () {
                if (this.compressImages) {
                    this.$refs.imageDropzone.setOption('resizeWidth', 1024);
                    this.$refs.imageDropzone.setOption('resizeQuality', 65);
                } else {
                    this.$refs.imageDropzone.setOption('resizeWidth', null);
                    this.$refs.imageDropzone.setOption('resizeQuality', null);
                }
            },
            makeAttachment: function (value) {
                return this.$parent.makeAttachmentAbstract('image', value);
            },
            imageUploaded: function (file, response) {
                this.$emit('new-attachment', this.makeAttachment(response.fileName));
            },
            imageRemoved: function (file, error, xhr) {
                if (file.accepted && this.deleteStoredImagesOnDeleteEvent) {
                    let xhrResponseWhenUploaded = JSON.parse(file.xhr.response);
                    let fileName = xhrResponseWhenUploaded.fileName;

                    this.deleteImage(fileName);
                }
            },
            deleteImage: function (fileName) {
                let self = this;
                axios.post('chat/delete-attachment-image', {
                    fileName: fileName
                })
                    .then(function (response) {
                        self.$emit('delete-attachment', self.makeAttachment(fileName));
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            },
            clearDropzone: function () {
                this.deleteStoredImagesOnDeleteEvent = false;

                this.$refs.imageDropzone.removeAllFiles();

                this.deleteStoredImagesOnDeleteEvent = true;
            }
        }
    }
</script>