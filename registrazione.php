<?php
require("connessioneDB.php");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["invio-registrazione"])){
        $email = htmlspecialchars($_POST["email-reg"]);
        $password = $_POST["password-reg"];
        $conferma_password = $_POST["conferma-password-reg"];
        if($password !== $conferma_password){
            $errore = "Le password non coincidono!";
        } else {
            $stmt_check = $con->prepare("SELECT email FROM Utente WHERE email = ?");
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $risultato_check = $stmt_check->get_result();
            if($risultato_check->num_rows > 0){
                $errore = "Email già registrata!";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $livello_default = 1;
                $stmt_ins = $con->prepare("INSERT INTO Utente (email, password_hash, livello) VALUES (?, ?, ?)");
                $stmt_ins->bind_param("ssi", $email, $password_hash, $livello_default);
                if($stmt_ins->execute()){
                    header("Location: login.php?registrato=1");
                    exit();
                } else {
                    $errore = "Errore durante la registrazione. Riprova.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleReg.css">
    <title>ArteAri - Registrazione</title>
</head>
<body>
    <div class="registrazione-container">
        <div class="registrazione-card">
            <div class="logo-area">
                <img src="immagini/icon.jpeg" alt="Logo">
                <h1>ArteAri</h1>
            </div>
            
            <h2>Crea il tuo account</h2>
            
            <?php if(isset($errore)): ?>
                <p class="error-msg"><?php echo $errore; ?></p>
            <?php endif; ?>

            <form method="post" action="" name="dati-registrazione">
                <div class="input-group">
                    <label for="email-reg">Email</label>
                    <input type="email" name="email-reg" id="email-reg" placeholder="Inserisci la tua e-mail" required>
                </div>

                <div class="input-group">
                    <label for="password-reg">Password</label>
                    <input type="password" name="password-reg" id="password-reg" placeholder="Crea una password" required>
                </div>

                <div class="input-group">
                    <label for="conferma-password-reg">Conferma Password</label>
                    <input type="password" name="conferma-password-reg" id="conferma-password-reg" placeholder="Ripeti la password" required>
                </div>

                <input type="submit" id="invio-registrazione" name="invio-registrazione" value="REGISTRATI">
            </form>
            
            <div class="footer-links">
                <p>Hai già un account? <a href="login.php">Accedi qui</a></p>
                <a href="index.html" class="back-home">Torna alla Home</a>
            </div>
        </div>
    </div>
</body>
</html>