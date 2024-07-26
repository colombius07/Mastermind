<?php
session_start();
require_once 'mastermind.php';

// Gérer la soumission du formulaire pour changer la taille du plateau
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['board_size'])) {
    $new_size = intval($_POST['board_size']);
    $_SESSION['board_size'] = $new_size;
    // Réinitialiser la session pour appliquer la nouvelle taille
// a partir d'ici, utilisation de la doc pour comprendre cette partie
    session_unset();
    session_destroy();
    session_start();
// fin de l'utilisation de la doc
    $_SESSION['board_size'] = $new_size;
    header("Location: index.php"); // Rediriger pour appliquer la nouvelle taille
    exit();
}

// Gérer la réinitialisation du jeu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset'])) {
    session_unset();
    session_destroy();
    header("Location: index.php"); // Rediriger après réinitialisation
    exit();
}

// Initialiser le jeu avec la taille actuelle du plateau
$board_size = isset($_SESSION['board_size']) ? $_SESSION['board_size'] : 4;
$game = new MastermindGame($board_size);
$game->handleRequest();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mastermind</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .white { color: white; }
        .red { color: red; }
        .guess { display: inline-block; width: 20px; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="color-help">
            <h2>Aide pour les Pions</h2>
            <p>Le feedback de vos propositions utilise les pions suivants :</p>
            <p>
                <strong class="white">⬜</strong> Pion blanc : Le chiffre est correct et bien placé.<br>
                <strong class="red">🔴</strong> Pion rouge : Le chiffre est correct mais mal placé.<br>
            </p>
        </div>
        <h1>Jeu de Mastermind</h1>
        
        <!-- Formulaire pour choisir la taille du plateau -->
        <form method="post" action="index.php">
            <label for="board_size">Choisissez la taille du plateau :</label>
            <select id="board_size" name="board_size">
                <option value="4" <?php echo $board_size == 4 ? 'selected' : ''; ?>>4</option>
                <option value="5" <?php echo $board_size == 5 ? 'selected' : ''; ?>>5</option>
                <option value="6" <?php echo $board_size == 6 ? 'selected' : ''; ?>>6</option>
            </select>
            <button type="submit">Changer la taille</button>
        </form>
        
        <?php if (!$game->isGameOver()): ?>
            <form method="post" action="index.php">
                <label for="guess">Entrez votre combinaison (<?php echo $board_size; ?> chiffres entre 1 et 6) :</label>
                <input type="text" id="guess" name="guess" pattern="[1-6]{<?php echo $board_size; ?>}" required>
                <button type="submit">Soumettre</button>
            </form>
        <?php endif; ?>

        <form method="post" action="index.php">
            <button type="submit" name="reset" value="1">Réinitialiser</button>
            <button type="submit" name="load" value="1">Charger</button>
            <button type="submit" name="save" value="1">Sauvegarde</button>
        </form>
        
        <?php if ($game->getMessage()): ?>
            <p><?php echo htmlspecialchars($game->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <div class="history">
            <h2>Historique des propositions</h2>
            <?php if (!empty($game->getPropositions())): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Proposition</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($game->getPropositions() as $entry): ?>
                            <tr>    
                                <td>
                                    <?php
                                    $guess = str_split($entry['guess']);
                                    foreach ($guess as $g) {
                                        echo "<span class='guess'>$g</span>";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $entry['feedback']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune proposition faite pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
