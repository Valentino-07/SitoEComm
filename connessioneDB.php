<?php
require("DB_config.php");
$con=new mysqli($db_host,$db_user,$db_password,$db_name);
if(!$con){
    die("Errore della connessione:". $con->connection_error);
}
?>