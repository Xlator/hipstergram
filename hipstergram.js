lastPicId = 0;
$.ajaxSetup({
    type: "GET",
    dataType: "json",
})

loadSettings = function() {
    $.ajax({
        url: "settings.json",
        success: function(r) {
            $('body').prepend($('<h1/>', { text: r.tags }));
            $('title').text($('title').text() + " " + r.tags + " ::");
        }
    });
}

getRecent = function() { 
    var data = {};

    if(lastPicId != 0) {
        data.last = lastPicId;
    }

    
    $.ajax({
        url: "recent.json",
        data: data,
        success: function(r) {
            if(r != null && r.length > 0) {
                var length = $('ul').find('li').length;
                window.lastPicId = r[0].id;
                console.log('Adding '+r.length+' images');

                $.each(r, function(k, v) {
                    row = $('<li/>', { data: { id: v.id } });
                    $('<img/>', { src: v.imageUrl, class: "grayscale"})
                    .load(function() {
                        console.log($(this));
                        row.on('click', getTweet);
                        if(length == 0)
                            $('ul').append(
                                $('<li/>', { data: { id: v.id } }).append($(this).fadeIn()).on('click', getTweet)
                            );
                        else
                            $('li:gt(70)').remove();
                            $('ul').prepend(
                                $('<li/>', { data: { id: v.id } }).append($(this).fadeIn(500)).on('click', getTweet)
                            );
                    });
                });
            }
            queryTwitter();
        }
    });
}

queryTwitter = function() {
    $.ajax({
        url: "query",
        success: function() {
            getRecent();
        },
        error: function() {
            console.log("Error while querying Twitter/Instagram. Retrying in 5 secs...");
            setTimeout(queryTwitter, 5000);
        }
    })
}

getTweet = function(e) {
    var id = $(this).data('id');
    $(this).find('img').removeClass('grayscale');
    $('li').not($(this)).find('img').addClass('grayscale');
    $.ajax({
        url: "tweet.json",
        data: { tweet: id },
        success: showTweet
    });
}

showTweet = function(tweet) {
    $('div').html('');
    template = "\
        <h2><img src={{profileImageUrl}} alt={{profileUsername}} /> {{profileUsername}}</h2> \
        <img src={{imageUrl}} class=pic /><p>{{text}}</p> \
    ";
    $('div').append($(Mustache.to_html(template, tweet[0])));
}

loadSettings();
getRecent();
