<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Project</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="script.js"></script>
</head>
<body>

    <!-- Navigation bar -->
    <nav class="navbar navbar-inverse" style="margin-top: 10px">
  <div class="container-fluid">
    <div class="navbar-header">

      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Home </a></li>
        <li><a href="firstPage.php">Topics</a></li>
        </li>

      </ul>
    </div>
  </div>
</nav>

</body>
</html>
<?php


$connection = mysqli_connect("127.0.0.1", "root", "Siddhu*876", "brainwave");

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


function delete_card($card_title) {
  $conn = mysqli_connect("localhost", "root", "Siddhu*876", "brainwave");

  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $stmt = $conn->prepare("DELETE FROM cards WHERE card_title = ?");
  $stmt->bind_param("s", $card_title);

  $stmt->execute();

  $stmt->close();
  mysqli_close($conn);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
  $card_title = $_POST["card_title"];
  delete_card($card_title);

  header("Location: index.php");
  exit;
}





// Function to insert a new card into the database
function insert_card($card_title, $card_question, $card_answer) {
  // Connect to the database
  $conn = mysqli_connect("localhost", "root", "Siddhu*876", "brainwave");

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

function getUniqueCardTitles() {
  $conn = mysqli_connect("localhost", "root", "Siddhu*876", "brainwave");

  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $query = "SELECT DISTINCT card_title FROM CARDS ORDER BY card_title";
  $result = mysqli_query($conn, $query);

  $titles = array();

  while ($row = mysqli_fetch_assoc($result)) {
    $titles[] = $row;
  }

  mysqli_close($conn);

  return $titles;
}



function filter_cards($selected_title) {
  $conn = mysqli_connect("localhost", "root", "Siddhu*876", "brainwave");

  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $query = "SELECT card_title, card_question, card_answer FROM CARDS";

  if (!empty($selected_title)) {
    $query .= " WHERE card_title = ?";
  }

  $stmt = $conn->prepare($query);

  if (!empty($selected_title)) {
    $stmt->bind_param("s", $selected_title);
  }

  $stmt->execute();
  $result = $stmt->get_result();

  $qaPairs = array();

  while ($row = $result->fetch_assoc()) {
    $qaPairs[] = array(
      'card_title' => $row['card_title'],
      'card_question' => $row['card_question'],
      'card_answer' => $row['card_answer']
    );
  }

  $stmt->close();
  mysqli_close($conn);

  return $qaPairs;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["filter"]) && isset($_POST["card_title"])) {
  $selected_title = $_POST["card_title"];
  $qaPairs = filter_cards($selected_title);
}

if (isset($_POST['prev']) || isset($_POST['next'])) {
  if (isset($_POST['selected_title']) && !empty($_POST['selected_title'])) {
    $selected_title = $_POST['selected_title'];
    $qaPairs = filter_cards($selected_title);
  }
}


if (isset($qaPairs[$currentPair])) {
  $currentCard = $qaPairs[$currentPair];
} else {
  $currentCard = array(
    'card_title' => '',
    'card_question' => '',
    'card_answer' => ''
  );
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Project</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <script src="script.js"></script>
</head>

<body style="background: url(https://static.vecteezy.com/system/resources/thumbnails/018/033/254/small/abstract-colorful-background-soft-color-background-design-beautiful-blue-watercolor-grunge-watercolor-paper-textured-aquarelle-canvas-for-modern-creative-design-background-with-rays-vector.jpg); background-repeat: no-repeat; background-size: cover">




    <!-- Navigation bar -->
    <nav>
        <!-- Navigation bar content -->
    </nav>

    <!-- Main content -->
    <div class="container">
  <h2 class="title">
    <span class="title-word title-word-1">Welcome </span>
    <span class="title-word title-word-2">To</span>
    <span class="title-word title-word-3">Our</span>
    <span class="title-word title-word-4">BrainWave</span>
  </h2>
</div>
    <main>
    <?php if (!empty($qaPairs)) : ?>
    <h1 class="card_title">Course Title: <?php echo $currentCard['card_title']; ?></h1>
    <?php endif; ?>

    </main>
  <div class ="search-container">
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <label for="card_title">Search by Title:</label>
    <select class="drop-select" name="select-profession">
      <option value="">All</option>
      <?php
      $titles = getUniqueCardTitles();
      foreach ($titles as $title) {
          echo '<option value="' . htmlspecialchars($title['card_title']) . '">' . htmlspecialchars($title['card_title']) . '</option>';
      }
      ?>
    </select>
    <input id ="filter-button" type="submit" name="filter" value="Filter">
  </form>
    </div>

<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <input type="hidden" name="card_title" value="<?php echo $currentCard['card_title']; ?>">
      <button type="submit" name="delete" class="delete-btn">Delete</button>
    </form>

    <div class="main-container">
    <div class="flashcard-container">
    
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="currentPair" value="<?php echo $currentPair; ?>">
    <input type="hidden" name="selected_title" value="<?php echo isset($selected_title) ? htmlspecialchars($selected_title) : ''; ?>">
    <button type="submit" name="prev" class="arrow-left"><img src="back_arrow.png" height="80px" width="80px"></button>
    <button type="submit" name="next" class="arrow-right"><img src="arrow_right.png" height="80px" width="80px"></button>
  </form>




  <div class="flashcard" onclick="flipCard(this)">
  <?php if (!empty($qaPairs)) : ?>
    <div class="front">
      <h1 class="card-question"><?php echo $qaPairs[$currentPair]['card_question']; ?></h1>
    </div>
    <div class="back">
      <h2 class="card-answer"><?php echo $qaPairs[$currentPair]['card_answer']; ?></h2>
    </div>
  <?php endif; ?>
</div>
        
        

  </div>


 <!-- Add card form -->
 <div class="add-card" style="margin-top: 100px">
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
        </div>
</body>
</html>
