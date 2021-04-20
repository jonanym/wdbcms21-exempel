
$(document).ready(function () {
    
    getIp();

});

var foo = { name: "Bo", age: 5 };

function getIp() {

    $.get("https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/ip/", function(data) {
        console.log(data.ip);
        
        $("#ip").html(data.ip);
    });

    
}
