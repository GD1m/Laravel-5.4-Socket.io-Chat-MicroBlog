<template>
    <div class="youtube-attachment-menu">
        <ul>
            <li is="youtube-item"
                v-for="(video, index) in attachments.youtube"
                v-bind:videoId="video.value"
                v-bind:start-time="video.startTime"
                v-on:remove="removeVideo(index)"></li>
            <div class="clearfix"></div>
        </ul>

        <form v-on:submit.prevent="addVideo()">
            <div class="input-group">
                <input v-model="url"
                       type="text"
                       placeholder="Paste YouTube video Url here"
                       class="form-control">
                <span class="input-group-btn">
                    <button v-bind:disabled="!url" type="submit" class="btn btn-info">Add</button>
                </span>
            </div>
        </form>
    </div>
</template>
<style>

</style>
<script>
    export default{
        data(){
            return {
                url: '',
                videoId: '',
                startTime: 0,
            }
        },
        props: ['attachments'],
        components: {
            'youtube-item': {
                template: `
                            <li class="youtube-item">
                                <youtube :video-id="videoId" player-width="230" player-height="150" :player-vars="{start: startTime}"></youtube>
                                <button v-on:click="$emit('remove')" class="btn btn-default btn-sm youtube-item-remove-button">
                                    <span class="glyphicon glyphicon-remove remove-message"></span> Remove
                                </button>
                            </li>
                        `,
                props: ['videoId', 'startTime']
            }
        },
        methods: {
            initVideoInfoByUrl (url) {
                this.videoId = this.$youtube.getIdFromURL(url);
                this.startTime = this.$youtube.getTimeFromURL(url);
            },
            makeAttachment: function () {
                let attachment = this.$parent.makeAttachmentAbstract('youtube', this.videoId);
                attachment.startTime = this.startTime;

                return attachment;
            },
            clearProps: function () {
                this.url = '';
                this.videoId = '';
                this.startTime = 0;
            },
            addVideo: function () {
                this.initVideoInfoByUrl(this.url);

                this.$emit('new-attachment', this.makeAttachment());

                this.clearProps();
            },
            removeVideo(index)
            {
                this.$emit('delete-attachment-by-index', 'youtube', index);
            }
        }
    }
</script>