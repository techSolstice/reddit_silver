var posts_per_page = 5;

// Bind the Next Page button
$('#pagination_next a').click(function(){
    next_page();
});

// Show only the posts for the passed in page
function show_page(page_number)
{
    var initial_post_num = (page_number - 1) * posts_per_page + 1;
    $("div#thread>ul").css('display', 'none');

    for (let post_num = 0; post_num < posts_per_page; post_num++)
    {
        $("div#thread>ul:nth-child(" + (initial_post_num + post_num) + ")").css('display', 'block');
    }
}

// Show the posts for the next page
function next_page(){
    var current_page = +($('#current_page_number').text());
    show_page(current_page + 1);
    set_active_page(current_page + 1);
}

// Show the posts for the previous page
function previous_page(){
    var current_page = +($('#current_page_number').text());
    show_page(current_page - 1);
    set_active_page(current_page - 1);
}

// Set the navigation bar based on the passed in page
function set_active_page(page_number){
    var total_pages = Math.ceil(obtain_total_posts() / posts_per_page);

    // Check and disable Previous link
    if (page_number < 2)
    {
        $('#pagination_previous a').addClass('disabled').off("click");
    }else{
        if ($('#pagination_previous a').hasClass('disabled')) {
            $('#pagination_previous a').removeClass('disabled').click(function () {
                previous_page();
            });
        }
    }

    //Check and disable Next link
    if (page_number + 1 > total_pages)
    {
        $('#pagination_next a').addClass('disabled').off("click");
    }else
    {
        if ($('#pagination_next a').hasClass('disabled')) {
            $('#pagination_next a').removeClass('disabled').click(function () {
                next_page();
            });
        }
    }

    //Set current page
    $('#current_page_number').text(page_number);
}

// How many posts do we have?  Useful for pagination
function obtain_total_posts(){
    return $('div#thread>ul').length;
}

