<?php
require("connessioneDB.php");
session_start();

if (!isset($_SESSION["utente"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['azione']) && $_GET['azione'] == 'svuota') {
    unset($_SESSION['carrello']);
    header("Location: carrello.php");
    exit();
}

$totale_carrello = 0;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="immagini/icon.jpeg">
    <link rel="stylesheet" href="style.css">
    <title>ArteAri - Carrello</title>
    <style>
        .cart-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border: 2px solid #34495e;
            border-radius: 15px;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .cart-table th {
            border-bottom: 2px solid #34495e;
            padding: 15px;
            text-align: left;
            color: #7f8c8d;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .cart-table td {
            padding: 20px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .cart-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .cart-total-section {
            text-align: right;
            border-top: 2px solid #34495e;
            padding-top: 20px;
        }
        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #7b2cb1;
        }
        .cart-actions {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .btn-checkout {
            background-color: #7b2cb1;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-checkout:hover { background-color: #6a1b9a; }
        .btn-empty {
            color: #c62828;
            text-decoration: none;
            font-size: 0.9rem;
            align-self: center;
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
                <li><a href="shop.php">SHOP</a></li>
                <li><a href="indexLog.php">HOME</a></li>
                <li><a href="carrello.php" class="active">CARRELLO</a></li>
                <li><a href="account.php">IL MIO ACCOUNT</a></li>
            </ul>
        </nav>
        <div class="auth-section">
            <a href="index.html" class="auth-link">ESCI</a>
        </div>
    </div>
</header>

<main style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 81.7vh; padding-top: 1px;">
    <div class="cart-container">
        <h2 style="margin-bottom: 30px; color: #2c3e50;">Il tuo Carrello</h2>

        <table class="cart-table">
            <thead>
                <tr>
                    <th>Prodotto</th>
                    <th>Dettagli</th>
                    <th>Prezzo</th>
                    <th>Q.tà</th>
                    <th>Subtotale</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!isset($_SESSION['carrello']) || empty($_SESSION['carrello'])) {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 50px;'>Il tuo carrello è vuoto. <a href='shop.php'>Torna allo shop</a></td></tr>";
                } else {
                    foreach ($_SESSION['carrello'] as $indice => $item) {
                        $stmt = $con->prepare("SELECT nome, prezzo, linkImg FROM Prodotto WHERE idProdotto = ?");
                        $stmt->bind_param("i", $item['id']);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $p = $res->fetch_assoc();

                        $subtotale = $p['prezzo'] * $item['qta'];
                        $totale_carrello += $subtotale;

                        echo "<tr>";
                        echo "  <td>
                                    <div style='display:flex; align-items:center; gap:15px;'>
                                        <img src='" . $p['linkImg'] . "' class='cart-img'>
                                        <strong>" . htmlspecialchars($p['nome']) . "</strong>
                                    </div>
                                </td>";
                        echo "  <td style='font-size:0.9rem; color:#7f8c8d;'>
                                    Taglia: " . $item['taglia'] . "<br>
                                    Colore: " . $item['colore'];
                        if($item['img_custom'] != "") {
                            echo "<br><span style='color:#7b2cb1;'>✓ Immagine caricata</span>";
                        }
                        echo "  </td>";
                        echo "  <td>€ " . number_format($p['prezzo'], 2, ',', '.') . "</td>";
                        echo "  <td>" . $item['qta'] . "</td>";
                        echo "  <td><strong>€ " . number_format($subtotale, 2, ',', '.') . "</strong></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <?php
        if (isset($_SESSION['carrello']) && !empty($_SESSION['carrello'])) {
            echo "
            <div class='cart-total-section'>
                <span style='margin-right: 20px; color: #7f8c8d;'>Totale Ordine:</span>
                <span class='total-price'>€ " . number_format($totale_carrello, 2, ',', '.') . "</span>
            </div>

            <div class='cart-actions'>
                <a href='carrello.php?azione=svuota' class='btn-empty' onclick=\"return confirm('Vuoi davvero svuotare il carrello?')\">Svuota Carrello</a>
                <a href='procedi_ordine.php' class='btn-checkout'>PROCEDI ALL'ORDINE</a>
            </div>";
        }
        ?>
    </div>
</main>

<footer>
    <div class="footer-content">
        <p>&copy; 2026 ArteAri - Tutti i diritti riservati</p>
        <div class="footer-links">
            <a href="https://www.instagram.com/arteari_la.casa.del.ricamo/">Contatti</a>
        </div>
    </div>
</footer>

</body>
</html>