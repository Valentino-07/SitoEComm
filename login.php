<?php 
require("connessioneDB.php");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["invio-login"])){
        $email = htmlspecialchars($_POST["email-login"]);
        $password = $_POST["password-login"];

        $stmt = $con->prepare("SELECT email, livello, password_hash
                FROM Utente
                WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $utenti = $stmt->get_result();

        if($utenti && $utenti->num_rows > 0){
            $riga = $utenti->fetch_assoc();
            if(password_verify($password, $riga["password_hash"])){  
                unset($riga["password_hash"]);          
                $_SESSION["utente"] = $riga;
                
                if($_SESSION["utente"]["livello"] == 1)
                    header("Location: indexLog.html");
                elseif($_SESSION["utente"]["livello"] == 2)
                    header("Location: amministrazione.php");
                exit();
            }
            else{
                $errore = "Password errata!";
            }
        }
        else{
            $errore = "Devi ancora registrarti!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleLog.css">
    <title>ArteAri - Login</title>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-area">
                <img src="immagini/icon.jpeg" alt="Logo">
                <h1>ArteAri</h1>
            </div>
            
            <h2>Bentornato</h2>
            
            <?php if(isset($errore)){
                echo "<p class='error-msg'>" . $errore . "</p>";
            }
            ?>

            <form method="post" action="" name="dati-login">
                <div class="input-group">
                    <label for="email-login">Email</label>
                    <input type="text" name="email-login" id="email-login" placeholder="E-mail" required>
                </div>

                <div class="input-group">
                    <label for="password-login">Password</label>
                    <input type="password" name="password-login" id="password-login" placeholder="Password" required>
                </div>

                <input type="submit" id="invio-login" name="invio-login" value="ACCEDI">
            </form>
            
            <div class="footer-links">
                <p>Nuovo su ArteAri? <a href="registrazione.php">Iscriviti ora</a></p>
                <a href="index.html" class="back-home">Torna alla Home</a>
            </div>
        </div>
    </div>
</body>
</html>