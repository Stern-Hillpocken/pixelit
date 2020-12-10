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
	// Sélectionner les données
	$reponse = $bdd->prepare('SELECT * FROM lobbies WHERE name=:lobby');
	$reponse->execute(array(':lobby' => $_SESSION['lobby']));
	while ($donnees = $reponse->fetch()){
		$words = $donnees['words'];
	}
	$reponse->closeCursor();
	// Nombre de joueurs
	$reponse = $bdd->prepare('SELECT pseudo FROM users WHERE lobby=:lobby');
	$reponse->execute(array(':lobby' => $_SESSION['lobby']));
	$pseudo = array();
	while ($donnees = $reponse->fetch()){
		array_push($pseudo,$donnees['pseudo']);
	}
	$reponse->closeCursor();
	// Modifier
	$nbTeam;
	$compo = array();
	for($i = 0; $i < count($pseudo); $i++){
		if($i === count($pseudo)-1){
			array_push($compo, $compo[count($compo)-1]);
			$nbTeam = $compo[count($compo)-1]+1;
		} else if($i%2 === 0){
			array_push($compo, $i/2);
		} else {
			array_push($compo, floor($i/2));
		}
	}
	$team = array();
	while(count($compo) > 0){
		$rand = rand(0,count($compo)-1);
		array_push($team, $compo[$rand]);
		array_splice($compo, $rand, 1);
	}
	// Passer $words en tableau
	$wordsArray = array();
	preg_match_all('/(\w+\s*\w+)(,|$)/', $words, $out_preg);
	$wordsArray = $out_preg[1];
	//
	$currentWords = array();
	while(count($currentWords) < $nbTeam){
		$rand = rand(0,count($wordsArray)-1);
		array_push($currentWords, $wordsArray[$rand]);
		array_splice($wordsArray, $rand, 1);
	}
	$currentWordsString = null;
	for($i = 0; $i < count($currentWords); $i++){
		$currentWordsString .= $currentWords[$i];
		if($i !== count($currentWords)-1){
			$currentWordsString .= ', ';
		}
	}
	$emptyGrid = '';
	for($i = 0; $i < 81; $i++){
		$emptyGrid .= '0';
	}

	// Pousser et passer en drawing phase
	$req = $bdd->prepare('UPDATE lobbies SET status = \'drawing\', currentRound = currentRound+1, teamShow = 0, startTime = 0, currentWords = :currentWords WHERE name = :currentLobby');
	$req->execute(array(
		'currentWords' => $currentWordsString,
	  'currentLobby' => $_SESSION['lobby']
	  ));
	$req->closeCursor();

	// Pousser pour les joueurs : grid, guess, team
	for($i = 0; $i < count($pseudo); $i++){
		$req = $bdd->prepare('UPDATE users SET grid = :grid, guess = \'\', team = :team WHERE pseudo = :pseudo');
		$req->execute(array(
			'grid' => $emptyGrid,
			'team' => $team[$i],
		  'pseudo' => $pseudo[$i]
		  ));
		$req->closeCursor();
	}
}
?>
