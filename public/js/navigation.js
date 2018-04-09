// Bind the navigation page

$('#nav_top').click(function(){
    show_topic('top');
});

$('#nav_hot').click(function(){
    show_topic('hot');
});

$('#nav_new').click(function(){
    show_topic('new');
});

// Intercept external calls to support custom logic prior to leaving page
function call_external_url(user_url)
{
    window.open(user_url, '_blank');
}