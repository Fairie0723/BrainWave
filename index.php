<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Project</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

    <!-- Navigation bar -->
    <nav class="stroke">
        <ul>
            <li><a href="https://www.google.com/">First Class</a></li>
            <li><a href="https://www.google.com/">Second Class</a></li>
            <li><a href="https://www.google.com/">Third Class</a></li>
            <li><a href="https://www.google.com/">Fourth Class</a></li>
            <li><a href="https://www.google.com/">Fifth Class</a></li>
            <li><a href="https://www.google.com/">Sixth Class</a></li>
        </ul>
    </nav>

</body>
</html>

<?php
// Function to establish a database connection
function db_connect() {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "brainwave";

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Function to fetch flashcards from the database
function getFlashcardsFromDatabase() {
    // Connect to the database
    $conn = db_connect();

    // Query the database for all flashcards
    $result = $conn->query("SELECT * FROM cards");

    // Fetch all flashcards as an associative array
    $flashcards = $result->fetch_all(MYSQLI_ASSOC);

    // Close the connection
    $conn->close();

    return $flashcards;
}

// Function to insert a new card into the database
function insert_card($card_title, $card_description, $card_question, $card_answer) {
    // Connect to the database
    $conn = db_connect();

    // Prepare an SQL statement
    $stmt = $conn->prepare("INSERT INTO cards (card_title, card_description, card_question, card_answer) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $card_title, $card_description, $card_question, $card_answer);

    // Execute the statement
    $stmt->execute();

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted data
    $card_title = $_POST["card_title"];
    $card_description = $_POST["card_description"];
    $card_question = $_POST["card_question"];
    $card_answer = $_POST["card_answer"];

    // Insert the new card into the database
    insert_card($card_title, $card_description, $card_question, $card_answer);

    // Redirect to the main page
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Project</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

    <!-- Navigation bar -->
    <nav>
        <!-- Navigation bar content -->
    </nav>

    <!-- Main content -->
    <main>
        <h1>Put title here</h1>

        <div class="flashcard-container">
            <?php
            // Fetch the flashcard data from the SQL database
            $flashcards = getFlashcardsFromDatabase();

            // Loop through the flashcards and generate the HTML
            foreach ($flashcards as $flashcard) {
                echo '<div class="flashcard" onclick="flipCard(this)">';
                echo '<div class="front">';
                echo '<div class="title-description">';
                echo '<h3 class="card_title">' . htmlspecialchars($flashcard['card_title']) . '</h3>';
                echo '<p class="card_description">' . htmlspecialchars($flashcard['card_description']) . '</p>';
                echo '</div>';
                echo '<h2                class="question">' . htmlspecialchars($flashcard['card_question']) . '</h2>';
                echo '</div>';
                echo '<div class="back">';
                echo '<p class="answer">' . htmlspecialchars($flashcard['card_answer']) . '</p>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Add card form -->
        <div class="add-card">
            <h2>Add a new card</h2>
            <form action="index.php" method="post">
                <label for="card_title">Title:</label>
                <input type="text" id="card_title" name="card_title" required>
                
                <label for="card_description">Description:</label>
                <input type="text" id="card_description" name="card_description" required>
                
                <label for="card_question">Question:</label>
                <input type="text" id="card_question" name="card_question" required>
                
                <label for="card_answer">Answer:</label>
                <input type="text" id="card_answer" name="card_answer" required>
               
                <input type="submit" value="Add Card">
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <!-- Footer content -->
    </footer>

    <!-- JavaScript for flashcard flip -->
    <script>
        function flipCard(card) {
            card.classList.toggle('flipped');
        }
    </script>

</body>
</html>

