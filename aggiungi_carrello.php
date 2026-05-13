<?php
require("connessioneDB.php");
session_start();
if (!isset($_SESSION["utente"])) {
    header("Location: login.php");
    exit();
}
//per il ricamo personalizzato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_prod = (int)$_POST['id_prodotto'];
    $taglia = $_POST['taglia'];
    $colore = $_POST['colore'];
    $qta = (int)$_POST['qta'];
    $nome_file_custom = "";
    if (isset($_FILES['img_custom']) && $_FILES['img_custom']['error'] == 0) {
        $cartella_upload = "uploads/";
        if (!is_dir($cartella_upload)) {
            mkdir($cartella_upload, 0777, true);
        }
        $estensione = pathinfo($_FILES['img_custom']['name'], PATHINFO_EXTENSION);
        $nome_file_custom = "custom_" . time() . "_" . $id_prod . "." . $estensione;
        move_uploaded_file($_FILES['img_custom']['tmp_name'], $cartella_upload . $nome_file_custom);
    }
    $nuovo_articolo = array(
        "id" => $id_prod,
        "taglia" => $taglia,
        "colore" => $colore,
        "qta" => $qta,
        "img_custom" => $nome_file_custom
    );
    if (!isset($_SESSION['carrello'])) {
        $_SESSION['carrello'] = array();
    }
    $trovato = false;
    foreach ($_SESSION['carrello'] as $index => $item) {
        if ($item['id'] == $id_prod && $item['taglia'] == $taglia && $item['colore'] == $colore) {
            $_SESSION['carrello'][$index]['qta'] += $qta;
            $trovato = true;
            break;
        }
    }
    if (!$trovato) {
        $_SESSION['carrello'][] = $nuovo_articolo;
    }
    $_SESSION['success_msg'] = "Prodotto aggiunto correttamente al carrello!";
    header("Location: shop.php");
    exit();
}
?>