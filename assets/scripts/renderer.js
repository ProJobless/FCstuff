function renderFriends(response)
{
    $('#chat p.information').hide();

    var friendsCount = 0;

    $('#chat .online .items').html('');
    $('#chat .offline .items').html('');

    for (var i = 0; i < response.friends.length; i++) {
        friend = response.friends[i];

        if (friend.status == 'friends') {
            image = global_json.base_url + 'user-content/' + friend.friend_id + '/' + friend.profile_picture;
            html = "<div class='item' data-friend-id=" + friend.friend_id + " onclick='showConversation(); fetchConversation(" + friend.friend_id + ", false)'><img src='" + image + "' width='28' height='28'><strong>" + friend.name + "</strong></div>";

            last_seen = new Date(friend.last_seen + ' UTC');
            now = new Date();
            diff = (now.getTime() - last_seen.getTime()) / 1000;

            if (diff < 10) {
                $('#chat .online .items').append(html);
            } else {
                $('#chat .offline .items').append(html);
            };

            $('#chat .online .item').attr('title', 'Online');
            $('#chat .offline .item').attr('title', 'Offline');

            friendsCount += 1;
        };
    }; 

    if (friendsCount < 1) {
        $('#chat p.information').show().html("You don't have any friends yet &hellip;");
    };
}

function renderNotifications(response, update)
{
    $('#notifications p.information').hide();
    
    for (var i = 0; i < response.notifications.length; i++) {
        notification = response.notifications[i];

        if (notification.image) {
            image = global_json.base_url + 'user-content/' + notification.image;
        } else {
            image = global_json.base_url + 'user-content/' + user_json.user_id + '/' + user_json.profile_picture;
        };

        link = global_json.base_url + notification.link;
        html = "<a class='item ajax' href=" + link + " data-notification-id='" + notification.notification_id + "'><img src='" + image + "' width='36' height='36'><p>" + notification.content + "</p></a>";

        if (update) {
            $('#notifications .items').prepend(html);
        } else {
            $('#notifications .items').append(html);
        };

        if (notification.seen == 0) {
            newNotificationsCount = parseInt($('#tabs .notifications span').html());
            newNotificationsCount += 1;
            $('#tabs .notifications span').show().html(newNotificationsCount);
        };
    };
}

function renderRecentConversations(response)
{
    $('#aside .recent').show();

    for (var i = 0; i < response.messages.length; i++) {
        message = response.messages[i];

        id = message.friend_id;
        content = message.message;
        name = message.name;
        image = global_json.base_url + 'user-content/' + id + '/' + message.profile_picture;

        html = "<div class='item new' data-friend-id='" + id + "' onclick='showConversation(); fetchConversation(" + id + ")'><img src='" + image + "' height='36' width = '36'><strong>" + name + "</strong><p>" + content + "</p></div>";

        div = $("#aside .recent .item[data-friend-id='" + id + "']");

        if (div.length) {
            div.remove();
        };

        $('#aside .recent .items').prepend(html);
    };
}

function renderConversation(friendJSON, response, more)
{
    $('#conversation .top h2').html(friendJSON.user.name);

    if (response.success == true) {
        if (more) {
            lastMessageId = $('#conversation .messages .message:first-child').attr('data-message-id');
            for (var i = 0; i < response.conversation.length; i++) {
                message = response.conversation[i];

                prepMessage(message);

                $('#conversation .messages').prepend(html);
                $('#conversation').nanoScroller({ scrollTo: $("#conversation .messages .message[data-message-id='" + lastMessageId + "']").prev() });
            };

        } else {
            $('#conversation .messages').html('');
            for (var i = response.conversation.length; i > 0; i--) {
                message = response.conversation[i-1];

                prepMessage(message);

                $('#conversation .messages').append(html);

            };
        };
    }
}

function prepMessage(message)
{
    type = message.type;
    content = message.message;
    id = message.message_id;

    if (type == 'sent') {
        image = global_json.base_url + 'user-content/' + user_json.user_id + '/' + user_json.profile_picture;
        html = "<div class='message sent' data-message-id='" + id + "'><img src='" + image + "' height='36' width='36' class='left'><div class='arrow left'><div class='left-arrow'></div></div><p class='left'>" + content + "</p></div>";
    } else {
        image = global_json.base_url + 'user-content/' + friendJSON.user.user_id + '/' + friendJSON.user.profile_picture;
        html = "<div class='message received' data-message-id='" + id + "'><img src='" + image + "' height='36' width='36' class='right'><div class='arrow right'><div class='right-arrow'></div></div><p class='right'>" + content + "</p></div>";
    };
}

function renderNewMessages(friendJSON, response)
{
    for (var i = 0; i < response.conversation.length; i++) {
        message = response.conversation[i];

        type = message.type;
        content = message.message;
        id = message.message_id;

        image = global_json.base_url + 'user-content/' + friendJSON.user.user_id + '/' + friendJSON.user.profile_picture;
        html = "<div class='message received' data-message-id='" + id + "'><img src='" + image + "' height='36' width='36' class='right'><div class='arrow right'><div class='right-arrow'></div></div><p class='right'>" + content + "</p></div>";

        $("#conversation").nanoScroller();

        if (type == 'received') {
            $('#conversation .messages').append(html);
            $('#conversation').nanoScroller({ scroll: 'bottom' });
        };
    }
}