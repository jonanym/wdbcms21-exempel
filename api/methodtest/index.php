<?php 

header('content-type: application/json');

// Spara URL-variablerna ur query string i array $request_vars
parse_str($_SERVER['QUERY_STRING'], $request_vars);

// Hämta data fråm request body och spara i array $request_body
$request_json = file_get_contents('php://input');
$request_body = json_decode($request_json);

$response = [ 
    "request_method" => $_SERVER['REQUEST_METHOD'],
    "request_body" => $request_body,
    "query_string" => $_SERVER['QUERY_STRING'],
    "request_vars" => $request_vars

];

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $response["result"] = "Success POST";
    // spara grejer i databasen
    // SQL insert into products
} elseif ($_SERVER['REQUEST_METHOD'] == "GET" && isset($request_vars["id"])) {
    // Hämta detaljer för ett enskilt objekt (t.ex. produkt)
    $response["result"] = "Success GET id = " . $request_vars["id"];
}


echo json_encode($response);

//$_SERVER['REQUEST_METHOD'];
//echo '{ "' . $_SERVER['REQUEST_METHOD'] . '" }';

