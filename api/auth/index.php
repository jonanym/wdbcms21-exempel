<?php 

header('content-type: application/json');

// Hämta data fråm request body och spara i array $request_body
$request_json = file_get_contents('php://input');
$request_body = json_decode($request_json);

$response["message"] = "Authentication FAILED";

if ($_SERVER['REQUEST_METHOD'] == "POST"    
    && isset($request_body->username) 
    && isset($request_body->password)) {

    $apiKey = authenticate($request_body->username, $request_body->password);

    if ($apiKey) {
        $response["api_key"] = $apiKey;
        $response["message"] = "Login OK";  
    }

}

// Funktion för att kolla lösenord OBS: "Passw0rd"
// Man kan skapa saltade hashar i php med password_hash("Passw0rd",  PASSWORD_BCRYPT)
// SIMULATION, den kollar ju inte databasen på riktigt...
function authenticate($username, $password) {

    // Vi hämtar användarens hash från databasen (pseudo-SQL):
    /* SELECT password FROM users WHERE username = $username */
    // Vi låtsas att resultatet är följande:
    $users['password'] = '$2y$10$bR9vsbGRMKEbBltPNGhoJeDeDG3bxV0yWS3AO8jekLRWYoSjNEP2C';

    // Vi verifierar det inmatade lösenordet mot hashen
    if (password_verify($password, $users['password'])) {

        // Vi skapar en randomiserad teckensträng att använda som API Key för sessionen
        $apiKey = sha1(rand()); // Kanske enklaste möjliga sätt att skapa en 40-tecken lång random-sträng...
        // Men nu låtsas vi att att den blev följande:
        $apiKey = 'a8d2ca5e4de35d4ae8a6d0d2736700c3733c4063';
        
        // Uppdatera databasen med den nya nyckeln (pseudo-SQL):
        /* UPDATE users SET api_key = $apiKey WHERE username = $username */
        
        // Om uppdateringen gått som den skulle, returnera nyckeln:
        return $apiKey;

    }
    return false;
}

echo json_encode($response);


