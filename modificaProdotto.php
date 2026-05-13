<?php
require("connessioneDB.php");
session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: amministrazione.php");
    exit();
}

$idProdotto = (int)$_GET['id'];
$messaggio_feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["modifica-prodotto"])) {
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'];
    $prezzo = (float)$_POST['prezzo'];
    $qta = (int)$_POST['qta'];
    $linkImg = $_POST['linkImg'];
    $idfCategoria = (int)$_POST['idfCategoria'];

    $stmt_update = $con->prepare("UPDATE Prodotto SET idfCategoria = ?, nome = ?, descrizione = ?, prezzo = ?, qta = ?, linkImg = ? WHERE idProdotto = ?");
    $stmt_update->bind_param("issdisi", $idfCategoria, $nome, $descrizione, $prezzo, $qta, $linkImg, $idProdotto);
    
    if ($stmt_update->execute()) {
        $messaggio_feedback = "Query eseguita.";
    } else {
        $messaggio_feedback = "Errore nell'esecuzione della query: " . $stmt_update->error;
    }
}

$stmt_select = $con->prepare("SELECT * FROM Prodotto WHERE idProdotto = ?");
$stmt_select->bind_param("i", $idProdotto);
$stmt_select->execute();
$risultato = $stmt_select->get_result();

if ($risultato->num_rows === 0) {
    header("Location: amministrazione.php");
    exit();
}

$prodotto = $risultato->fetch_assoc();

$query_categorie = $con->query("SELECT * FROM Categoria");
$categorie = $query_categorie->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleAdmin.css">
    <title>ArteAri - Modifica Prodotto</title>
    <style>
        .form-group { 
            margin-bottom: 15px;
            display: flex; 
            flex-direction: column;
        }
        .form-group label {
            font-weight: 600; 
            margin-bottom: 5px; 
            color: #2c3e50; 
        }
        .form-group input, .form-group textarea, .form-group select { 
            padding: 8px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            font-family: inherit;
        }
        .msg-feedback { 
            padding: 10px; 
            margin-bottom: 15px; 
            background-color: #d4edda; 
            color: #155724; 
            border-radius: 4px; 
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Modifica Prodotto ID: <?php echo $idProdotto; ?></h1>
            <div class="header-actions">
                <a href="amministrazione.php" class="btn-logout">Torna alla lista prodotti</a>
            </div>
        </header>
        <main class="content-card">
            <?php
                if (!empty($messaggio_feedback)) {
                    echo '<div class="msg-feedback">' . htmlspecialchars($messaggio_feedback) . '</div>';
                }
            ?>
            <form method="post" action="modificaProdotto.php?id=<?php echo $idProdotto; ?>">     
                
                <div class="form-group">
                    <label>ID Prodotto</label>
                    <input type="text" value="<?php echo $prodotto['idProdotto']; ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="nome">Nome Prodotto</label>
                    <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($prodotto['nome'], ENT_QUOTES); ?>" required>
                </div>

                <div class="form-group">
                    <label for="idfCategoria">Categoria di appartenenza</label>
                    <select name="idfCategoria" id="idfCategoria" required>
                        <option value="" disabled>-- Seleziona la categoria --</option>
                        <?php
                            foreach ($categorie as $cat) {
                                $chiavi = array_keys($cat);
                                $nomePK = $chiavi[0];
                                
                                if ($cat[$nomePK] == $prodotto['idfCategoria']) {
                                    $selected = 'selected';
                                } else {
                                    $selected = '';
                                }
                                
                                echo '<option value="' . htmlspecialchars($cat[$nomePK], ENT_QUOTES) . '" ' . $selected . '>' . htmlspecialchars($cat['nome_categoria'], ENT_QUOTES) . '</option>';
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descrizione">Descrizione</label>
                    <textarea name="descrizione" id="descrizione" rows="4" required><?php echo htmlspecialchars($prodotto['descrizione'], ENT_QUOTES); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="prezzo">Prezzo</label>
                    <input type="number" name="prezzo" id="prezzo" step="0.01" value="<?php echo $prodotto['prezzo']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="qta">Quantità residua</label>
                    <input type="number" name="qta" id="qta" value="<?php echo $prodotto['qta']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="linkImg">URL immagine</label>
                    <input type="url" name="linkImg" id="linkImg" value="<?php echo htmlspecialchars($prodotto['linkImg'], ENT_QUOTES); ?>">
                </div>

                <div class="footer-actions">
                    <button type="submit" name="modifica-prodotto" class="btn-edit">Esegui!!!</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>