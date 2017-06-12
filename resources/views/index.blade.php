@extends('layouts/master')

@section('content')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div id="app">
        <div class="container">
            <div class="row ">
                <h3 class="text-center">{{ config('app.name') }}</h3>
                <br/>
                <div class="col-md-9">
                    <div class="panel-group">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <label class="checkbox-inline">
                                    <input type="checkbox" v-model="scrollToTopOnNewMessage">
                                    Scroll to top on new messages
                                </label>
                            </div>
                            <div class="panel-body fixed-panel" id="chat">
                                <ul class="media-list">
                                    <li class="media" v-for="message in reversedMessages">
                                        <div class="media-body">
                                            <div class="media pull-right">
                                                <p class="pull-left">
                                                    <strong>@{{ message.author }} : </strong>
                                                </p>
                                                <div class="media-body message-content">
                                                    @{{ message.content }}
                                                    <br/>

                                                    <div class="message-attachments">
                                                        <ul>
                                                            <li v-for="image in message.attachment_images" class="image-item">
                                                                <img v-bind:src="image.image_url" class="attachment-image" />
                                                            </li>
                                                            <div class="clearfix"></div>
                                                        </ul>
                                                        <ul>
                                                            <li v-for="youtube in message.attachment_youtubes" class="youtube-item">
                                                                <youtube :video-id="youtube.video_id" player-width="230" player-height="150" :player-vars="{start: youtube.start_time}"></youtube>
                                                            </li>
                                                            <div class="clearfix"></div>
                                                        </ul>
                                                        <ul>
                                                            <li v-for="link in message.attachment_links" class="link-item">
                                                                <a v-bind:href="link.href" target="_blank">@{{ link.title }}</a>
                                                            </li>
                                                            <div class="clearfix"></div>
                                                        </ul>
                                                    </div>

                                                    <small class="text-muted">@{{ message.created_at }}</small>
                                                    <div class="pull-right message-buttons">
                                                        <button type="button"
                                                                class="btn btn-default btn-sm remove-message-button"
                                                                v-bind:id="'remove_message_button_' + message.id"
                                                                v-on:click="deleteMessage(message.id)"
                                                                v-if="authUser.id === message.user_id">
                                                            <span class="glyphicon glyphicon-remove remove-message"></span>
                                                            Remove
                                                        </button>

                                                        <div class="button-tooltip-wrapper">
                                                            <button type="button"
                                                                    class="btn btn-default btn-sm like-button"
                                                                    v-bind:id="'like_message_button_' + message.id"
                                                                    v-on:click.prevent="postLike(message.id)"
                                                                    v-bind:disabled="authUser.id === message.user_id || !checkNotLikedYetByAuthUser(message)">
                                                                <span class="glyphicon glyphicon-thumbs-up"></span> Like
                                                                (@{{ message.likes.length }})
                                                            </button>
                                                        </div>

                                                        <div v-if="message.likes.length"
                                                             class="tooltip bottom"
                                                             style="display: none;">
                                                            <div class="tooltip-arrow"></div>
                                                            <div class="tooltip-inner">
                                                                <span style="white-space: nowrap;">Liked by:</span>
                                                                <ol>
                                                                    <li class="like-item"
                                                                        v-for="like in getReversedLikes(message)">
                                                                        <strong>@{{ like.username }}</strong>
                                                                        <small>(@{{ like.created_at }})</small>
                                                                    </li>
                                                                </ol>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <hr/>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <div class="text-center" v-if="haveElseMessages">
                                    <button class="btn btn-info" v-on:click.prevent="showMoreMessages()">Show more
                                    </button>
                                </div>
                            </div>

                            <div class="panel-footer">
                                <form method="post" v-on:submit.prevent="postMessage()">
                                    <div class="input-group">
                                    <span class="input-group-btn">
                                        <button v-on:click.prevent="toggleAttachmentsMenu()"
                                                type="button"
                                                class="btn btn-info btn-sm attachments-button">
                                            <strong><span class="glyphicon glyphicon-paperclip"></span></strong>
                                </button>
                                    </span>
                                        <input type="text" class="form-control" placeholder="Enter Message"
                                               v-model="message"/>
                                        <span class="input-group-btn">
                                        <button v-bind:disabled="messageIsEmpty" class="btn btn-info" type="submit">SEND</button>
                                    </span>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="attachments_menu">
                            <attachments v-bind:attachments="attachments" v-show="showAttachmentsMenu"></attachments>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Online Users
                        </div>

                        <div class="panel-body fixed-panel">
                            <ul class="media-list" v-for="user in users">

                                <li class="media">
                                    <div class="media-body">
                                        <div class="media">
                                            <a class="pull-left" href="#">
                                            </a>
                                            <div class="media-body">
                                                <h5 v-if="authUser.id === user.id">
                                                    <strong>@{{ user.username }}</strong>
                                                    <small><a href="/logout">(Logout)</a></small>
                                                </h5>
                                                <h5 v-else>@{{ user.username }}</h5>
                                                <hr/>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('javascript')
    <script src="/js/app.js"></script>
@endsection



