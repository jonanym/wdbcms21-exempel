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

/**
 * POST methodtest/ID (ny bokning för en specifik gäst)
 */
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($request_vars["id"])) {

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

    // Använd strip_tags() på sådana textfält som ska visas på en webbsida för att förhindra cross site scripting (XSS). htmlentities() är en annan bra metod som håller kvar html men byter ut < och > till &lt; och &gt;
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

    $newId = $stmt->insert_id;

    $response["result"] = "Success POST";
    $response["booking_saved"] = true;
    $response["new_id"] = $newId;

/**
 *  GET methodtest/ID (specifik id)
 */
} elseif ($_SERVER['REQUEST_METHOD'] == "GET" && isset($request_vars["id"])) {

    $response["result"] = "Success GET id = " . $request_vars["id"];

    $stmt = $mysqli->prepare("SELECT
        g.firstname,
        g.lastname,
        b.bookingid,
        b.hotelroom,
        b.addinfo,
        b.datefrom,
        b.dateto 
    FROM
        hotel_guest g
    INNER JOIN hotel_booking b ON
        g.guestid = b.guest
    WHERE g.guestid = ?
      -- and b.bookingid not in (194,195,189)
    ORDER BY b.bookingid ASC");

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


/**
 *  PUT methodtest/?bookingId=[id] (uppdatera bokning)
 */
} elseif ($_SERVER['REQUEST_METHOD'] == "PUT" && isset($request_vars["bookingId"])) {

    $response["result"] = "PUT works!";

    $stmt = $mysqli->prepare("UPDATE hotel_booking SET
            datefrom = ?,
            dateto = ?,
            hotelroom = ?,
            addinfo  = ?
        WHERE bookingid = ?");

    if (!$stmt) {
        die("SQL ERROR: " . $mysqli->error);
    }

    $stmt->bind_param("ssisi", 
        $datefrom,
        $dateto,
        $room,
        $comment,
        $bookingid
    );

    $datefrom = $request_body->datefrom;
    $dateto = $request_body->dateto;
    $room = $request_body->room;
    $comment = strip_tags($request_body->comment);
    $bookingid = $request_vars["bookingId"];

    $stmt->execute();

    $response["result"] = "Success PUT";
    $response["booking_saved"] = true;


/**
 *  DELETE methodtest/?bookingId=[id] (uppdatera bokning)
 */
} elseif ($_SERVER['REQUEST_METHOD'] == "DELETE" && isset($request_vars["bookingId"])) {

    $stmt = $mysqli->prepare("DELETE FROM 
            hotel_booking 
        WHERE bookingid = ?");

    if (!$stmt) {
        die("SQL ERROR: " . $mysqli->error);
    }

    $stmt->bind_param("i", $bookingid);

    $bookingid = $request_vars["bookingId"];

    $stmt->execute();

    $response["result"] = "Success DELETED booking " . $bookingid;

}


echo json_encode($response, JSON_INVALID_UTF8_SUBSTITUTE);
// JSON_INVALID_UTF8_SUBSTITUTE fixar så att json_encode inte failar även om det skulle finnas felaktiga tecken i strängen.



