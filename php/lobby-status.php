<?php
session_start();
// Connexion à la base de données
include 'bdd-connexion.php';

if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){

  $requete = $bdd->prepare('SELECT status FROM lobbies WHERE name = :currentLobby');
  $requete->execute(array('currentLobby' => $_SESSION['lobby']));

  $realStatus = null;

  while ($donnees = $requete->fetch()){
    $realStatus = $donnees['status'];
  }
  $requete->closeCursor();

  echo $realStatus; // retour à AJAX
}
?>
