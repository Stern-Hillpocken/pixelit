<?php
session_start();
// Connexion à la base de données
include 'bdd-connexion.php';

if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){

	//$host
	$reponse = $bdd->prepare('SELECT pseudo FROM users WHERE lobby=:lobby ORDER BY ID LIMIT 0,1');
	$reponse->execute(array(':lobby' => $_SESSION['lobby']));
	while ($donnees = $reponse->fetch()){
		$host = $donnees['pseudo'];
	}
	$reponse->closeCursor();
	//$lobbyStatus
	$reponse = $bdd->prepare('SELECT status FROM lobbies WHERE name=:currentLobby');
  $reponse->execute(array(':currentLobby' => $_SESSION['lobby']));
  while ($donnees = $reponse->fetch()){
    $lobbyStatus = $donnees['status'];
  }
  $reponse->closeCursor();

	$time = date("Y-m-d H:i:s");

	if($_SESSION['pseudo'] === $host){

		if($lobbyStatus === 'drawing'){
			// Première fois
			$req = $bdd->prepare('UPDATE lobbies SET status = \'guessing\', startTime = :startTime, teamShow = 0 WHERE name = :currentLobby');
		  $req->execute(array('startTime' => $time, 'currentLobby' => $_SESSION['lobby']));
		  $req->closeCursor();

		} else {
			// Si pas la première fois
			// Où est-ce qu'on en est niveau teamShow
			include 'guessing-values.php';
			/* return
				$sortedPointsValue
				$teamShowSplit[0] et [1]
			*/
			// Combien de bonnes réponses pour ce dessin ?
			$req = $bdd->prepare('SELECT guess FROM users WHERE lobby = :currentLobby');
		  $req->execute(array('currentLobby' => $_SESSION['lobby']));
			$nbGuess = 0;
			while($donnees = $req->fetch()){
				if($donnees['guess'] === 'true'){
					$nbGuess++;
				}
			}
		  $req->closeCursor();

			if($nbGuess === 0){
				// Le mot n'a pas été trouvé --> $sortedPointsValue (nb de groupe)
				$nbGroup = count($sortedPointsValue);

				if($teamShowSplit[1] < $nbGroup){
				// Encore des dessins ou indices
				$req = $bdd->prepare('UPDATE lobbies SET teamShow = teamShow+1, startTime = :startTime WHERE name = :currentLobby');
				$req->execute(array('startTime' => $time, 'currentLobby' => $_SESSION['lobby'])) or die(print_r($bdd->errorInfo()));
				$req->closeCursor();
				}else{
					// Plus de dessins ou indices
					$nbGuess = 1;
				}
			}/*else*/
			if($nbGuess >= 1){
				// Le mot a été trouvé
				// Afficher la réponse
				$req = $bdd->prepare('UPDATE lobbies SET status =\'showAnswer\', startTime = :startTime WHERE name = :currentLobby');
				$req->execute(array('startTime' => $time, 'currentLobby' => $_SESSION['lobby'])) or die(print_r($bdd->errorInfo()));
				$req->closeCursor();
			}

		}

	}

}
?>
