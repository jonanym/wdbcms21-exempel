
/**
 * onLoad-funktion, körs när sedan är färdigt laddad.
 * Event-handlers och sådant som är beroende av DOM-element är bra att ha i här
 */
$(function() {
    console.log('jQuery works!');

    $(document).on("change", "#out", function() {
        var guestId = $(this).val(); // .val() => värdet på form-field
        
        getBookings(guestId);
    });

    $(document).on('click', '#save', function() {

        var guestId = $("#out").val();

        createBooking(guestId);

    });

    getGuests(); // Hämta alla gäster
}); 


/** 
 * Övriga funktioner kan vara utanför onLoad-funktionen:
 * */
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

        }
    });
}

function createBooking(guestid) {
    
    $("#save_status").html(''); // Töm förra save-status

    /**
     * POST-request med vår request body (payload) som json 
     * observera guestid i slutet av urlen
     */
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

