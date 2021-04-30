// rest-test js

$(function() {
    
    $(document).on("click", "#login", function() {

        /**
         * Inloggnings-request, vi skickar användarnamn och lösenord och får en API Key i utbyte.
         */
        $.ajax({
            type: "POST",
            url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/auth/",
            data: JSON.stringify({ 
                "username": $("#user").val(), 
                "password": $("#passwd").val() 
            }),
            success: function(data) {
                console.log(data);
                // Vi sparar apikey i localstorage
                localStorage.setItem('apikey', data.api_key);
            }
        });
    })

    $(document).on("click", "#get", function() {

        /** 
         * GET-request för att få våra personliga hotellbokningar via vår API Key.
         * Vi skickar API Keyn som en custom header 'apikey'
         */
        $.ajax({
            type: "GET",
            url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/mybookings/",
            headers: { 'apikey': localStorage.getItem('apikey') }, 
            success: function(data) {
                // Dumpa ut hotellbokningarna i loggen
                console.log(data.result);
            }
        });
    })




});
