<?php
require("connessioneDB.php");
session_start();

if (!isset($_SESSION["utente"])) {
    header("Location: login.php");
    exit();
}

$messaggio_successo = "";
$messaggio_errore = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["aggiungi_prodotto"])) {
    try {
        $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES);
        $descrizione = htmlspecialchars($_POST['descrizione'], ENT_QUOTES);
        $prezzo = (float)$_POST['prezzo'];
        $qta = (int)$_POST['qta'];
        $linkImg = ""; 

        if (isset($_FILES['immagine_prodotto']) && $_FILES['immagine_prodotto']['error'] == 0) {
            $cartella_upload = "immagini/"; 
            if (!is_dir($cartella_upload)) {
                mkdir($cartella_upload, 0755, true); 
            }
            
            $estensione = pathinfo($_FILES['immagine_prodotto']['name'], PATHINFO_EXTENSION);
            $nome_file = "prod_" . time() . "_" . rand(100, 999) . "." . $estensione;
            $percorso_finale = $cartella_upload . $nome_file;

            if (move_uploaded_file($_FILES['immagine_prodotto']['tmp_name'], $percorso_finale)) {
                $linkImg = $percorso_finale;
            }
        }

        $stmt = $con->prepare("INSERT INTO Prodotto (nome, descrizione, prezzo, qta, linkImg) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $nome, $descrizione, $prezzo, $qta, $linkImg);
        
        if ($stmt->execute()) {
            $messaggio_successo = "Prodotto '$nome' aggiunto correttamente al catalogo!";
        } else {
            throw new Exception("Impossibile salvare il prodotto nel database.");
        }

    } catch (Exception $e) {
        $messaggio_errore = "Errore DBMS: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="immagini/icon.jpeg">
    <link rel="stylesheet" href="style.css">
    <title>ArteAri - Nuovo Prodotto</title>
    <style>
        .admin-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border: 2px solid #34495e;
            border-radius: 15px;
            text-align: center;
        }

        .form-mockup {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }

        .input-box {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-family: inherit;
        }

        textarea.input-box {
            resize: vertical;
            min-height: 100px;
        }

        .btn-add {
            width: 100%;
            padding: 15px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background 0.3s;
        }

        .btn-add:hover {
            background: #1a252f;
        }

        .success-msg { color: #27ae60; font-weight: bold; margin-bottom: 15px; }
        .error-msg { color: #c0392b; font-weight: bold; margin-bottom: 15px; }
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
                <li><a href="account.php">IL MIO ACCOUNT</a></li>
            </ul>
        </nav>
        <div class="auth-section">
            <a href="logout.php" class="auth-link">ESCI</a>
        </div>
    </div>
</header>

<main style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 81.7vh; padding-top: 1px;">
    <div class="admin-container">
        <h2>Aggiungi Nuovo Prodotto</h2>
        <p>Compila i campi per inserire un nuovo articolo nel catalogo dello shop.</p>
        
        <?php
        if ($messaggio_successo != "") {
            echo "<div class='success-msg'>✓ " . $messaggio_successo . "</div>";
        }
        if ($messaggio_errore != "") {
            echo "<div class='error-msg'>⚠ " . $messaggio_errore . "</div>";
        }
        ?>

        <div class="form-mockup">
            <form method="POST" action="nuovo-prodotto.php" enctype="multipart/form-data">
                
                <label for="nome">Nome Prodotto *</label>
                <input type="text" id="nome" name="nome" class="input-box" placeholder="Es. Grembiule Ricamato" required>
                
                <label for="descrizione">Descrizione</label>
                <textarea id="descrizione" name="descrizione" class="input-box" placeholder="Dettagli del prodotto, materiali, ecc..."></textarea>
                
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1;">
                        <label for="prezzo">Prezzo (€) *</label>
                        <input type="number" id="prezzo" name="prezzo" class="input-box" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="qta">Quantità in Magazzino *</label>
                        <input type="number" id="qta" name="qta" class="input-box" min="0" value="1" required>
                    </div>
                </div>

                <label for="immagine_prodotto">Immagine Copertina</label>
                <input type="file" id="immagine_prodotto" name="immagine_prodotto" class="input-box" accept="image/png, image/jpeg, image/webp">

                <button type="submit" name="aggiungi_prodotto" class="btn-add">+ INSERISCI NEL CATALOGO</button>
            </form>
        </div>
    </div>
</main>

<footer>
    <div class="footer-content">
        <p>&copy; 2026 ArteAri - Tutti i diritti riservati</p>
    </div>
</footer>

</body>
</html>