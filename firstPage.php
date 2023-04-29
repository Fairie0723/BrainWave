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
function insert_card($card_title, $card_question, $card_answer) {
    $conn = db_connect();

    // Prepare an SQL statement
    $stmt = $conn->prepare("INSERT INTO cards (card_title, card_question, card_answer) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $card_title, $card_question, $card_answer);

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
    $card_question = $_POST["card_question"];
    $card_answer = $_POST["card_answer"];

    // Insert the new card into the database
    insert_card($card_title, $card_question, $card_answer);

    // Redirect to the main page
    header("Location: firstPage.php");
    exit;
}

// Function to delete a card by its ID
function delete_card($card_id) {
    // Connect to the database
    $conn = db_connect();

    // Prepare an SQL statement
    $stmt = $conn->prepare("DELETE FROM cards WHERE id = ?");
    $stmt->bind_param("i", $card_id);

    // Execute the statement
    $stmt->execute();

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

// Check if it's a delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["card_id"])) {
    $card_id = $_POST["card_id"];
    delete_card($card_id);
    exit;
}

function getUniqueCardTitles() {
    $conn = db_connect();
    $result = $conn->query("SELECT DISTINCT card_title FROM cards");
    $titles = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();

    return $titles;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Project</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="fp_style.css" rel="stylesheet" type="text/css">

    
    
</head>
<body>

    <!-- Navigation bar -->
    <nav>
        <ul class="nav-bar">
            <li><a href="index.php">Home</a></li>
            <li><a href="firstPage.php">Group Cards</a></li>
            <li><a href="https://www.google.com/">Third Class</a></li>
            <li><a href="https://www.google.com/">Fourth Class</a></li>
            <li><a href="https://www.google.com/">Fifth Class</a></li>
            <li><a href="https://www.google.com/">Sixth Class</a></li>
        </ul>
    </nav>

</body>
<body>

    <!-- Navigation bar -->
    <nav>
        <!-- Navigation bar content -->
    </nav>

    <!-- Main content -->
    <main>
        <h1>Put title here</h1>

        <div class="search-container">
    <label for="search">Search by Title:</label>
    <select id="search" onchange="filterCards()">
        <option value="">All</option>
        <?php
        $titles = getUniqueCardTitles();
        foreach ($titles as $title) {
            echo '<option value="' . htmlspecialchars($title['card_title']) . '">' . htmlspecialchars($title['card_title']) . '</option>';
        }
        ?>
    </select>
</div>


        <div class="flashcard-container">
    <?php
    // Fetch the flashcard data from the SQL database
    $flashcards = getFlashcardsFromDatabase();

    // Loop through the flashcards and generate the HTML
    foreach ($flashcards as $flashcard) {
        echo '<div class="flashcard" data-id="' . htmlspecialchars($flashcard['id']) . '" onclick="flipCard(this)">';
        echo '<div class="front">';
        echo '<div class="title-description">';
        echo '<h3 class="card_title">' . htmlspecialchars($flashcard['card_title']) . '</h3>';

        echo '</div>';
        echo '<h2 class="question">' . htmlspecialchars($flashcard['card_question']) . '</h2>';
        echo '</div>';
        echo '<div class="back">';
        echo '<p class="answer">' . htmlspecialchars($flashcard['card_answer']) . '</p>';
        echo '</div>';
        echo '</div>';
    }
    ?>
</div>

<div class="buttons-container">
    <button id="delete-mode-btn" onclick="toggleDeleteMode()">Delete mode</button>
    <button id="toggle-add-card-form">Add Card</button>
</div>
         <!-- Button to toggle delete mode -->
    

        <!-- Add card form -->
        <div class="add-card">
            <h2>Add a new card</h2>
            <form action="firstPage.php" method="post">
                <label for="card_title">Title:</label>
                <input type="text" id="card_title" name="card_title" required>
                
                
                
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

   <!-- JavaScript for flashcard flip and form toggle -->
<script>
        let deleteMode = false;

        function toggleDeleteMode() {
    deleteMode = !deleteMode;
    const flashcards = document.getElementsByClassName('flashcard');
    for (let i = 0; i < flashcards.length; i++) {
        if (deleteMode) {
            flashcards[i].classList.add('delete-mode');
            flashcards[i].setAttribute('onclick', 'deleteCard(this)');
        } else {
            flashcards[i].classList.remove('delete-mode');
            flashcards[i].setAttribute('onclick', 'flipCard(this)');
        }
    }
}


        function flipCard(card) {
            if (deleteMode) {
                deleteCard(card);
            } else {
                card.classList.toggle('flipped');
            }
        }

        function deleteCard(card) {
    if (!deleteMode) return;

    if (confirm("Are you sure you want to delete this card?")) {
        const cardId = card.getAttribute("data-id");
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_card.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                card.remove();
            }
        };
        xhr.send("id=" + cardId);
    }
}

    

    function toggleAddCardForm() {
        const form = document.querySelector('.add-card');
        form.style.display = form.style.display === 'flex' ? 'none' : 'flex';
    }

    // Hide the form initially when the page loads
    function hideAddCardFormOnLoad() {
        const form = document.querySelector('.add-card');
        form.style.display = 'none';
    }

    // Call the function to hide the form on page load
    document.addEventListener('DOMContentLoaded', hideAddCardFormOnLoad);

    // Attach the event listener to the button
    const toggleButton = document.getElementById('toggle-add-card-form');
    toggleButton.addEventListener('click', toggleAddCardForm);

    function filterCards() {
    const searchValue = document.getElementById("search").value.toLowerCase();
    const cards = document.getElementsByClassName("flashcard");

    for (let i = 0; i < cards.length; i++) {
        const cardTitle = cards[i].querySelector(".card_title").textContent.toLowerCase();
        if (searchValue === "" || cardTitle === searchValue) {
            cards[i].style.display = "block";
        } else {
            cards[i].style.display = "none";
        }
    }
}


</script>



</body>
</html>

