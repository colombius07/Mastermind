<?php
/**
 * Classe MastermindGame
 *
 * Cette classe implémente le jeu Mastermind, où un joueur doit deviner une séquence secrète de couleurs.
 */
class MastermindGame
{
    /** @var array $secret La séquence secrète générée. */
    private $secret;

    /** @var array $propositions Les propositions faites par le joueur. */
    private $propositions;

    /** @var int $attempts Le nombre de tentatives effectuées par le joueur. */
    private $attempts;

    /** @var string $message Le message affiché au joueur. */
    private $message;

    /** @var bool $game_over Indique si le jeu est terminé. */
    private $game_over;

    /** @var int $board_size La taille du plateau de jeu (nombre de cases). */
    private $board_size;

    public function __construct($board_size = null)
    {
        // Initialiser board_size dans la session uniquement si elle n'est pas déjà définie
        if ($board_size !== null) {
            $_SESSION['board_size'] = $board_size;
        }
        // Assigner la taille du plateau de la session ou la valeur par défaut si la session n'est pas définie
        $this->board_size = isset($_SESSION['board_size']) ? $_SESSION['board_size'] : 4; // Valeur par défaut de 4
        $_SESSION['board_size'] = $this->board_size;
        $this->init();
    }

    /**
     * Initialisation du jeu.
     *
     * Initialise ou réinitialise les propriétés de la session et de la classe.
     */
    private function init()
    {
        // Vérifier la taille du plateau et générer un nouveau secret si nécessaire
        if (!isset($_SESSION['secret']) || $_SESSION['board_size'] != $this->board_size) {
            $_SESSION['secret'] = $this->generateSecret();
        }
        if (!isset($_SESSION['propositions'])) {
            $_SESSION['propositions'] = [];
        }
        if (!isset($_SESSION['attempts'])) {
            $_SESSION['attempts'] = 0;
        }
        if (!isset($_SESSION['message'])) {
            $_SESSION['message'] = '';
        }
        if (!isset($_SESSION['game_over'])) {
            $_SESSION['game_over'] = false;
        }

        $this->secret = $_SESSION['secret'];
        $this->propositions = $_SESSION['propositions'];
        $this->attempts = $_SESSION['attempts'];
        $this->message = $_SESSION['message'];
        $this->game_over = $_SESSION['game_over'];
    }

    /**
     * Génère une séquence secrète aléatoire.
     *
     * @return array La séquence secrète.
     */
    private function generateSecret()
    {
        return array_map(function() { return rand(1, 6); }, range(1, $this->board_size));
    }

    /**
     * Évalue une proposition du joueur.
     *
     * @param array $guess La proposition du joueur.
     * @return array Un tableau contenant le feedback et le nombre de positions correctes.
     */
    public function evaluateGuess($guess)
    {
        $feedback = array_fill(0, $this->board_size, '');
        $used_secret = array_fill(0, $this->board_size, false);
        $used_guess = array_fill(0, $this->board_size, false);
        $correct_count = 0;
// créé par intelligence artificielle  partir d'ici
        for ($i = 0; $i < $this->board_size; $i++) {
            if ($guess[$i] == $this->secret[$i]) {
                $feedback[$i] = "<span class='white'>⬜</span>";
                $used_secret[$i] = true;
                $used_guess[$i] = true;
                $correct_count++;
            }
        }
// fin de la création par intelligence artificielle
        for ($i = 0; $i < $this->board_size; $i++) {
            if (!$used_guess[$i]) {
                for ($j = 0; $j < $this->board_size; $j++) {
                    if (!$used_secret[$j] && $guess[$i] == $this->secret[$j]) {
                        $feedback[$i] = "<span class='red'>🔴</span>";
                        $used_secret[$j] = true;
                        break;
                    }
                }
            }
        }
        
        return [
            'feedback' => implode('', $feedback),
            'correct_count' => $correct_count
        ];
    }

    /**
     * Vérifie si la proposition du joueur est correcte.
     *
     * @param array $guess La proposition du joueur.
     * @return bool True si la proposition est correcte, false sinon.
     */    
    public function isCorrectGuess($guess)
    {
        return $this->secret === $guess;
    }

    /**
     * Termine le jeu et enregistre un message de victoire.
     */    
    public function endGame()
    {
        $_SESSION['message'] = "Félicitations ! Vous avez trouvé le code " . implode('', $this->secret) . " en " . $_SESSION['attempts'] . " essai(s).";
        $_SESSION['game_over'] = true;
    }

    /**
     * Gère les requêtes HTTP POST pour les actions du jeu.
     */    
    public function handleRequest()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['guess'])) {
                $guess = array_map('intval', str_split($_POST['guess']));
                $_SESSION['attempts']++;
    
                if (count($guess) == $this->board_size) {
                    $result = $this->evaluateGuess($guess);
                    $feedback = $result['feedback'];
                    $correct_count = $result['correct_count'];
    
                    $_SESSION['propositions'][] = [
                        'guess' => $_POST['guess'],
                        'feedback' => $feedback
                    ];
    
                    if ($correct_count == $this->board_size) {
                        $this->endGame();
                    }
    
                    // Sauvegarde automatique après chaque proposition
                    $this->saveGame();
    
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Veuillez entrer une combinaison valide.";
                }
            } elseif (isset($_POST['reset'])) {
                session_unset();
                session_destroy();
                header("Location: index.php");
                exit();
            } elseif (isset($_POST['load'])) {
                $this->loadGame();
                header("Location: index.php");
                exit();
            } elseif (isset($_POST['save'])){
                $this->saveGame();
                header("Location: index.php");
            } elseif (isset($_POST['board_size'])) {
                $_SESSION['board_size'] = intval($_POST['board_size']);
                // Réinitialiser la session en conséquence
                session_unset();
                session_destroy();
                session_start();
                $this->loadGame(); // Charger l'état du jeu après avoir changé la taille
                header("Location: index.php");
                exit();
            }
        }
    }
    
    /**
     * Retourne les propositions faites par le joueur.
     *
     * @return array Les propositions.
     */
    public function getPropositions()
    {
        return $this->propositions;
    }

    /**
     * Retourne le message actuel du jeu.
     *
     * @return string Le message.
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Vérifie si le jeu est terminé.
     *
     * @return bool True si le jeu est terminé, false sinon.
     */
    public function isGameOver()
    {
        return $this->game_over;
    }

    /**
     * Sauvegarde l'état du jeu dans un fichier JSON.
     */
    private function saveGame()
    {
        $data = [
            'board_size' => $this->board_size,
            'secret' => $this->secret,
            'propositions' => $this->propositions,
            'attempts' => $this->attempts,
            'message' => $this->message,
            'game_over' => $this->game_over,
        ];
        file_put_contents('game_state.json', json_encode($data));
    }

    /**
     * Charge l'état du jeu à partir d'un fichier JSON.
     */
    private function loadGame()
    {
        if (file_exists('game_state.json')) {
            $data = json_decode(file_get_contents('game_state.json'), true);
            $_SESSION['board_size'] = $data['board_size'];
            $_SESSION['secret'] = $data['secret'];
            $_SESSION['propositions'] = $data['propositions'];
            $_SESSION['attempts'] = $data['attempts'];
            $_SESSION['message'] = $data['message'];
            $_SESSION['game_over'] = $data['game_over'];
            $this->init();
        }
    }
}
?>
