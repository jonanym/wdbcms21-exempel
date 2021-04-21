
$(document).ready(function () {
    
    getIp();

});


function getIp() {

    $.get("https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/ip/", function(data) {
        console.log(data.ip);
        
        $("#ip").html(data.ip);
    });

    
}
