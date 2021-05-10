// rest-test js

$(function() {
    console.log('jQuery works!');

    $(document).on("change", "#out", function() {

        // $(this) är ett jQuery-objekt för det element vi klickat på
        // attr() är en metod för att hämta ett html-attribut
        var guestId = $(this).val(); // .val() => värdet på form-field
        
        getBookings(guestId);
    });

    $(document).on('click', '#save', function() {

        var guestid = $("#out").val();

        createBooking(guestid);

    });

    getGuests();
});

function getBookings(guestid) {
    // Här kan vi köra en GET-request för en specifik guestid
    console.log(guestid);
    $.ajax({
        type: "GET",
        url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/methodtest/" + guestid,
        success: function(data) {
            console.log(data);
            // #C0001
            $("#bookings").html('');
            $.each(data.result, function(i, value) {
                $("#bookings").append("<div>" 
                    + " Rum: " + value.hotelroom  
                    + " Anländer: " + value.datefrom 
                    + "</div>");
            });
        }
    });    
}

function getGuests() {
    $.ajax({
        type: "GET",
        url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/methodtest/",
        success: function(data) {

            console.log(data.result);
            $.each(data.result, function(i, value) {
                    //console.log(value);
                    $("#out").append('<option class="guest" id="' + value.guestid + '" value="'+value.guestid +'">'
                        + value.guestid + " " 
                        + value.firstName 
                        + ' (' + value.bookings + ' bookings)'
                        + '</option>');
            });
            //console.log(data);
        }
    });
}

function createBooking(guestid) {
    
    $("#save_status").html('');

    $.ajax({
        type: "POST",
        url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/methodtest/" + guestid,
        data: JSON.stringify({ 
            room: $("#room").val(), 
            datefrom: $("#startdate").val(), 
            dateto: $("#enddate").val(), 
            comment: $("#comment").val()
        }),
        dataType: "json",
        success: function(data) {
            console.log(data);

            if (data.booking_saved) {
                $("#save_status").html("Booking saved!");
            }
            getBookings(guestid);
        }
    }); 
}


console.log('js works');