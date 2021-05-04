<?php 

include("../../../../../local/mysql_env_real.php");
$mysqli = new mysqli("mysql.arcada.fi", MYSQLUSER, MYSQLPASS, MYSQLUSER);
if ($mysqli->connect_error) die("MySQL Connect ERROR:" . $mysqli->connect_error); 

header('content-type: application/json');
header("Access-Control-Allow-Methods: POST, PUT, GET, OPTIONS, DELETE");

// Spara URL-variablerna ur query string i array $request_vars
parse_str($_SERVER['QUERY_STRING'], $request_vars);

// Hämta data från request body och spara i array $request_body
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

} elseif ($_SERVER['REQUEST_METHOD'] == "GET" && isset($request_vars["id"])) {
    // Hämta detaljer för ett enskilt objekt (t.ex. produkt)
    $response["result"] = "Success GET id = " . $request_vars["id"];

    $stmt = $mysqli->prepare("SELECT
        g.firstname,
        g.lastname,
        b.hotelroom,
        b.datefrom
    FROM
        hotel_guest g
    INNER JOIN hotel_booking b ON
        g.guestid = b.guest
    WHERE g.guestid = ?
    ORDER BY b.datefrom DESC");

    if (!$stmt) {
        die("SQL ERROR: " . $mysqli->error);
    }

    $stmt->bind_param("s", $request_vars["id"]); 

    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }    

    $response["result"] = $rows;

} elseif ($_SERVER['REQUEST_METHOD'] == "GET" && !isset($request_vars["id"])) {


    $stmt = $mysqli->prepare("SELECT
        g.guestid,
        g.firstName,
        g.lastname,
        -- g.address,
        -- g.city,
        (select count(*) from hotel_booking where guest = g.guestid) as bookings
    FROM
        hotel_guest g");

    if (!$stmt) {
        die("SQL ERROR: " . $mysqli->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }    

    $response["result"] = $rows;
}

echo json_encode($response);



