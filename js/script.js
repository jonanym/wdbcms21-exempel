
var jsonObj = { name: "Bo", age: 5, toys: ["car", "doll"]};

$(function() {



    $.ajax({
        type: "POST",
        url: "https://fw-teaching.fi/demo/wdbocms/api/v1/products/", 
        data: JSON.stringify({ name : "Gloves", price: 24.90 }),
        dataType: "json",
        success: function(data) {
            console.log(data);
            listProducts();
        },
    });
});


function listProducts() {
    $.ajax({
        type: "GET",
        url: "https://fw-teaching.fi/demo/wdbocms/api/v1/products/", 
        success: function(data) {
            console.log(data);
        },
    });
}
