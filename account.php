<?php
require("connessioneDB.php");
session_start();

if (!isset($_SESSION["utente"])) {
    header("Location: login.php");
    exit();
}

$email_utente = $_SESSION["utente"]["email"];
$messaggio_feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["elimina_account"])) {
    $stmt_delete = $con->prepare("DELETE FROM Utente WHERE email = ?");
    $stmt_delete->bind_param("s", $email_utente);
    
    if ($stmt_delete->execute()) {
        session_unset();
        session_destroy();
        header("Location: index.html");
        exit();
    } else {
        $messaggio_feedback = "Errore in fase di eliminazione: " . $stmt_delete->error;
    }
}

$stmt_select = $con->prepare("SELECT email, nome, cognome, indirizzo, livello FROM Utente WHERE email = ?");
$stmt_select->bind_param("s", $email_utente);
$stmt_select->execute();
$risultato = $stmt_select->get_result();
$dati_utente = $risultato->fetch_assoc();

if (!empty($dati_utente['nome'])) {
    $nome = htmlspecialchars($dati_utente['nome'], ENT_QUOTES);
} else {
    $nome = 'Dato non valorizzato';
}

if (!empty($dati_utente['cognome'])) {
    $cognome = htmlspecialchars($dati_utente['cognome'], ENT_QUOTES);
} else {
    $cognome = 'Dato non valorizzato';
}

if (!empty($dati_utente['indirizzo'])) {
    $indirizzo = htmlspecialchars($dati_utente['indirizzo'], ENT_QUOTES);
} else {
    $indirizzo = 'Dato non valorizzato';
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="immagini/icon.jpeg">
    <link rel="stylesheet" href="style.css">
    <title>ArteAri - Il mio Account</title>
    <style>
        .account-container { 
            max-width: 600px; 
            margin: 50px auto; 
            padding: 20px;
        }
        .account-card { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            border: 1px solid #eee; 
        }
        .account-header { 
            border-bottom: 2px solid #f5f7fa; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
        }
        .account-header h2 { 
            color: #2c3e50; 
            font-size: 1.8rem; 
        }
        .info-group { 
            margin-bottom: 15px; 
            display: flex; 
            flex-direction: column; 
        }
        .info-label { 
            font-size: 0.85rem; 
            color: #7f8c8d; 
            font-weight: 600; 
            text-transform: uppercase; 
        }
        .info-value { 
            font-size: 1.1rem; 
            color: #34495e; 
            padding: 8px 0; 
            border-bottom: 1px solid #f0f0f0; 
        }
        .btn-delete-account { 
            background-color: #c62828; 
            color: white; 
            border: none; 
            padding: 12px 20px; 
            border-radius: 6px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: 0.3s; 
            width: 100%; 
        }
        .btn-delete-account:hover { 
            background-color: #b71c1c; 
        }
        .msg-error { 
            background-color: #ffebee; 
            color: #c62828; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <a id="home" href="indexLog.php">
            <div class="logo">
                <img src="immagini/icon.jpeg" alt="Logo">
                <h1>ArteAri</h1>
            </div>
        </a>
        <nav>
            <ul>
                <li><a href="indexLog.php">HOME</a></li>
                <li><a href="shop.php">SHOP</a></li>
                <li><a href="carrello.php">CARRELLO</a></li>
                <li><a href="account.php" class="active">IL MIO ACCOUNT</a></li>
            </ul>
        </nav>
        <div class="auth-section">
            <a href="login.php" class="auth-link">ESCI</a>
        </div>
    </div>
</header>

<main>
    <div class="account-container">
        <div class="account-card">
            
            <div class="account-header">
                <h2>Riepilogo Profilo</h2>
            </div>

            <?php
            if (!empty($messaggio_feedback)) {
                echo "<div class='msg-error'>" . htmlspecialchars($messaggio_feedback) . "</div>";
            }
            ?>

            <div class="info-group">
                <span class="info-label">Indirizzo Email</span>
                <span class="info-value"><?php echo htmlspecialchars($dati_utente['email']); ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">Nome</span>
                <span class="info-value"><?php echo $nome; ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">Cognome</span>
                <span class="info-value"><?php echo $cognome; ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">Indirizzo di Spedizione</span>
                <span class="info-value"><?php echo $indirizzo; ?></span>
            </div>

                <form method="POST" action="account.php" onsubmit="return confirm('WARNING: Sei assolutamente sicuro di voler distruggere il tuo account? L\'azione non è reversibile.');">
                    <button type="submit" name="elimina_account" class="btn-delete-account">ELIMINA ACCOUNT DEFINITIVAMENTE</button>
                </form>
        </div>
    </div>
</main>

<footer>
    <div class="footer-content">
        <p>&copy; 2026 ArteAri - Tutti i diritti riservati</p>
        <div class="footer-links">
            <a href="#">Contatti</a>
        </div>
    </div>
</footer>
    
</body>
</html>