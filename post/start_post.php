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
	$reponse = $bdd->prepare('SELECT pseudo FROM users WHERE lobby=:lobby ORDER BY ID LIMIT 0,1');
	$reponse->execute(array(':lobby' => $_SESSION['lobby']));
	while ($donnees = $reponse->fetch()){
		$host = $donnees['pseudo'];
	}
	$reponse->closeCursor();
}

if($_SESSION['pseudo'] === $host){

	// Préparer les rounds et le temps
	$rounds = $_POST['rounds'];
	if($rounds === '' || $rounds < 1 || !is_int($rounds)){$rounds = 3;}
	$timeDraw = $_POST['timeDraw'];
	if($timeDraw === '' || $timeDraw < 5 || !is_int($timeDraw)){$timeDraw = 5;}
	$timeAnswer = $_POST['timeAnswer'];
	if($timeAnswer === '' || $timeAnswer < 5 || !is_int($timeAnswer)){$timeAnswer = 5;}

	// Préparer les mots, plus de 8 mots
	$words = $_POST['words'];
	preg_match_all('/(\w+\s*\w+)(,|$)/', $words, $out_preg);
	if(count($out_preg[1]) < 8 OR (isset($_POST['add-words']) AND $_POST['add-words'] === 'checked')){
		$words .= ', champignon, bamboo, pinocchio, serpent, main, oeil, stylo, souris';
	}
	$words = strtolower($words);

	// Passer en game
	$req = $bdd->prepare('UPDATE lobbies SET status = \'drawing\', rounds = :rounds, timeDraw = :timeDraw, timeAnswer = :timeAnswer, words = :words, startTime = :startTime  WHERE name = :currentLobby');
	$req->execute(array(
	  'currentLobby' => $_SESSION['lobby'],
		'rounds' => $rounds,
		'timeDraw' => $timeDraw,
		'timeAnswer' => $timeAnswer,
		'words' => $words,
		'startTime' => date("Y-m-d H:i:s")
	  ));

	// Commencer le round
	include 'newround_post.php';
}
// Actualiser
header('Location: ./../?'.$_SESSION['lobby']);
?>
