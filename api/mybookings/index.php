<?php 

include("../../../../../local/mysql_env_real.php");
$mysqli = new mysqli("mysql.arcada.fi", MYSQLUSER, MYSQLPASS, MYSQLUSER);
if ($mysqli->connect_error) die("MySQL Connect ERROR:" . $mysqli->connect_error); 

header('content-type: application/json');

// Spara alla request headers i en variabel, vi intresserar oss i  det här fallet
// speciellt för $request_headers["apikey"]
$request_headers = apache_request_headers();

// Spara URL-variablerna ur query string i array $request_vars
parse_str($_SERVER['QUERY_STRING'], $request_vars);

$response = [];

// Om metoden är GET och apikey finns
if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($request_headers["apikey"])) {

    // Använd API Keyn föra att få användarens bokningar
    $stmt = $mysqli->prepare("SELECT
            g.firstname,
            g.lastname,
            b.*
        FROM
            hotel_guest g
        INNER JOIN hotel_booking b
            ON g.guestid = b.guest
        WHERE
            g.api_key =  ?");

    if (!$stmt) die("SQL ERROR: " . $mysqli->error);

    // Lägg till $request_headers["apikey"] till vår prepared statement
    $stmt->bind_param("s", $request_headers["apikey"]);

    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }    

    $response["result"] = $rows;
}

echo json_encode($response);



