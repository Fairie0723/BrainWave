
// Create an array of question and answer objects
let qaPairs = [];
  
  // Set initial card index to 0
  let cardIndex = 0;
  
  // Update the card with the current question and answer pair
  function updateCard() {
    let questionDiv = document.querySelector('.question');
    let answerDiv = document.querySelector('.answer');
    
    questionDiv.textContent = qaPairs[cardIndex].question;
    answerDiv.textContent = qaPairs[cardIndex].answer;
  }
  
  // Add event listeners to left and right arrows
  let arrowLeft = document.querySelector('.arrow-left');
  let arrowRight = document.querySelector('.arrow-right');
  
  arrowLeft.addEventListener('click', function() {
    if (cardIndex === 0) {
      cardIndex = qaPairs.length - 1;
    } else {
      cardIndex--;
    }
    updateCard();
  });
  
  arrowRight.addEventListener('click', function() {
    if (cardIndex === qaPairs.length - 1) {
      cardIndex = 0;
    } else {
      cardIndex++;
    }
    updateCard();
  });
  
  // Initialize the card with the first question and answer pair
  updateCard();
  


function showForm() {
    document.getElementById('prompt').style.display = 'block';
    document.getElementById('add').style.display = 'none';
}

function hideForm() {
    document.getElementById('prompt').style.display = 'none';
    document.getElementById('add').style.display = 'block';
}