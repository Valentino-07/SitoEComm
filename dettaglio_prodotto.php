<?php
require("connessioneDB.php");
session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: shop.php");
    exit();
}

$idProdotto = (int)$_GET['id'];

$stmt = $con->prepare("
    SELECT P.*, C.nome_categoria 
    FROM Prodotto P 
    JOIN Categoria C ON P.idfCategoria = C.idCategoria 
    WHERE P.idProdotto = ?
");
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

        .input-item select, 
        .input-item input { 
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
        <a id="home" href="indexLog.php">
            <div class="logo">
                <img src="immagini/icon.jpeg" alt="Logo">
                <h1>ArteAri</h1>
            </div>
        </a>
        <nav>
            <ul>
                <li><a href="indexLog.php">HOME</a></li>
                <li><a href="shop.php" class="active">SHOP</a></li>
                <li><a href="carrello.php">CARRELLO</a></li>
                <li><a href="account.php">IL MIO ACCOUNT</a></li>
            </ul>
        </nav>
        <div class="auth-section">
            <?php 
            if(isset($_SESSION["utente"])){
                echo '<a href="logout.php" class="auth-link">ESCI</a>';
            } else {
                echo '<a href="login.php" class="auth-link">ACCEDI</a>';
            }
            ?>
        </div>
    </div>
</header>

<main>
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

                <?php
                if ($prodotto['nome_categoria'] == 'Grembiuli') {
                    echo '<div class="input-item">
                            <label>Taglia (Altezza in cm)</label>
                            <select name="attributi_dinamici[taglia]" required>
                                <option value="" disabled selected>-- Seleziona taglia --</option>
                                <option value="86cm">86 cm (12-18 mesi)</option>
                                <option value="92cm">92 cm (18-24 mesi)</option>
                                <option value="98cm">98 cm (2-3 anni)</option>
                                <option value="104cm">104 cm (3-4 anni)</option>
                                <option value="110cm">110 cm (4-5 anni)</option>
                                <option value="116cm">116 cm (5-6 anni)</option>
                            </select>
                          </div>';

                    echo '<div class="input-item">
                            <label>Colore</label>
                            <select name="attributi_dinamici[colore]" required>
                                <option value="" disabled selected>-- Seleziona colore --</option>
                                <option value="Bianco">Bianco</option>
                                <option value="Rosa">Rosa</option>;
                                <option value="Blu">Blu</option>
                                <option value="Verde">Verde</option>
                                <option value="Giallo">Giallo</option>
                                <option value="Rosso">Rosso</option>
                            </select>
                          </div>';

                    echo '<div class="input-item">
                            <label>Immagine ricamo personalizzato</label>
                            <input type="file" name="file_dinamici[immagine_ricamo]" accept="image/*">
                          </div>';

                } 
                else if ($prodotto['nome_categoria'] == 'Cucito' || $prodotto['nome_categoria'] == 'Set Asilo') {
                    echo '<div class="input-item">
                            <label>Taglia</label>
                            <select name="attributi_dinamici[taglia]" required>
                                <option value="S">Small</option>
                                <option value="M">Medium</option>
                                <option value="L">Large</option>
                            </select>
                          </div>';

                    echo '<div class="input-item">
                            <label>Colore Stoffa</label>
                            <select name="attributi_dinamici[colore]" required>
                                <option value="Bianco">Bianco</option>
                                <option value="Rosa">Rosa</option>;
                                <option value="Blu">Blu</option>
                                <option value="Verde">Verde</option>
                                <option value="Giallo">Giallo</option>
                                <option value="Rosso">Rosso</option>
                            </select>
                          </div>';
                } 
                else {
                    if(!empty($prodotto['attributi_form'])){
                        $attributi = json_decode($prodotto['attributi_form'], true);
                    }
                    else{
                        $attributi = [];
                    }
                    if (!empty($attributi)) {
                        foreach ($attributi as $nome_campo => $valori) {
                            $nome_input = strtolower(str_replace(' ', '_', $nome_campo));
                            echo '<div class="input-item"><label>' . htmlspecialchars($nome_campo) . '</label>';
                            if (is_array($valori)) {
                                echo '<select name="attributi_dinamici[' . $nome_input . ']" required><option value="" disabled selected>-- Seleziona --</option>';
                                foreach ($valori as $opzione) { echo '<option value="' . htmlspecialchars($opzione) . '">' . htmlspecialchars($opzione) . '</option>'; }
                                echo '</select>';
                            } else if ($valori === "file") {
                                echo '<input type="file" name="file_dinamici[' . $nome_input . ']" accept="image/*">';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<p style="font-size:0.8rem; color:#7f8c8d; margin-bottom:15px;">Nessuna personalizzazione richiesta.</p>';
                    }
                }
                ?>

                <div class="input-item">
                    <label for="qta">Quantità</label>
                    <input type="number" name="qta" id="qta" value="1" min="1" required>
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