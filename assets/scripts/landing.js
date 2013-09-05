function showLoginModal()
{
    $('#modal').css('display', 'block');
    $('#overlay').css('display', 'block');
    $('#identifier').focus();
    window.location.hash = '#login';
}

function hideLoginModal()
{
    $('#modal').css('display', 'none');
    $('#overlay').css('display', 'none');
    window.location.hash = '';
}

var url;

function getNewCaptcha()
{
    if ( ! url)
    {
        url = $('#captcha').attr("src");
    };

    $('#captcha').attr("src", url + '?' + Math.random());
}

$(document).ready(function(){
    if (window.location.hash == '#login')
    {
        showLoginModal();
    }
});

$(document).keyup(function(e) {
    if (e.keyCode == 27)
    {
        hideLoginModal();
    }
});