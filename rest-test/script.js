// rest-test js

$(function() {
    console.log('jQuery works!');

    $.ajax({
        type: "GET",
        url: "https://cgi.arcada.fi/~welandfr/demo/wdbcms21-exempel/api/methodtest/",
        success: function(data) {
            console.log(data);
        },
        error: function() {

        }
    });

});
console.log('js works');