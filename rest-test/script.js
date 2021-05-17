
/**
 * onLoad-funktion, körs när sedan är färdigt laddad.
 * Event-handlers och sådant som är beroende av DOM-element är bra att ha i här
 */
$(function() {
    console.log('jQuery works!');

    $(document).on("change", "#out", function() {
        var guestId = $(this).val(); // .val() => värdet på form-field
        
        $("#booking-id").val(0);
        $("#save").html("Boka");

        getBookings(guestId);
    });

    $(document).on('click', '#save', function() {

        var guestId = $("#out").val();
        var bookingId = $("#booking-id").val();

        if (bookingId == 0) {
            createBooking(guestId);
        } else {
            updateBooking(bookingId);
        }
        

    });

    $(document).on('click', '.edit-booking', function() {

        var bookingId = $(this).attr("data-booking-id");

        $("#save").html("Spara ändringar");

        console.log(bookingId);

        editBooking(bookingId);

    });

    $(document).on('click', '.del-booking', function() {

        var bookingId = $(this).attr("data-booking-id");

        if (confirm("Vill du radera bokning " + bookingId + "?")) {
            console.log("DELETE " + bookingId);
            delBooking(bookingId);
        }

    
        //editBooking(bookingId);

    });

    getGuests(); // Hämta alla gäster
}); 


/** 
 * Övriga funktioner kan vara utanför onLoad-funktionen:
 * */
var currentBookings = {}; 
function getBookings(guestid) {

    currentBookings = {};
    
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

                currentBookings[value.bookingid] = {
                    hotelroom: value.hotelroom,
                    datefrom: value.datefrom,
                    dateto: value.dateto,
                    addinfo: value.addinfo
                }

                $("#bookings").append("<div>" 
                    + " ID: " + value.bookingid  
                    + " Rum: " + value.hotelroom  
                    + " Anländer: " + value.datefrom 
                    + " Åker hem: " + value.dateto 
                    + " Önskemål: " + value.addinfo 
                    + ' <span class="edit-booking" data-booking-id="'+ value.bookingid +'">[Edit]</span>'
                    + ' <span class="del-booking" data-booking-id="'+ value.bookingid +'">[Delete]</span>'
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

function editBooking(bookingId) {

    var booking = currentBookings[bookingId];

    $("#booking-id").val(bookingId);
    $("#room").val(booking.hotelroom);
    $("#startdate").val(booking.datefrom);
    $("#enddate").val(booking.dateto);
    $("#comment").val(booking.addinfo);

}

function updateBooking(bookingId) {

    var guestId = $("#out").val();

    console.log("PUT-request: update booking " + bookingId);

    $.ajax({
        type: "PUT",
        url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/methodtest/?bookingId=" + bookingId,
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
            getBookings(guestId);
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
                $("#save_status").html("Booking saved, new bookingid:" + data.new_id);
            }
            getBookings(guestid);
        }
    }); 
}

function delBooking(bookingId) {

    var guestId = $("#out").val();

    $.ajax({
        type: "DELETE",
        url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/methodtest/?bookingId=" + bookingId,
        success: function(data) {

            console.log(data.result);

            getBookings(guestId);

        }
    });
}
