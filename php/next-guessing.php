<?php
session_start();
// Connexion à la base de données
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=pixelit_database;charset=utf8', 'root', '');
}
catch(Exception $e)
{
  die('Erreur : '.$e->getMessage());
}

if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){

  $req = $bdd->prepare('UPDATE lobbies SET status = \'guessing\', startTime = 0, teamShow = :teamShow  WHERE name = :currentLobby');
  $req->execute(array(
    'teamShow' => 0,
    'currentLobby' => $_SESSION['lobby']
    ));
    $req->closeCursor();

}
?>
