$(document).ready(function(){
    $('#user').click( function(event){
        showDropdown();
        event.stopPropagation();
    });

    $("#aside").nanoScroller({
        preventPageScrolling: true
    });

    $(document).click(function(){
        hideDropdown();
    });

    fetchFriends();
    setInterval("fetchFriends()", 10000);

    fetchNotifications();
    setInterval("fetchNotifications(true)", 10000);

    fetchRecentConversations();
    setInterval("fetchRecentConversations(true)", 10000);

    setInterval("fetchNewMessages()", 5000);

    $('#aside .tab').click(function() {
        $("#aside").nanoScroller();
    });

    $("#conversation").bind("scrolltop", function(e){
        fetchConversation($('#conversation').attr('data-friend-id'), true);
    });

    $('#conversation textarea').keydown(function(event) {
        if (event.keyCode == 13) {
            if ($('#conversation textarea').val() != '') {
                sendMessage();
                $('#conversation textarea').val('').focus();
            };
            return false;
         }
    });

    switch(global_json.content_type) {
        case 'feed':
            fetchFeed();
    }
});

$(window).scroll(function() {
    if($(window).scrollTop() + $(window).height() > $(document).height() - 50) {
        fetchFeed(true);
    }
});

function showDropdown()
{
    hideDropdown();
    $('#dropdown').toggle();
    $('#dropdown').width($('#user').width() + 13.5);
    $('#user').css('background-color', '#8E031C');
}

function hideDropdown()
{
    $('#dropdown').hide();
    $('#user').css('background-color', '');
}

function showChat()
{
    $('#tabs .chat').addClass('active');
    $('#tabs .notifications').removeClass('active');
    $('#chat').show();
    $('#notifications').hide();
    $("#aside").nanoScroller({
        scroll: 'top'
    });
    $('#tabs .notifications span').hide().html('0');
}

function showNotifications()
{
    $('#tabs .notifications').addClass('active');
    $('#tabs .chat').removeClass('active');
    $('#notifications').show();
    $('#chat').hide();
    $("#aside").nanoScroller({
        scroll: 'top'
    });
    $('#tabs .notifications span').hide().html('0');
}

function showConversation()
{
    $('#conversation').show().animate({
        width:'310px'
    }, 250, function(){
        $('#conversation > .content').css('display', 'block');
        $("#conversation").nanoScroller();
        $("#conversation").nanoScroller({
            scroll: 'bottom'
        });
        $('#conversation textarea').focus();
        $('#conversation').css('border-right', '1px solid #787878');
        $('#conversation textarea').autosize();
    });

    $('#aside').animate({
        left: '310px'
    }, 250);
}

function hideConversation()
{
    $('#conversation > .content').css('display', 'none');
    $('#conversation').animate({
        width:0
    }, 250, function(){
        $('#conversation').css('display', 'none');
    });

    $('#aside').animate({
        left: '0'
    }, 250);
}

function fetchFriends()
{
    var url = global_json.base_url + 'friends/read';

    $.post(url, function(response) {

        if (response.success == true) {
            renderFriends(response);
        } else {
            $('#chat p.information').show().html("You don't have any friends yet &hellip;");
        };

    }, "json");
}

function fetchNotifications(update)
{
    var firstNotificationId = $('#notifications .items .item:first-child').attr('data-notification-id');

    var url = global_json.base_url + 'notifications/read';
    
    $.post(url, {'after' : firstNotificationId}, function(response) {
        if (response.success == true) {
            renderNotifications(response, update);
        };

    }, "json");
}

function fetchRecentConversations()
{
    var url = global_json.base_url + 'conversations/unread';
    $.post(url, function(response) {
        if (response.success == true) {
            renderRecentConversations(response);
        };
    }, "json");
}

function fetchConversation(user_id, more)
{
    var url = global_json.base_url + 'users/read/' + user_id;
    $("#aside .recent .item[data-friend-id='" + user_id + "']").removeClass('new');
    $('#conversation').attr('data-friend-id', user_id);
    
    $.post(url, function(response) {
        friendJSON = response;
        if (more) {
            lastMessageId = $('#conversation .messages .message:first-child').attr('data-message-id');
        } else {
            lastMessageId = null;
        };
        
        var url = global_json.base_url + 'conversations/read/';
        $.post(url, {'friend_user_id' : user_id, before : lastMessageId}, function(response){
            renderConversation(friendJSON, response, more);
        }, "json");
    }, "json");
}

function fetchNewMessages()
{
    if ($('#conversation').is(":visible")) {
        $("#conversation").nanoScroller();
        user_id = $('#conversation').attr('data-friend-id');
        var url = global_json.base_url + 'users/read/' + user_id;

        $("#aside .recent .item[data-friend-id='" + user_id + "']").removeClass('new');
        
        $.post(url, function(response) {
            friendJSON = response;

            firstMessageId = $('#conversation .messages .received:last').attr('data-message-id');

            var url = global_json.base_url + 'conversations/read/';
            $.post(url, {'friend_user_id' : user_id, after : firstMessageId}, function(response){
                if (response.success == true) {
                    renderNewMessages(friendJSON, response);
                }
                fetchRecentConversations();
            }, "json");

        }, "json");
    };
}

function sendMessage()
{
    message = $('#conversation textarea').val();
    image = global_json.base_url + 'user-content/' + user_json.user_id + '/' + user_json.profile_picture;
    friend_id = $('#conversation').attr('data-friend-id');

    html = "<div class='message sent'><img src='" + image + "' height='36' width='36' class='left'><div class='arrow left'><div class='left-arrow'></div></div><p class='left'>" + message + "</p></div>";

    $('#conversation .messages').append(html);
    $("#conversation").nanoScroller();
    $('#conversation').nanoScroller({ scroll: 'bottom' });

    var url = global_json.base_url + 'conversations/send/';
    $.post(url, {
        'friend_user_id' : friend_id,
        'message' : message
    });
}

function fetchFeed(update)
{
    if (update) {
        last_post_id = $('#content .feed .post:last').attr('data-post-id');
    } else {
        last_post_id = null;
        $('#content').html('').append("<div class='loader'>Loading &hellip;").append("<div class='feed'></div>");
    };

    var url = global_json.base_url + 'posts/feed';
    $.post(url, {'last_post_id' : last_post_id}, function(response){
        if (response.success == true) {
            $('#content .loader').remove();
            renderFeed(response, update);
            $('#content').append("<div class='loader'>Loading more posts &hellip;");
        } else {
            $('#content .loader').remove();
            $('#content').append("<div class='loader'>There are no more posts &hellip;");
        }
    }, "json");
}