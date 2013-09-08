<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>FCstuff - Make more friends!</title>

    <meta name="description" content="FCstuff is a brand new social-networking utility to help you make more friends.">
    <meta name="viewport" content="width=device-width">

    <link rel="shortcut icon" href="<?php echo base_url('favicon.ico?ver=1'); ?>">

    <link rel="stylesheet" href="<?php echo base_url('assets/stylesheets/reset.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/stylesheets/landing.css'); ?>">

    <script src="<?php echo base_url('assets/scripts/jquery-2.0.3.js'); ?>"></script>
    <script src="<?php echo base_url('assets/scripts/landing.js'); ?>"></script>
</head>
<body>
    <?php $continue = ($this->input->get('continue') ? '?continue=' . $_GET['continue'] : '');?>

    <div id="header">
        <h1><a href="<?php echo base_url(); ?>">FCstuff</a></h1>
        <button tabindex="1" onclick="showLoginModal()">Login</button>
    </div>

    <div id="content">
        <h1>Haven't joined <span>FCstuff</span> yet?</h1>
        <form autocomplete="off" method="POST" action="<?php echo base_url('users/create') . $continue ?>">
            <?php echo ($this->session->flashdata('name_invalid') ? "<div class='error'>You can't use this name.</div>" : ''); ?>
            <input tabindex="2" name="name" type="text" placeholder="Your Name" autocomplete="off" value="<?php echo $this->session->flashdata('name'); ?>" required>
            <?php echo ($this->session->flashdata('email_invalid') ? "<div class='error'>You can't use this email address.</div>" : ''); ?>
            <input tabindex="3" name="email" type="email" placeholder="Your Email Address" autocomplete="off" value="<?php echo $this->session->flashdata('email'); ?>" required>
            <?php echo ($this->session->flashdata('password_invalid') ? "<div class='error'>Your password is too short.</div>" : ''); ?>
            <input tabindex="4" name="password" type="password" placeholder="Choose A Password" autocomplete="off" required>
            <?php echo ($this->session->flashdata('captcha_invalid') ? "<div class='error'>You haven't entered the correct text from the image.</div>" : ''); ?>
            <div class="captcha" onclick="getNewCaptcha()" title="Click here to get a new image."><img id="captcha" src="<?php echo base_url('captcha'); ?>"></div>
            <input tabindex="5" name="captcha" type="text" placeholder="Enter Text From The Image" autocomplete="off" required>
            <button tabindex="6">Create my account!</button>
        </form>
    </div>

    <div id="overlay" onclick="hideLoginModal()"></div>

    <div id="modal">
        <h2>Welcome back!</h2>
        <form autocomplete="off" method="POST" action="<?php echo base_url('users/login') . $continue ?>">
            <input name="identifier" id="identifier" type="text" placeholder="Email Address" autocomplete="off" required>
            <input name="password" id="password" type="password" placeholder="Password" autocomplete="off" required>
            <label><input name ="remember" id="remember" type="checkbox" checked> Remember me</label>
            <?php echo ($this->session->flashdata('login_failed') ? "<div class='error'>The email or password you entered was incorrect.</div>" : ''); ?>
            <button>Login</button>
        </form>
    </div>
</body>
</html>