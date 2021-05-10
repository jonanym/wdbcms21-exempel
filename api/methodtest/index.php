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

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($request_vars["id"])) {

    /* INSERT INTO hotel_booking (
    datefrom,
    dateto,
    guest,
    hotelroom,
    addinfo
 ) VALUES (
 	'2020-01-01',
    '2021-01-01',
    'C0001',
     404,
     'foo'
 )*/

    $stmt = $mysqli->prepare("INSERT 
        INTO hotel_booking (
            datefrom,
            dateto,
            guest,
            hotelroom,
            addinfo
        ) VALUES (
            ?,
            ?,
            ?,
            ?,
            ?
        )");

    if (!$stmt) {
        die("SQL ERROR: " . $mysqli->error);
    }

    $datefrom = $request_body->datefrom;
    $dateto = $request_body->dateto;
    $guest = strip_tags($request_vars["id"]);
    $room = $request_body->room;
    $comment = strip_tags($request_body->comment);

    $stmt->bind_param("sssis", 
        $datefrom,
        $dateto,
        $guest,
        $room,
        $comment
    );
    $stmt->execute();

    $response["result"] = "Success POST";
    $response["booking_saved"] = true;

/**
 *  GET methodtest/ID (specifik id)
 */
} elseif ($_SERVER['REQUEST_METHOD'] == "GET" && isset($request_vars["id"])) {

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


/**
 *  GET methodtest/ (returnera alla)
 */
} elseif ($_SERVER['REQUEST_METHOD'] == "GET" && !isset($request_vars["id"])) {

    $stmt = $mysqli->prepare("SELECT
        g.guestid,
        g.firstName,
        g.lastname,
        -- Subquery för att räkna varje gästs bokningar:
        (SELECT COUNT(*) FROM hotel_booking 
            WHERE guest = g.guestid) as bookings
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



