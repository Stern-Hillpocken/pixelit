<?php
session_start();
// Connexion à la base de données
include '../php/bdd-connexion.php';

////////////////////////////////////////////////////////////////////////////////
// LOGIN
////////////////////////////////////////////////////////////////////////////////
if(isset($_POST['pseudo'])){
  $pseudo = $_POST['pseudo'];
  // Nettoyer le pseudo
  while($pseudo[0] === ' '){
    $pseudo = substr($pseudo, 1, strlen($pseudo)-1);
  }
	if(strlen($pseudo) > 25){
		$pseudo = substr($pseudo, 0, 25);
	}
  // S'assurer qu'il n'est pas dans la BDD
	$reponse = $bdd->prepare('SELECT pseudo FROM users WHERE lobby = ?');
  $reponse->execute(array($_POST['lobby']));
	while ($donnees = $reponse->fetch()){
		if($donnees['pseudo'] === $pseudo){
      $pseudo = '';
    }
	}
	$reponse->closeCursor();
  // Check
  if($pseudo !== ''){
    $_SESSION['pseudo'] = $pseudo;
    $emptyGrid = '';
    for($i = 0; $i < 81; $i++){
      $emptyGrid.='0';
    }
    // Insertion du message à l'aide d'une requête préparée
    $req = $bdd->prepare('INSERT INTO users (pseudo, lobby, score, grid, guess, team) VALUES(?, ?, ?, ?, ?, ?)');
    $req->execute(array($_SESSION['pseudo'], '', 0, $emptyGrid, '', 0));
		$req->closeCursor();
  }
}

////////////////////////////////////////////////////////////////////////////////
// LOBBY
////////////////////////////////////////////////////////////////////////////////
$lobby = $_POST['lobby'];

// Check if lobbyExist
$lobbyExist = false;
$lobbyWaiting = false;
$req = $bdd->query('SELECT name, status FROM lobbies');
while($donnees = $req->fetch()){
	if($donnees['name'] === $lobby){
    $lobbyExist = true;
    if($donnees['status'] === 'lobby'){$lobbyWaiting = true;}
  }
}
$req->closeCursor();

if(($lobby === '' || strlen($lobby) !== 8 || $lobbyExist === false || $lobbyWaiting === false) AND isset($_SESSION['pseudo'])){
  // Générer un nouveau lobby
  $chars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
  $lobbyCreationExist = true;
  while($lobbyCreationExist === true){
    $lobbyCreationExist = false;
    //Créer
    $lobby = '';
    while(strlen($lobby) < 8){
      $randomChar = rand(0, count($chars)-1);
      $lobby.=$chars[$randomChar];
    }
  	// S'assurer que le lobby n'est pas dans la BDD
  	$req = $bdd->query('SELECT name FROM lobbies');
    while($donnees = $req->fetch()){
    	if($donnees['name'] === $lobby){
        $lobbyCreationExist = true;
        break;
      }
    }
    $req->closeCursor();
  }

	// Créer dans la BDD
	$time = date("Y-m-d H:i:s");
	$req = $bdd->prepare('INSERT INTO lobbies (name, status, rounds, timeDraw, timeAnswer, words, currentRound, teamShow, startTime, currentWords, lastTimestamp) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
	$req->execute(array($lobby, 'lobby', 3, 20, 30, '', 0, 0, $time, '', $time));
	$req->closeCursor();
}

// Y mettre le joueur
if(isset($_SESSION['pseudo'])){
	$req = $bdd->prepare('UPDATE users SET lobby = :lobby WHERE pseudo = :pseudo order by ID desc limit 1');
	$req->execute(array(
	  'lobby' => $lobby,
	  'pseudo' => $_SESSION['pseudo']
	  ));
	$req->closeCursor();
	$_SESSION['lobby'] = $lobby;
	header('Location: ./../?'.$_SESSION['lobby']);
} else {
	header('Location: ./../');
}
?>
