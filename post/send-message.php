<?php
session_start();
// Connexion à la base de données
include '../php/bdd-connexion.php';

// Envoyer un message
if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby']) AND isset($_POST['message']) AND !empty($_POST['message'])){
	// Récupérer le status
	$req = $bdd->prepare('SELECT status FROM lobbies WHERE name = :currentLobby');
	$req->execute(array('currentLobby' => $_SESSION['lobby'])) or die(print_r($bdd->errorInfo()));
	$status = null;
	while ($donnees = $req->fetch()){
		$status = $donnees['status'];
	}
	$req->closeCursor();

	if($status === 'guessing'){
		// Vérifier si c'est la bonne réponse
		include '../php/guessing-values.php';
		/* RETURN
		$teamPseudo (array)
		$teamShowSplit (array [team;phase])
		$teamDisplayKey (array)
		$isClueTime (bool)
		*/
		// Récupérer le mot
		$playerInvolved = $teamPseudo[0];
		include '../php/preg-currentWord.php';
		//return $currentWord

		// Récupérer la team du joueur messagers et si il a déjà trouvé
		$req = $bdd->prepare('SELECT team, guess FROM users WHERE pseudo = :pseudo');
		$req->execute(array('pseudo' => $_SESSION['pseudo'])) or die(print_r($bdd->errorInfo()));
		$playerInvolvedTeam = null;
		$playerInvolvedGuess = null;
		while($donnees = $req->fetch()){
			$playerInvolvedTeam = intval($donnees['team']);
			$playerInvolvedGuess = $donnees['guess'];
		}
		$req->closeCursor();

		// Il ne faut pas qu'on soit dans la team du mot et que ce soit le bon mot
		$_POST['message'] = strtolower($_POST['message']);
		if($playerInvolvedTeam !== $teamShowSplit[0] AND $_POST['message'] === $currentWord AND $playerInvolvedGuess !== 'true'){
			// Pour le messager
			$req = $bdd->prepare('UPDATE users SET score = score+1, guess = \'true\' WHERE pseudo = :pseudo');
			$req->execute(array('pseudo' => $_SESSION['pseudo'])) or die(print_r($bdd->errorInfo()));
			$req->closeCursor();
			// Pour le/les dessinateur(s)
			if($isClueTime === false){
				$lastTDK = $teamDisplayKey[count($teamDisplayKey)-1];
				$currentTDValue = $teamGridPoints[$lastTDK];
				$realKey = array();
				$realKey = array_keys($teamGridPoints, $currentTDValue);
				for($i = 0; $i < count($realKey); $i++){
					$req = $bdd->prepare('UPDATE users SET score = score+1 WHERE pseudo = :pseudo');
					$req->execute(array('pseudo' => $teamPseudo[$realKey[$i]])) or die(print_r($bdd->errorInfo()));
					$req->closeCursor();
				}
			}
			echo '<div id="word-finded">Trouvé : '.$currentWord.' !</div>';
		}else{
			// Insertion du message à l'aide d'une requête préparée
			$req = $bdd->prepare('INSERT INTO minichat (pseudo, message, lobby) VALUES(?, ?, ?)');
			$req->execute(array($_SESSION['pseudo'], $_POST['message'], $_SESSION['lobby'])) or die(print_r($bdd->errorInfo()));
			$req->closeCursor();
		}
	}

}

?>
