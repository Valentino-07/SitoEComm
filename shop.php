<?php
require("connessioneDB.php");
session_start();
$query = $con->query("SELECT idProdotto, nome, linkImg, prezzo FROM Prodotto");
$prodotti = $query->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="immagini/icon.jpeg">
    <link rel="stylesheet" href="style.css">
    <title>ArteAri - Shop</title>
    <style>
        .shop-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 2px solid #34495e; /* Richiamo i bordi del wireframe */
        }

        .shop-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #34495e;
        }

        .shop-header h2 {
            color: #2c3e50;
            font-size: 2rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            justify-items: center;
        }

        .product-card {
            width: 100%;
            background: #fff;
            border: 2px solid #34495e; 
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .product-image-container {
            height: 220px;
            border-bottom: 2px solid #34495e;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .product-image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 15px;
            text-align: left; /* Allineamento del testo come nel disegno */
        }

        .product-name {
            font-size: 1.2rem;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .product-price {
            margin-top: 5px;
            color: #7b2cb1;
            font-weight: bold;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #27ae60;
            border: 2px solid #27ae60;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
            animation: fadeIn 0.5s ease-in-out;
        }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
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
            if (isset($_SESSION["utente"])) {
                echo '<a href="index.html" class="auth-link">ESCI</a>';
            } else {
                echo '<a href="login.php" class="auth-link">ACCEDI</a>';
            }
            ?>
        </div>
    </div>
</header>

<main>
    <div class="shop-container">
        
        <div class="shop-header">
            <h2>Benvenuto nello shop di ArteAri</h2>
        </div>

        <?php
        if (isset($_SESSION['success_msg'])) {
            echo '<div class="alert-success">✓ ' . htmlspecialchars($_SESSION['success_msg']) . '</div>';
            unset($_SESSION['success_msg']); 
        }
        ?>

        <div class="product-grid">
            <?php
            if (empty($prodotti)) {
                echo "<p style='grid-column: 1 / -1; text-align: center; color: #7f8c8d;'>Nessun prodotto presente a catalogo.</p>";
            } else {
                foreach ($prodotti as $p) {
                    $img_src = !empty($p['linkImg']) ? htmlspecialchars($p['linkImg'], ENT_QUOTES) : 'https://via.placeholder.com/300x200?text=IMG';
                    
                    echo '<a href="dettaglio_prodotto.php?id=' . $p['idProdotto'] . '" class="product-card">';
                    echo '  <div class="product-image-container">';
                    echo '      <img src="' . $img_src . '" alt="' . htmlspecialchars($p['nome'], ENT_QUOTES) . '">';
                    echo '  </div>';
                    echo '  <div class="product-info">';
                    echo '      <div class="product-name">' . htmlspecialchars($p['nome'], ENT_QUOTES) . '</div>';
                    echo '      <div class="product-price">€ ' . number_format($p['prezzo'], 2, ',', '.') . '</div>';
                    echo '  </div>';
                    echo '</a>';
                }
            }
            ?>
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