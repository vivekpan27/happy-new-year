<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pac-Man Game</title>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-color: grey;
    }
    canvas {
      border: 5px solid #FFFF00; /* Yellow border for the game board */
      background-color: black;
    }
    #timer {
      position: absolute;
      top: 10px;
      left: 10px;
      color: white;
      font-size: 20px;
    }
    #score {
      position: absolute;
      top: 10px;
      right: 10px;
      color: white;
      font-size: 20px;
    }
  </style>
</head>
<body>
  <canvas id="pacmanCanvas" width="400" height="400"></canvas>
  <div id="timer"></div>
  <div id="score"></div>

  <script>
    const canvas = document.getElementById('pacmanCanvas');
    const ctx = canvas.getContext('2d');
    const timerElement = document.getElementById('timer');
    const scoreElement = document.getElementById('score');

    const pacMan = {
      x: 50,
      y: 50,
      radius: 20,
      startAngle: 0.2,
      endAngle: 1.8,
      mouthDirection: 1, // 1 for opening, -1 for closing
      speed: 10, // 2 times faster
      color: '#0000FF', // Blue
    };

    const ghosts = [];
    const ghostCount = 5;

    const pellets = [];
    const totalPellets = 10;
    let score = 0;
    let timer = 30;

    function drawPacMan() {
      ctx.beginPath();
      ctx.arc(pacMan.x, pacMan.y, pacMan.radius, pacMan.startAngle * Math.PI, pacMan.endAngle * Math.PI);
      ctx.lineTo(pacMan.x, pacMan.y);
      ctx.fillStyle = pacMan.color;
      ctx.fill();
      ctx.closePath();
    }

    function drawGhost(ghost) {
      ctx.beginPath();
      ctx.arc(ghost.x, ghost.y, pacMan.radius, 0, 2 * Math.PI);
      ctx.fillStyle = ghost.color;
      ctx.fill();
      ctx.closePath();
    }

    function drawPellets() {
      ctx.fillStyle = '#00FF00'; // Green
      for (const pellet of pellets) {
        ctx.beginPath();
        ctx.arc(pellet.x, pellet.y, 5, 0, 2 * Math.PI);
        ctx.fill();
        ctx.closePath();
      }
    }

    function clearCanvas() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function updatePacMan() {
      // Update mouth direction
      if (pacMan.endAngle >= 1.8 || pacMan.startAngle <= 0.2) {
        pacMan.mouthDirection *= -1;
      }

      // Update mouth angle
      pacMan.startAngle += 0.05 * pacMan.mouthDirection;
      pacMan.endAngle -= 0.05 * pacMan.mouthDirection;
    }

    function movePacMan(direction) {
      switch (direction) {
        case 'ArrowUp':
          pacMan.y = Math.max(0, pacMan.y - pacMan.speed);
          break;
        case 'ArrowDown':
          pacMan.y = Math.min(canvas.height, pacMan.y + pacMan.speed);
          break;
        case 'ArrowLeft':
          pacMan.x = Math.max(0, pacMan.x - pacMan.speed);
          break;
        case 'ArrowRight':
          pacMan.x = Math.min(canvas.width, pacMan.x + pacMan.speed);
          break;
      }

      // Check for pellet consumption
      for (let i = 0; i < pellets.length; i++) {
        const pellet = pellets[i];
        const distance = Math.sqrt((pacMan.x - pellet.x) ** 2 + (pacMan.y - pellet.y) ** 2);

        if (distance < pacMan.radius + 5) {
          // Pac-Man consumed the pellet
          pellets.splice(i, 1);
          score++;

          // Update the live score
          scoreElement.textContent = 'Score: ' + score;

          // Check if all pellets are consumed
          if (pellets.length === 0) {
            alert('Congratulations! You won! Your score: ' + score);
            resetGame();
          }

          break;
        }
      }
    }

    function updateGhosts() {
      for (const ghost of ghosts) {
        // Move the ghosts either horizontally or vertically until they reach the game board boundary,
        // then change direction
        if (ghost.direction === 'horizontal') {
          if (ghost.moveRight) {
            ghost.x = Math.min(canvas.width - pacMan.radius, ghost.x + 0.6); // 3 times faster
          } else {
            ghost.x = Math.max(0 + pacMan.radius, ghost.x - 0.6); // 3 times faster
          }

          // Change direction if reaching the boundary
          if (ghost.x + pacMan.radius >= canvas.width || ghost.x - pacMan.radius <= 0) {
            ghost.moveRight = !ghost.moveRight;
          }
        } else {
          if (ghost.moveDown) {
            ghost.y = Math.min(canvas.height - pacMan.radius, ghost.y + 0.6); // 3 times faster
          } else {
            ghost.y = Math.max(0 + pacMan.radius, ghost.y - 0.6); // 3 times faster
          }

          // Change direction if reaching the boundary
          if (ghost.y + pacMan.radius >= canvas.height || ghost.y - pacMan.radius <= 0) {
            ghost.moveDown = !ghost.moveDown;
          }
        }
      }
    }

    function updateTimer() {
      timerElement.textContent = 'Time: ' + timer + 's';

      if (timer === 0) {
        alert('Time is up! Your score: ' + score);
        resetGame();
      } else {
        timer--;
        setTimeout(updateTimer, 1000);
      }
    }

    function resetGame() {
      pacMan.x = 50;
      pacMan.y = 50;
      pellets.length = 0;
      ghosts.length = 0;
      score = 0;
      timer = 30;
      initializePellets();
      initializeGhosts();
      updateTimer();
      scoreElement.textContent = 'Score: ' + score;
    }

    function handleKeyPress(event) {
      movePacMan(event.key);
    }

    function initializePellets() {
      // Add new pellets to the array
      for (let i = 0; i < totalPellets; i++) {
        pellets.push({
          x: Math.random() * canvas.width,
          y: Math.random() * canvas.height,
        });
      }
    }

    function initializeGhosts() {
      const ghostColors = ['#FF0000', '#00FF00', '#FF00FF', '#FFFF00', '#00FFFF'];
      // Add new ghosts to the array with different colors
      for (let i = 0; i < ghostCount; i++) {
        ghosts.push({
          x: Math.random() * canvas.width,
          y: Math.random() * canvas.height,
          color: ghostColors[i],
          direction: Math.random() < 0.5 ? 'horizontal' : 'vertical',
          moveRight: Math.random() < 0.5,
          moveDown: Math.random() < 0.5,
        });
      }
    }

    function gameLoop() {
      clearCanvas();
      updatePacMan();
      drawPacMan();
      drawPellets();
      updateGhosts();
      for (const ghost of ghosts) {
        drawGhost(ghost);
      }
      requestAnimationFrame(gameLoop);
    }

    document.addEventListener('keydown', handleKeyPress);

    initializePellets();
    initializeGhosts();
    updateTimer();
    gameLoop();
  </script>
</body>
</html>
