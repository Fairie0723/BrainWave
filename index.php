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

        <li><a href="index.php">Home</a></li>
            <li><a href="firstPage.php">Group Cards</a></li>

            <li><a href="https://www.google.com/">Third Class</a></li>
            <li><a href="https://www.google.com/">Fourth Class</a></li>
            <li><a href="https://www.google.com/">Fifth Class</a></li>
            <li><a href="https://www.google.com/">Sixth Class</a></li>
        </ul>
    </nav>

</body>
</html>
<?php


// Replace "hostname", "username", "password", and "database" with your database credentials
$connection = mysqli_connect("127.0.0.1", "root", "", "brainwave");

if (!$connection) {
  die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT card_title, card_question, card_answer FROM CARDS";
$result = mysqli_query($connection, $query);

// Create an array of question and answer objects from the database results
$qaPairs = array();

while ($row = mysqli_fetch_assoc($result)) {
  $qaPairs[] = array(
    'card_title' => $row['card_title'],
    'card_question' => $row['card_question'],
    'card_answer' => $row['card_answer']
  );
}

mysqli_close($connection);

$currentPair = 0;
$totalPairs = count($qaPairs);

if (isset($_POST['prev'])) {
  $currentPair = ($_POST['currentPair'] - 1 + $totalPairs) % $totalPairs;
} elseif (isset($_POST['next'])) {
  $currentPair = ($_POST['currentPair'] + 1) % $totalPairs;
}

// Function to insert a new card into the database
function insert_card($card_title, $card_question, $card_answer) {
  // Connect to the database
  $conn = mysqli_connect("localhost", "root", "", "brainwave");

  // Check the connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  // Prepare the statement
  $stmt = $conn->prepare("INSERT INTO cards (card_title, card_question, card_answer) VALUES (?, ?, ?)");

  // Check if the statement was prepared
  if (!$stmt) {
    die("Error preparing statement: " . mysqli_error($conn));
  }

  // Bind the parameters
  $stmt->bind_param("sss", $card_title, $card_question, $card_answer);

  // Execute the statement
  $stmt->execute();

  // Close the statement
  $stmt->close();

  // Close the connection
  mysqli_close($conn);
}

// Check if the form was submitted and the "Add Card" button was clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_card"])) {
  // Get the submitted data
  $card_title = $_POST["card_title"];
  $card_question = $_POST["card_question"];
  $card_answer = $_POST["card_answer"];

  // Insert the new card into the database
  insert_card($card_title, $card_question, $card_answer);

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
    <h1 class="card_title"><?php echo $qaPairs[$currentPair]['card_title']; ?></h1>

    </main>
    <div class="flashcard-container">
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <input type="hidden" name="currentPair" value="<?php echo $currentPair; ?>">
      <button type="submit" name="prev" class="arrow-left"><==</button>
    </form>
    <div class="flashcard" onclick="flipCard(this)">
            <div class="front">
            <h1 class="card-question"><?php echo $qaPairs[$currentPair]['card_question']; ?></h1>
            </div>
            <div class="back">
            <h2 class="card-answer"><?php echo $qaPairs[$currentPair]['card_answer']; ?></h2>   
            </div>
        </div>
        
  <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <input type="hidden" name="currentPair" value="<?php echo $currentPair; ?>">
      <button type="submit" name="next" class="arrow-right">==></button>
    </form>

  </div>


 <!-- Add card form -->
 <div class="add-card">
            <h2>Add a new card</h2>
            <form action="index.php" method="post">
                <label for="card_title">Title:</label>
                <input type="text" id="card_title" name="card_title" required>
                
                <label for="card_question">Question:</label>
                <input type="text" id="card_question" name="card_question" required>
                
                <label for="card_answer">Answer:</label>
                <input type="text" id="card_answer" name="card_answer" required>
               
                <input type="submit" name="add_card" value="Add Card">
            </form>
        </div>
</body>
</html>
