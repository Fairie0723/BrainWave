<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_id = $_POST["id"];
    delete_card($card_id);
}

function delete_card($card_id) {
    $conn = db_connect();

    $stmt = $conn->prepare("DELETE FROM cards WHERE id = ?");
    $stmt->bind_param("i", $card_id);

    $stmt->execute();

    $stmt->close();
    $conn->close();
}

function db_connect() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "brainwave";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>