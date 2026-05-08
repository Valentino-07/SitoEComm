<?php
require("connessioneDB.php");
session_start();

if (isset($_GET['elimina_id'])) {
    $id_da_cancellare = (int) ($_GET['elimina_id']);
    $stmt = $con->prepare("DELETE FROM Prodotto WHERE idProdotto = ?");
    $stmt->bind_param("i", $id_da_cancellare);
    $stmt->execute();
    
    header("Location: amministrazione.php");
    exit();
}

$query = $con->query("SELECT idProdotto, nome, prezzo, descrizione FROM Prodotto");
$prodotti = $query->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleAdmin.css">
    <title>ArteAri - Amministrazione</title>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Pagina Amministratore</h1>
            <div class="header-actions">
                <span>Profilo Admin</span>
                <a href="index.html" class="btn-logout">Esci</a>
            </div>
        </header>

        <main class="content-card">
            <h2>Gestione Catalogo Prodotti</h2>
            
            <table class="prodotti-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Prezzo</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(empty($prodotti)) {
                        echo "<tr><td colspan='4'>Nessun prodotto presente nel database.</td></tr>";
                    } else {
                        foreach ($prodotti as $p){
                            echo "<tr>
                                    <td>" . $p['idProdotto'] . "</td>
                                    <td>" . htmlspecialchars($p['nome']) . "</td>
                                    <td>€ " . number_format($p['prezzo'], 2, ',', '.') . "</td>
                                    <td class='actions'>
                                        <a href='modificaProdotto.php?id=" . $p['idProdotto'] . "' class='btn-edit'>
                                            Modifica
                                        </a>
                                        <a href='amministrazione.php?elimina_id=" . $p['idProdotto'] . "'
                                        class='btn-delete'
                                        onclick=\"return confirm('Sei sicuro di voler eliminare il prodotto " . addslashes($p['nome']) . "?')\">
                                            Elimina
                                        </a>
                                    </td>
                                </tr>";
                        }
                    } 
                    ?>
                </tbody>
            </table>

            <div class="footer-actions">
                <a href="nuovo-prodotto.php" class="btn-new">+ Nuovo Prodotto</a>
            </div>
        </main>
    </div>
</body>
</html>