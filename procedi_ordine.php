<?php
require("connessioneDB.php");
session_start();

// Protezione della pagina: accesso consentito solo agli utenti loggati con carrello non vuoto
if (!isset($_SESSION["utente"]) || !isset($_SESSION["carrello"]) || empty($_SESSION["carrello"])) {
    header("Location: carrello.php");
    exit();
}

$email_utente = $_SESSION["utente"]["email"];
$ordine_completato = false;
$errore_db = "";

// Gestione del finto pagamento e salvataggio ordine
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["conferma_pagamento"])) {
    
    // 1. Recupero l'ID numerico dell'utente (necessario per la FK idfUtente in Ordine)
    $stmt_user = $con->prepare("SELECT idUtente, indirizzo FROM Utente WHERE email = ?");
    $stmt_user->bind_param("s", $email_utente);
    $stmt_user->execute();
    $res_user = $stmt_user->get_result();
    $user_data = $res_user->fetch_assoc();
    $id_u = $user_data['idUtente'];
    $indirizzo_consegna = $user_data['indirizzo'];

    // 2. Calcolo del totale finale leggendo i prezzi attuali dal DB
    $totale_ordine = 0;
    foreach ($_SESSION['carrello'] as $item) {
        $stmt_p = $con->prepare("SELECT prezzo FROM Prodotto WHERE idProdotto = ?");
        $stmt_p->bind_param("i", $item['id']);
        $stmt_p->execute();
        $prezzo_db = $stmt_p->get_result()->fetch_assoc()['prezzo'];
        $totale_ordine += ($prezzo_db * $item['qta']);
    }

    // 3. Inserimento testata Ordine (Mockando pesoTotale a 0 per ora)
    $data_attuale = date("Y-m-d H:i:s");
    $peso_fittizio = 0.5; 
    $stmt_ord = $con->prepare("INSERT INTO Ordine (idfUtente, data_ordine, totale, pesoTotale, indirizzo) VALUES (?, ?, ?, ?, ?)");
    $stmt_ord->bind_param("isdds", $id_u, $data_attuale, $totale_ordine, $peso_fittizio, $indirizzo_consegna);
    
    if ($stmt_ord->execute()) {
        $id_nuovo_ordine = $con->insert_id;

        // 4. Inserimento righe DettagliOrdine per ogni articolo nel carrello
        foreach ($_SESSION['carrello'] as $item) {
            // Recupero prezzo per lo storico ordine
            $stmt_p2 = $con->prepare("SELECT prezzo FROM Prodotto WHERE idProdotto = ?");
            $stmt_p2->bind_param("i", $item['id']);
            $stmt_p2->execute();
            $prezzo_storia = $stmt_p2->get_result()->fetch_assoc()['prezzo'];

            $stmt_det = $con->prepare("INSERT INTO DettagliOrdine (idfOrdine, idfProdotto, qta, prezzo) VALUES (?, ?, ?, ?)");
            $stmt_det->bind_param("iiid", $id_nuovo_ordine, $item['id'], $item['qta'], $prezzo_storia);
            $stmt_det->execute();

            // Opzionale: aggiornamento stock (qta) nella tabella Prodotto
            $con->query("UPDATE Prodotto SET qta = qta - " . $item['qta'] . " WHERE idProdotto = " . $item['id']);
        }

        // 5. Pulizia sessione e flag successo
        unset($_SESSION['carrello']);
        $ordine_completato = true;
    } else {
        $errore_db = "Si è verificato un problema tecnico durante la creazione dell'ordine.";
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
    <title>ArteAri - Pagamento</title>
    <style>
        .payment-container { max-width: 600px; margin: 50px auto; padding: 30px; background: white; border: 2px solid #34495e; border-radius: 15px; text-align: center; }
        .card-mockup { background: #f8f9fa; border: 1px solid #ddd; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: left; }
        .input-pay { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        .btn-pay { width: 100%; padding: 15px; background: #7b2cb1; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1.1rem; }
        .success-msg { color: #27ae60; }
        .error-msg { color: #c0392b; }
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
    </div>
</header>

<main style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 81.7vh; padding-top: 1px;">
    <div class="payment-container">
        <?php
        if ($ordine_completato) {
            echo "<h2 class='success-msg'>Pagamento Riuscito!</h2>";
            echo "<p style='margin: 20px 0;'>Grazie per il tuo acquisto. Il tuo ordine è in fase di elaborazione.</p>";
            echo "<a href='shop.php' class='auth-link' style='color:#7b2cb1; font-weight:bold;'>Torna allo Shop</a>";
        } else {
            if ($errore_db != "") {
                echo "<p class='error-msg'>" . $errore_db . "</p>";
            }
            echo "<h2>Checkout Sicuro</h2>";
            echo "<p>Inserisci i dati della tua carta per completare l'ordine</p>";
            echo "<div class='card-mockup'>";
            echo "  <form method='POST' action='procedi_ordine.php'>";
            echo "      <label>Numero Carta</label>";
            echo "      <input type='text' class='input-pay' placeholder='0000 0000 0000 0000' required>";
            echo "      <div style='display:flex; gap:10px;'>";
            echo "          <div style='flex:1;'><label>Scadenza</label><input type='text' class='input-pay' placeholder='MM/AA' required></div>";
            echo "          <div style='flex:1;'><label>CVV</label><input type='text' class='input-pay' placeholder='123' required></div>";
            echo "      </div>";
            echo "      <label>Titolare Carta</label>";
            echo "      <input type='text' class='input-pay' placeholder='Nome Cognome' required>";
            echo "      <button type='submit' name='conferma_pagamento' class='btn-pay'>PAGA ORA</button>";
            echo "  </form>";
            echo "</div>";
            echo "<p style='font-size:0.8rem; color:#7f8c8d;'>Transazione protetta da crittografia end-to-end fittizia.</p>";
        }
        ?>
    </div>
</main>

<footer>
    <div class="footer-content">
        <p>&copy; 2026 ArteAri - Tutti i diritti riservati</p>
    </div>
</footer>

</body>
</html>