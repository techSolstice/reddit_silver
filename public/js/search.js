// Display search results based on Search Text
function perform_search(){
    hide_headlines();
    $.get('/api/search/' + $('#search_input').val(), function(data){redraw_page(data)});
    $('#nav_button').click();
}

// Display a given topic (e.g. Hot, New)
function show_topic(topic_name){
    hide_headlines();
    $.get('/api/topic/' + topic_name, function(data){redraw_page(data)});
    $('#nav_button').click();
}

// Supports an advanced search for limit by subreddit
function search_subreddit(){
    $.get('/api/subreddit/' + $('#search_input').val(), function(data){redraw_page(data)})
}

// Hide the News headlines
function hide_headlines()
{
    $('#headlines').hide();
}

// Show the News headlines - currently linking to Home however
function show_headlines()
{
    $('#headlines').show();
}

// Render the page in JavaScript using a JSON string
function redraw_page(data)
{
    $('#thread').hide();
    $('#page_pagination').hide();

    parsed_json = create_posts(data);
    number_posts = populate_posts(parsed_json);
    paginate_posts();
    create_pagination_bar(number_posts);
    $(document).foundation();

    $('#thread').show();
    $('#page_pagination').show();
}

// Create the HTML for each Post in a Thread
function create_posts(post_json)
{
    var parsed_json = JSON.parse(post_json);
    var html_string = '';
    $.each(parsed_json, function (jsonkey, jsonval) {
            html_string += '<ul class="thread accordion" data-accordion data-allow-all-closed="true">';
            html_string += '<li class="accordion-item" data-accordion-item>';
            html_string += '<a href="#" class="accordion-title"><span class="stat post_score">-</span> points <div class="post_title"></div></a>';
            html_string += '<div class="accordion-content post_details" data-tab-content></div>';
            html_string += '</li></ul>';
    });

    $('#thread').html(html_string);

    return parsed_json;
}

// Populate the HTML with the content from Reddit's API
function populate_posts(parsed_json)
{
    let post_counter = 0;
    const REDDIT_HOST = 'https://www.reddit.com';

    $.each(parsed_json, function (jsonkey, jsonval) {
        let epoch_date = parsed_json[jsonkey]['date_created'];
        let date_object = new Date(epoch_date*1000);
        let string_date =  date_object.getMonth() + '/' + date_object.getDate() + '/' + date_object.getFullYear() + ' ' + date_object.getHours() + ':' +  date_object.getMinutes();
        post_counter++;

        $('.post_score', $('#thread>ul:nth-child(' + post_counter + ')')).text(
            parsed_json[jsonkey]['score']
        );
        $('.post_title', $('#thread>ul:nth-child(' + post_counter + ')')).html(
            '<div><a href="#" onclick="call_external_url(\'' + REDDIT_HOST + parsed_json[jsonkey]['permalink'] + '\')" class="button large" target="_blank">' + parsed_json[jsonkey]['title'] + '</a></div>'
        );
        $('.post_details', $('#thread>ul:nth-child(' + post_counter + ')')).html(
            'Posted on ' + string_date + ' by ' + parsed_json[jsonkey]['author'] + '<br><img src="/img/stock1.jpeg">'
        );
    });

    return post_counter;
}

// Hide any of the posts we don't want to display for user-friendly pagination
function paginate_posts()
{
    $('#thread>ul').each(function(index){
        if (index >= 5)
        {
            $(this).css('display', 'none')
        }
    })
}

// Create a navigation bar to allow user to navigate through posts
function create_pagination_bar(number_posts)
{
    let html_string = '';

    html_string = '<li class="pagination-previous" id="pagination_previous"><a href="#" class="disabled" aria-label="Previous page">Previous</a></li>';
    html_string += '<li class="current"><span class="show-for-sr">You\'re on page</span> <span id="current_page_number">1</span></li>';
    html_string += '<li class="pagination-next" id="pagination_next"><a href="#" aria-label="Next page">Next</a></li>';

    $('#page_pagination').html(html_string);

    if (number_posts > 5)
    {
        $('#pagination_next a').click(function () {
            next_page();
        });
    }else{
        $('#pagination_next a').addClass('disabled').off("click");
    }
}

// Bind click event to Search button
$('#search_button').click(function(){
    perform_search();
});
