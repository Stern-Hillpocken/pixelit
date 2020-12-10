<?php
session_start();
// Connexion à la base de données
include '../php/bdd-connexion.php';

if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){

  $reponse = $bdd->prepare('SELECT pseudo FROM users WHERE lobby=:lobby ORDER BY ID LIMIT 0,1');
	$reponse->execute(array(':lobby' => $_SESSION['lobby']));
	while ($donnees = $reponse->fetch()){
		$host = $donnees['pseudo'];
	}
	$reponse->closeCursor();

  if($_SESSION['pseudo'] === $host){
    $req = $bdd->prepare('UPDATE lobbies SET status = \'lobby\' WHERE name = :currentLobby');
  	$req->execute(array('currentLobby' => $_SESSION['lobby']));
    $req->closeCursor();
  }

  // Actualiser
  header('Location: ./../?'.$_SESSION['lobby']);
}
?>
