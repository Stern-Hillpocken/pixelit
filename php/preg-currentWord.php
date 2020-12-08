<?php
// Récupération des mots à deviner
$reponse = $bdd->prepare('SELECT currentWords FROM lobbies WHERE name=:currentLobby');
$reponse->execute(array(':currentLobby' => $_SESSION['lobby']));
$currentWords;
while ($donnees = $reponse->fetch()){
  $currentWords = $donnees['currentWords'];
}
$reponse->closeCursor();
preg_match_all('/(\w+\s*\w+)(,|$)/', $currentWords, $out_preg);
$currentWordsArray = $out_preg[1];
// Récupération des scores
$reponse = $bdd->prepare('SELECT team FROM users WHERE pseudo=:pseudo ');
$reponse->execute(array(':pseudo' => $playerInvolved));
// Affichage
$currentWord = null;
while ($donnees = $reponse->fetch()){
  $currentWord = $currentWordsArray[$donnees['team']];
}
$reponse->closeCursor();
?>
