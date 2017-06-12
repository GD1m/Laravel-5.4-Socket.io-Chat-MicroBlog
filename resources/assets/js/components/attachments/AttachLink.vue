<template>
    <div class="link-attachment-menu">
        <button v-on:click.prevent="showAddLinkDialog()"
                v-if="linkObjectIsEmpty"
                type="button"
                class="btn btn-info add-new-link-button">
            Add Link
        </button>
        
        <ul>
            <li is="link-item"
                v-for="(link, index) in attachments.link"
                v-bind:value="link.value"
                v-bind:title="link.title"
                v-on:remove="removeLink(index)"></li>
        </ul>

        <form class="form-horizontal" v-if="!linkObjectIsEmpty">
            <div class="form-group">
                <label class="control-label col-sm-2" for="title">Title:</label>
                <div class="col-sm-10">
                    <input v-model="linkObject.value" type="text" class="form-control" id="title"
                           placeholder="Enter title for link">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="url">Url:</label>
                <div class="col-sm-10">
                    <input v-model="linkObject.href" type="text" class="form-control" id="url" placeholder="Enter Url">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2">Result:</label>
                <div class="col-sm-10">
                    <a v-if="finalUrl" v-bind:href="finalUrl">{{ linkObject.value }}</a>
                    <br/>
                    ({{ finalUrl }})
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button v-on:click.prevent="addNewLink" v-bind:disabled="!finalUrl || !linkObject.value"
                            type="submit" class="btn btn-success">Ok
                    </button>
                    <button v-on:click.prevent="cancel" type="button" class="btn btn-danger">Cancel</button>
                </div>
            </div>
        </form>

    </div>
</template>
<style>

</style>
<script>
    window.linkify = require('linkifyjs');
    window.linkifyStr = require('linkifyjs/string');

    window.bootbox = require('bootbox');

    export default{
        data(){
            return {
                linkObject: {}
            }
        },
        props: ['attachments'],
        computed: {
            linkObjectIsEmpty: function () {
                return typeof(this.linkObject.value) === 'undefined';
            },
            finalUrl: function () {
                let link = this.parseLinkObjectFromString(this.linkObject.href);

                return link ? link.href : null;
            }
        },
        components: {
            'link-item': {
                template: `
                            <li>
                                <a v-bind:href="value">{{ title }}</a>
                                ({{ value }})
                                <button v-on:click="$emit('remove')" class="btn btn-default btn-sm">
                                    <span class="glyphicon glyphicon-remove remove-message"></span>
                                </button>
                            </li>
                        `,
                props: ['title', 'value']
            }
        },
        methods: {
            makeAttachment: function (linkObject) {
                let attachment = this.$parent.makeAttachmentAbstract('link', linkObject.href);
                attachment.title = linkObject.value;

                return attachment;
            },
            parseLinkObjectFromString: function (string) {
                let linkArray = linkify.find(string);

                if (!linkArray.length) {
                    return false;
                }

                return linkArray.shift();
            },
            setLinkObject: function (link) {
                this.linkObject = link;
            },
            showAddLinkDialog: function () {
                let self = this;
                bootbox.prompt("Enter URL or Email address here (google.com, http://google.com or billy@microsoft.com for example)", function (result) {
                    if (!result) {
                        return;
                    }

                    let link = self.parseLinkObjectFromString(result);

                    if (!link) {
                        return bootbox.alert('Invalid link! Try again');
                    }

                    self.setLinkObject(link);
                });
            },
            cancel: function () {
                this.linkObject = {};
            },
            addNewLink: function () {
                this.linkObject.href = this.finalUrl;

                this.$emit('new-attachment', this.makeAttachment(this.linkObject));

                this.linkObject = {};
            },
            removeLink: function (index) {
                this.$emit('delete-attachment-by-index', 'link', index);
            }
        }
    }
</script>