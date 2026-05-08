<?php
require("connessioneDB.php");
session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: shop.php");
    exit();
}

$idProdotto = (int)$_GET['id'];

$stmt = $con->prepare("SELECT nome, descrizione, prezzo, linkImg FROM Prodotto WHERE idProdotto = ?");
$stmt->bind_param("i", $idProdotto);
$stmt->execute();
$risultato = $stmt->get_result();

if ($risultato->num_rows === 0) {
    header("Location: shop.php");
    exit();
}

$prodotto = $risultato->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="immagini/icon.jpeg">
    <link rel="stylesheet" href="style.css">
    <title>ArteAri - <?php echo htmlspecialchars($prodotto['nome']); ?></title>
    <style>
        .detail-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border: 2px solid #34495e;
            border-radius: 15px;
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        .product-visual {
            flex: 1;
            min-width: 300px;
        }

        .product-visual img {
            width: 100%;
            border: 2px solid #34495e;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .description-box {
            border: 2px solid #34495e;
            padding: 15px;
            border-radius: 10px;
            min-height: 100px;
        }

        .custom-form {
            flex: 1;
            min-width: 300px;
            border: 2px solid #34495e;
            padding: 25px;
            border-radius: 10px;
            background-color: #fcfcfc;
        }

        .form-title {
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .input-item {
            margin-bottom: 15px;
        }

        .input-item label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .input-item select, .input-item input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn-add-cart {
            width: 100%;
            padding: 15px;
            background-color: #7b2cb1;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btn-add-cart:hover {
            background-color: #6a1b9a;
        }
    </style>
</head>
<body>

<header>
    <div class="header-container">
        <a id="home" href="indexLog.html">
            <div class="logo">
                <img src="immagini/icon.jpeg" alt="Logo">
                <h1>ArteAri</h1>
            </div>
        </a>
        <nav>
            <ul>
                <li><a href="indexLog.html">HOME</a></li>
                <li><a href="shop.php" class="active">SHOP</a></li>
                <li><a href="#">CARRELLO</a></li>
                <li><a href="account.php">IL MIO ACCOUNT</a></li>
            </ul>
        </nav>
        <div class="auth-section">
            <?php 
            if(isset($_SESSION["utente"])){
                echo '<a href="index.html" class="auth-link">ESCI</a>';
            } else {
                echo '<a href="login.php" class="auth-link">ACCEDI</a>';
            }
            ?>
        </div>
    </div>
</header>

<main style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 81.7vh; padding-top: 1px;">
    <div class="detail-container">
        
        <div class="product-visual">
            <img src="<?php echo htmlspecialchars($prodotto['linkImg']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>">
            <div class="description-box">
                <strong>Descrizione:</strong><br>
                <?php echo nl2br(htmlspecialchars($prodotto['descrizione'])); ?>
            </div>
        </div>

        <div class="custom-form">
            <div class="form-title">Personalizza il tuo prodotto</div>
            <form action="aggiungi_carrello.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_prodotto" value="<?php echo $idProdotto; ?>">
                
                <div class="input-item">
                    <label for="taglia">Taglia (Altezza in cm)</label>
                    <select name="taglia" id="taglia" required>
                        <option value="" disabled selected>-- Seleziona taglia --</option>
                        <option value="50cm">50 cm (0-1 mesi)</option>
                        <option value="56cm">56 cm (1-2 mesi)</option>
                        <option value="62cm">62 cm (2-4 mesi)</option>
                        <option value="68cm">68 cm (4-6 mesi)</option>
                        <option value="74cm">74 cm (6-9 mesi)</option>
                        <option value="80cm">80 cm (9-12 mesi)</option>
                        <option value="86cm">86 cm (12-18 mesi)</option>
                        <option value="92cm">92 cm (18-24 mesi)</option>
                        <option value="98cm">98 cm (2-3 anni)</option>
                        <option value="104cm">104 cm (3-4 anni)</option>
                        <option value="110cm">110 cm (4-5 anni)</option>
                        <option value="116cm">116 cm (5-6 anni)</option>
                    </select>
                </div>

                <div class="input-item">
                    <label for="colore">Colore</label>
                    <select name="colore" id="colore" required>
                        <option value="" disabled selected>-- Seleziona colore --</option>
                        <option value="Bianco">Bianco</option>
                        <option value="Rosa">Rosa</option>
                        <option value="Blu">Blu</option>
                        <option value="Verde">Verde</option>
                        <option value="Giallo">Giallo</option>
                        <option value="Rosso">Rosso</option>
                    </select>
                </div>

                <div class="input-item">
                    <label for="qta">Quantità</label>
                    <input type="number" name="qta" id="qta" value="1" min="1" required>
                </div>

                <div class="input-item">
                    <label for="img_custom">Inserisci Immagine ricamo personalizzato</label>
                    <input type="file" name="img_custom" id="img_custom" accept="image/*">
                </div>

                <button type="submit" class="btn-add-cart">AGGIUNGI AL CARRELLO</button>
            </form>
        </div>

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