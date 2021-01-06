<?php
session_start();
// Connexion à la base de données
include '../php/bdd-connexion.php';

$reponse = $bdd->prepare('SELECT pseudo, team FROM users WHERE lobby=:lobby ORDER BY ID LIMIT 0,1');
$reponse->execute(array(':lobby' => $_SESSION['lobby']));
while ($donnees = $reponse->fetch()){
  $host = $donnees['pseudo'];
}
$reponse->closeCursor();

if(isset($_SESSION['pseudo']) AND $_SESSION['pseudo'] === $host){
  $req = $bdd->prepare('UPDATE users SET lobby = :lobby WHERE pseudo = :pseudo AND lobby = :currentLobby');
	$req->execute(array(
	  'lobby' => '',
	  'pseudo' => $_GET['pseudo'],
    'currentLobby' => $_SESSION['lobby']
	  ));
	$req->closeCursor();
}

header('Location: ./../');

?>
