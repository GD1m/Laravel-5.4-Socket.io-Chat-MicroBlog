<template>
    <div class="panel panel-info">
        <div v-on:click.prevent="hide()"
             class="panel-heading"
             style="cursor: pointer;">
            <span v-if="attachmentsIsEmpty">Attach to message</span>
            <span v-else>Attached to message: |</span>

            <span v-if="attachments.image.length">
                <strong> {{ attachments.image.length }}</strong> image(s) |
            </span>
            <span v-if="attachments.link.length">
                <strong> {{ attachments.link.length }}</strong> link(s) |
            </span>
            <span v-if="attachments.youtube.length">
                <strong> {{ attachments.youtube.length }}</strong> YouTube video(s) |
            </span>

            <a class="pull-right">Hide Panel</a>
        </div>
        <div class="panel-body">
            <navigation v-bind:currentAttachmentComponent.sync="currentAttachmentComponent"></navigation>
        </div>
        <div class="panel-body">

            <keep-alive>
                <component v-bind:is="currentAttachmentComponent"
                           v-bind:attachments="attachments"
                           v-on:new-attachment="appendAttachment"
                           v-on:delete-attachment="removeAttachment"
                           v-on:delete-attachment-by-index="removeAttachmentByIndex"></component>
            </keep-alive>

        </div>
    </div>
</template>
<style>

</style>
<script>

    import Navigation from './Navigation.vue';
    import AttachImage from './AttachImage.vue';
    import AttachLink from './AttachLink.vue';
    import AttachYoutube from './AttachYoutube.vue';

    //import {bus} from '../../chat.js';

    export default{
        data (){
            return {
                currentAttachmentComponent: 'attach-image',
            }
        },
        mounted (){
//            bus.$on('attachments-cleared', this.clearAttachments());
        },
        computed: {
            attachmentsIsEmpty: function () {
                return !this.attachments.image.length
                    &&
                    !this.attachments.link.length
                    &&
                    !this.attachments.youtube.length;
            }
        },
        props: ['attachments'],
        components: {
            Navigation,
            AttachImage,
            AttachLink,
            AttachYoutube
        },
        methods: {
            hide: function () {
                app.toggleAttachmentsMenu(false);
            },
            makeAttachmentAbstract: function (type, value) {
                let attachment = {};

                attachment.type = type;
                attachment.value = value;

                return attachment;
            },
            appendAttachment: function (attachment) {
                app.appendAttachment(attachment);
            },
            removeAttachment: function (attachment) {
                app.removeAttachment(attachment);
            },
            removeAttachmentByIndex: function (type, index) {
                app.removeAttachmentByIndex(type, index);
            }
        }
    }
</script>
