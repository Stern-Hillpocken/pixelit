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

  $requete = $bdd->prepare('SELECT startTime, timeDraw FROM lobbies WHERE name = :currentLobby');
  $requete->execute(array('currentLobby' => $_SESSION['lobby']));

  $startTime = null;
  $timeDraw = null;

  while ($donnees = $requete->fetch()){
    $startTime = $donnees['startTime'];
    $timeDraw = $donnees['timeDraw'];
  }
  $requete->closeCursor();

  echo strtotime($startTime).' '.$timeDraw; // retour à AJAX
}
?>
