// rest-test js

$(function() {
    console.log('jQuery works!');


    $(document).on("click", ".guest", function() {

        // $(this) är ett jQuery-objekt för det element vi klickat på
        // attr() är en metod för att hämta ett html-attribut
        var guestId = $(this).attr("id");
        
        getBookings(guestId);
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
    // Här kan vi köra en GET-request för en specifik guestid
    console.log(guestid);
}



console.log('js works');