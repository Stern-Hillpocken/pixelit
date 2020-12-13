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
}

if($_SESSION['pseudo'] === $host){

	$req = $bdd->prepare('SELECT pseudo FROM users WHERE lobby = :currentLobby');
	$req->execute(array('currentLobby' => $_SESSION['lobby']));
	$nbPlayers = 0;
	while($donnees = $req->fetch()){
		$nbPlayers ++;
	}

	if($nbPlayers >= 4){
		// Préparer les rounds et le temps
		$rounds = intval($_POST['rounds']);
		if($rounds === '' || $rounds < 1){$rounds = 3;}
		$timeDraw = intval($_POST['timeDraw']);
		if($timeDraw === '' || $timeDraw < 5){$timeDraw = 5;}
		$timeAnswer = intval($_POST['timeAnswer']);
		if($timeAnswer === '' || $timeAnswer < 5){$timeAnswer = 5;}

		// Préparer les mots, plus de 8 mots
		$words = $_POST['words'];
		preg_match_all('/(\w+\s*\w+)(,|$)/', $words, $out_preg);
		if(count($out_preg[1]) < 8 OR (isset($_POST['add-words']) AND $_POST['add-words'] === 'checked')){
			$words .= ', bambou, pinocchio, serpent, oeil, stylo, souris, échec, biberon, poule, skateboard, vache, choppe, soleil, voiture, parapluie, scie, poisson, chat, ampoule, fleur, téléphone, oeuf, arc, étoile, vélo, casserole, nez, arbre, monocle, lunettes, prise';
		}
		$words = strtolower($words);

		// Nettoyer les scores
		$req = $bdd->prepare('UPDATE users SET score = 0 WHERE lobby = :currentLobby');
		$req->execute(array('currentLobby' => $_SESSION['lobby']));
		$req->closeCursor();

		// Passer en game
		$req = $bdd->prepare('UPDATE lobbies SET status = \'drawing\', rounds = :rounds, timeDraw = :timeDraw, timeAnswer = :timeAnswer, words = :words, currentRound = 0 WHERE name = :currentLobby');
		$req->execute(array(
		  'currentLobby' => $_SESSION['lobby'],
			'rounds' => $rounds,
			'timeDraw' => $timeDraw,
			'timeAnswer' => $timeAnswer,
			'words' => $words
		  ));
		$req->closeCursor();

		// Commencer le round
		include 'newround_post.php';
	}
}
// Actualiser
header('Location: ./../?'.$_SESSION['lobby']);
?>
