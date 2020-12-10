<?php
session_start();
// Connexion à la base de données
include 'bdd-connexion.php';

if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){

  $requete = $bdd->prepare('SELECT status, startTime, timeDraw, timeAnswer FROM lobbies WHERE name = :currentLobby');
  $requete->execute(array('currentLobby' => $_SESSION['lobby']));

	$status = null;
  $startTime = null;
  $timeDraw = null;
	$timeAnswer = null;

  while ($donnees = $requete->fetch()){
		$status = $donnees['status'];
    $startTime = $donnees['startTime'];
    $timeDraw = $donnees['timeDraw'];
		$timeAnswer = $donnees['timeAnswer'];
  }
  $requete->closeCursor();

	if($status === 'drawing'){
		echo strtotime($startTime).' '.$timeDraw; // retour à AJAX
	} else if($status === 'guessing'){
		echo strtotime($startTime).' '.$timeAnswer; // retour à AJAX
	} else {
		echo strtotime($startTime).' '.'10'; // retour à AJAX, 10 secondes de débat sur la solution
	}

}
?>
