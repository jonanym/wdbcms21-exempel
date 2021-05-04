// rest-test js

$(function() {
    console.log('jQuery works!');


    $(document).on("click", ".guest", function() {
        getBookings($(this).attr("id"));
        //console.log($(this).attr("id"));
    });

    $.ajax({
        type: "GET",
        url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/methodtest/",
        success: function(data) {

            console.log(data.result);
            $.each(data.result, function(i, value) {
                    //console.log(value);
                    $("#out").append('<li class="guest" id="' + value.guestid + '">'
                        + value.firstName 
                        + ' (' + value.bookings + ' bookings)'
                        + '</li>');
            });
            //console.log(data);
        },
        error: function() {

        }
    });

});

function getBookings(guestid) {
    // 
    console.log(guestid);
}



console.log('js works');