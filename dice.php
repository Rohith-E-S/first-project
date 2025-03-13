<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>1v1 Dice Roll Battle</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            text-align: center;
            background-color: #f9f9f9;
            color: #333;
            margin: 50px;
        }
        h1 {
            font-size: 36px;
            color: #444;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 50px;
            margin-top: 20px;
        }
        .player {
            padding: 20px;
            border-radius: 10px;
            width: 220px;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }
        .player:hover {
            transform: scale(1.05);
        }
        .dice {
            font-size: 50px;
            margin: 20px 0;
            animation: roll 0.5s ease-in-out;
        }
        @keyframes roll {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(36deg); }
            25% { transform: rotate(90deg); }
            35% { transform: rotate(126deg); }
            50% { transform: rotate(180deg); }
            75% { transform: rotate(270deg); }
            100% { transform: rotate(360deg); }
        }
        button {
            margin-top: 20px;
            padding: 12px 24px;
            font-size: 20px;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .winner {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin-top: 20px;
        }
        .draw {
            color: #ff9800;
        }
    </style>
</head>
<body>
    <h1>🎲 1v1 Dice Roll Battle 🎲</h1>
    <form method="POST">
        <button type="submit" name="roll">Roll the Dice</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $player1_roll = rand(1, 6);
        $player2_roll = rand(1, 6);
        
        echo "<div class='container'>";
        echo "<div class='player'><h2>Player 1</h2><div class='dice'>🎲 $player1_roll</div></div>";
        echo "<div class='player'><h2>Player 2</h2><div class='dice'>🎲 $player2_roll</div></div>";
        echo "</div>";

        if ($player1_roll > $player2_roll) {
            echo "<div class='winner'>🏆 Player 1 Wins!</div>";
        } elseif ($player1_roll < $player2_roll) {
            echo "<div class='winner'>🏆 Player 2 Wins!</div>";
        } else {
            echo "<div class='winner draw'>🤝 It's a Draw!</div>";
        }
    }
    ?>
</body>
</html>
