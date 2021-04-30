// rest-test js

var localApiKey;

$(function() {
    
    $(document).on("click", "#login", function() {

        $.ajax({
            type: "POST",
            url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/auth/",
            data: JSON.stringify({ 
                "username": $("#user").val(), 
                "password": $("#passwd").val() 
            }),
            success: function(data) {
                console.log(data);
                localStorage.setItem('apikey', data.api_key);
                localApiKey = data.api_key;
            },
            error: function() {

            }
        });
    })

    $(document).on("click", "#get", function() {

        $.ajax({
            type: "GET",
            url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/mybookings/",
            headers: { 'apikey': localStorage.getItem('apikey') },
            success: function(data) {
                console.log(data);
            },
            error: function() {

            }
        });
    })




});
