<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title><?php echo $title; ?></title>

    <meta name="description" content="<?php echo $description; ?>">
    <meta name="viewport" content="width=device-width">

    <link rel="shortcut icon" href="<?php echo base_url('favicon.ico?ver=1'); ?>">

    <link rel="stylesheet" href="<?php echo base_url('assets/stylesheets/reset.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/stylesheets/main.css'); ?>">

    <script src="<?php echo base_url('assets/scripts/jquery-2.0.3.js'); ?>"></script>
    <script src="<?php echo base_url('assets/scripts/jquery.nanoscroller.js'); ?>"></script>
    <script src="<?php echo base_url('assets/scripts/jquery.autosize.js'); ?>"></script>
    <script>
        var global_json = <?php echo $json['global']; ?>;
        var user_json   = <?php echo $json['user']; ?>;
    </script>
    <script src="<?php echo base_url('assets/scripts/renderer.js'); ?>"></script>
    <script src="<?php echo base_url('assets/scripts/main.js'); ?>"></script>
</head>
<body>
    <div id="header">
        <div id="logo"><a href="<?php echo base_url(); ?>"><h1>FCstuff</h1></a></div>
        <div id="user">
            <img src="<?php echo base_url('user-content/' . $user['user_id'] . '/' . $user['profile_picture']); ?>" height="32" width="32">
            <span><?php echo $user['name']; ?></span>
        </div>
        <div id="dropdown" class="dropdown">
            <a class="item ajax" href="<?php echo base_url() ?>">View Profile</a>
            <a class="item" href="<?php echo base_url('users/logout') ?>">Logout</a>
        </div>
    </div>
    <div id="aside" class="scrollbar">
        <div class="content">
            <div id="tabs">
                <div class="tab chat active" onclick="showChat()">Chat<span></span></div>
                <div class="tab notifications" onclick="showNotifications()">Notifications<span>0</span></div>
            </div>
            <div id="chat">
                <p class="information">Loading &hellip;</p>
                <div class="recent"><div class="items"></div><hr></div>
                <div class="online"><div class="items"></div></div>
                <div class="offline"><div class="items"></div></div>
            </div>
            <div id="notifications">
                <p class="information">Loading &hellip;</p>
                <div class="items"></div>
            </div>
        </div>
    </div>
    <div id="conversation" class="scrollbar">
        <div class="content">
            <div class="top">
                <h2>Loading &hellip;</h2>
                <div class="exit" onclick="hideConversation()">x</div>
            </div>
            <div class="messages"></div>
            <textarea placeholder="Write your message and press enter &hellip;"></textarea>
        </div>
    </div>
</body>
</html>