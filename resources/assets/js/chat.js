import VueYouTubeEmbed from 'vue-youtube-embed'

Vue.use(VueYouTubeEmbed);

Vue.component('attachments', require('./components/attachments/Attachments.vue'));

const bus = new Vue();

export {bus};

window.app = new Vue({
    el: '#app',
    data: {
        authUser: {},
        users: [],

        message: "",
        messages: [],

        attachments: {
            image: [],
            link: [],
            youtube: [],
        },

        showAttachmentsMenu: false,

        getMessagesLimit: 50,
        getMessagesOffset: 0,
        haveElseMessages: false,

        scrollToTopOnNewMessage: true
    },
    beforeMount: function () {
        this.getMessages();
        this.joinChannels();
    },
    mounted: function () {
    },
    updated: function () {
        this.initToolTips();

        this.toggleLikeButtonWrapperDisabledClass();
    },
    computed: {
        // To show newest messages on top, not at the bottom of chat panel
        reversedMessages: function () {
            return this.reverseArray(this.messages);
        },
        messageIsEmpty: function () {
            return this.message.trim() === "" && this.attachmentsIsEmpty
        },
        attachmentsIsEmpty: function () {
            return !this.attachments.image.length
                &&
                !this.attachments.link.length
                &&
                !this.attachments.youtube.length;
        }
    },
    methods: {
        /*
         * Helpers
         */

        // Returns reversed copy of array
        reverseArray: function (array) {
            return array.slice().reverse();
        },
        // Find index of array by id attribute
        // Returns -1 if not found
        findIndexByElementKey: function (array, key, id) {
            return array.findIndex(function (element, index, array) {
                return element[key] === id;
            });
        },
        scrollToTop: function () {
            if (this.scrollToTopOnNewMessage) {
                let container = this.$el.querySelector("#chat");
                container.scrollTop = 0;
            }
        },
        scrollToAttachmentsMenu: function () {
            let offset = $('#attachments_menu').offset();

            $('html, body').animate({
                scrollTop: offset.top,
                scrollLeft: offset.left
            });
        },

        /*
         * Channels
         */

        // Join Laravel Echo channels and initialize listeners
        joinChannels: function () {
            // Presence channel and private channel
            this.joinOnlineChannelAndPrivateChannel();

            // Public chat channel
            this.joinPublicChatChannel();
        },
        joinOnlineChannelAndPrivateChannel: function () {
            Echo.join('chat-channel-online')
                .here((users) => {
                    console.table(users);
                    this.initUsers(users);

                    // Private chat channel (uses authUser object)
                    this.joinPrivateChatChannel();
                })
                .joining((user) => {
                    this.addUser(user);
                })
                .leaving((user) => {
                    let userIndex = this.findIndexByElementKey(this.users, 'id', user.id);

                    this.removeUser(userIndex);
                });
        },
        joinPublicChatChannel: function () {
            Echo.channel('chat-channel')
                .listen('MessageCreated', (event) => {
                    let message = event.message;

                    message.likes = event.likes;
                    message.attachment_images = event.attachment_images;
                    message.attachment_links = event.attachment_links;
                    message.attachment_youtubes = event.attachment_youtubes;

                    app.addMessage(message);
                })
                .listen('MessageDeleted', (event) => {
                    let messageIndex = this.findIndexByElementKey(this.messages, 'id', event.id);

                    this.removeMessage(messageIndex);
                })
                .listen('LikeCreated', (event) => {
                    this.addLike(event.like)
                });
        },
        joinPrivateChatChannel: function () {
            Echo.private(`chat-channel.${this.authUser.id}`);
            // private listeners here
        },

        /*
         * Users
         */

        initUsers: function (users) {
            this.users = users;

            this.authUser = users.slice(0).shift();
        },
        addUser: function (user) {
            this.users.push(user);
        },
        removeUser: function (index) {
            if (index !== -1) {
                this.users.splice(index, 1);
            }
        },

        /*
         * Messages
         */

        getMessages: function () {
            const self = this;
            this.getMessagesOffset = this.messages.length;

            axios.get('/chat/get-messages/' + this.getMessagesLimit + '/' + this.getMessagesOffset)
                .then(function (response) {
                    self.messages = self.reverseArray(response.data.messages).concat(self.messages);

                    self.haveElseMessages = response.data.haveElseMessages;
                });
        },
        showMoreMessages: function () {
            this.getMessages();
        },
        postMessage: function () {
            if (this.messageIsEmpty) {
                return false;
            }

            let message = this.message;
            this.message = "";

            let attachments = this.attachments;

            this.clearAttachments();

            // this.toggleAttachmentsMenu(false);

            axios.post('/chat/post-message', {
                message: message,
                attachments: attachments,
            })
                .then(function (response) {
                    //
                })
                .catch(function (error) {
                    console.error(error);
                });
        },
        addMessage: function (message) {
            this.scrollToTop();

            this.messages.push(message);
        },
        deleteMessage: function (id) {
            $('#remove_message_button_' + id).attr('disabled', 'disabled');

            axios.delete('/chat/delete-message/' + id)
                .then(function (response) {
                    //
                })
                .catch(function (error) {
                    console.error(error);
                });
        },
        removeMessage: function (index) {
            if (index !== -1) {
                this.messages.splice(index, 1);
            }
        },

        /*
         * Likes
         */

        getReversedLikes: function (message) {
            return this.reverseArray(message.likes);
        },
        postLike: function (id) {
            $('#like_message_button_' + id).attr('disabled', 'disabled');

            axios.put('/chat/like-message/' + id)
                .then(function (response) {
                    //
                })
                .catch(function (error) {
                    console.error(error);
                });
        },
        addLike: function (like) {
            let messageIndex = this.findIndexByElementKey(this.messages, 'id', like.message_id);

            if (messageIndex !== -1) {
                this.messages[messageIndex].likes.push(like);
            }
        },
        checkNotLikedYetByAuthUser: function (message) {
            let likeIndex = this.findIndexByElementKey(message.likes, 'user_id', this.authUser.id);

            return likeIndex === -1;
        },
        toggleLikeButtonWrapperDisabledClass: function () {
            $.each($('.like-button'), function (index, button) {
                if ($(button).is(':disabled')) {
                    $(button).parent().addClass('disabled');
                } else {
                    $(button).parent().removeClass('disabled');
                }
            });
        },

        /*
         * Attachments
         */
        toggleAttachmentsMenu: function (forceValue) {
            if (forceValue && typeof(forceValue) === "boolean") {
                this.showAttachmentsMenu = forceValue;
            } else {
                this.showAttachmentsMenu = !this.showAttachmentsMenu;
            }

            if (this.showAttachmentsMenu) {
                this.scrollToAttachmentsMenu();
            }
        },
        appendAttachment: function (attachment) {
            this.attachments[attachment.type].push(attachment);
        },
        removeAttachmentByIndex: function (type, index) {
            this.attachments[type].splice(index, 1);
        },
        removeAttachment: function (attachment) {
            let attachmentIndex = this.findIndexByElementKey(this.attachments[attachment.type], 'value', attachment.value);

            if (attachmentIndex !== -1) {
                this.removeAttachmentByIndex(attachment.type, attachmentIndex);
            }
        },
        clearAttachments: function () {
            this.attachments = {
                image: [],
                link: [],
                youtube: [],
            };

            bus.$emit('attachments-cleared', '123');
        },

        /*
         * Tooltips on like buttons
         */

        initToolTips: function () {
            const self = this;

            $('.button-tooltip-wrapper').hover(function (e) {
                let tooltip = $(this).next('.tooltip');

                self.initTooltipPosition($(this), tooltip);

                tooltip.addClass('in').show();
            }, function () {
                $(this).next('.tooltip').removeClass('in').hide();
            });
        },
        initTooltipPosition: function (buttonWrapper, tooltip) {
            let button, buttonOffset, leftOffset, topOffset;

            button = buttonWrapper;

            buttonOffset = button.offset();

            leftOffset = (buttonOffset.left) - (tooltip.width() / 2) + (button.width() / 2);
            topOffset = buttonOffset.top + button.height();

            tooltip.offset({
                left: 0,
                top: 0
            });

            tooltip.offset({
                left: leftOffset,
                top: topOffset
            });
        }
    }
});