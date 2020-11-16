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

// MAJ des infos du lobby
$lobby = $_POST['lobby'];
if(strlen($lobby) === 8){//TODO:et que le lobby existe et qu'on est le top membre

	// Préparer le temps
	$timeDraw = $_POST['timeDraw'];
	if($timeDraw === '' || $timeDraw < 5 || !is_int($timeDraw)){$timeDraw = 5;}
	$timeAnswer = $_POST['timeAnswer'];
	if($timeAnswer === '' || $timeAnswer < 5 || !is_int($timeAnswer)){$timeAnswer = 5;}

	// Passer en game
	$req = $bdd->prepare('UPDATE lobbies SET status = \'game\', timeDraw = :timeDraw, timeAnswer = :timeAnswer, answer = :answer  WHERE name = :currentLobby');
	$req->execute(array(
	  'currentLobby' => $lobby,
		'timeDraw' => $timeDraw,
		'timeAnswer' => $timeAnswer,
		'answer' => 'motAFaireDeviner'
	  ));
}
// Actualiser
header('Location: ./../?'.$lobby);
?>
