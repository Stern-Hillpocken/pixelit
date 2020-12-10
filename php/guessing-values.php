<?php
$requete = $bdd->prepare('SELECT teamShow FROM lobbies WHERE name = :currentLobby');
$requete->execute(array('currentLobby' => $_SESSION['lobby']));

$teamShow = null;

while ($donnees = $requete->fetch()){
  $teamShow = $donnees['teamShow'];
}
$requete->closeCursor();

$teamShowSplit = array(intval(floor($teamShow/10)), intval($teamShow-floor($teamShow/10)*10));

$requete = $bdd->prepare('SELECT pseudo, grid FROM users WHERE lobby = :currentLobby AND team = :currentTeam ORDER BY ID');
$requete->execute(array('currentLobby' => $_SESSION['lobby'], 'currentTeam' => $teamShowSplit[0]));

$teamPseudo = array();
$teamGrid = array();

while ($donnees = $requete->fetch()){
  array_push($teamPseudo, $donnees['pseudo']);
  array_push($teamGrid, $donnees['grid']);
}
$requete->closeCursor();

// Calcul des points de couleurs de la grille
$teamGridPoints = array();
for($id = 0; $id < count($teamGrid); $id++){
  $teamGridPoints[$id] = 0;
  for($i = 0; $i < 81; $i++){
    if($teamGrid[$id][$i] === '1'){
      $teamGridPoints[$id] += 1;
    } else if($teamGrid[$id][$i] === '2'){
      $teamGridPoints[$id] += 4;
    }
  }
}

// Différentes valeurs de points
$sortedPointsValue = $teamGridPoints;
sort($sortedPointsValue);
$sortedPointsValue = array_unique($sortedPointsValue);
if($sortedPointsValue[0] === 0){
    array_splice($sortedPointsValue, 0, 1);
}

// $teamDisplayKey
$teamDisplayKey = array();
for($i = 0; $i <= $teamShowSplit[1]; $i++){// = for 0
  if($i < count($sortedPointsValue)){//Pour quand indice, ne pas avoir le dernier
    $teamDisplayKey = array_merge($teamDisplayKey,array_keys($teamGridPoints, $sortedPointsValue[$i]));
  }
  //$teamDisplayKey = array_keys($teamGridPoints, $sortedPointsValue[$teamShowSplit[1]]); //old
}
// $isClueTime
if($teamShowSplit[1] < count($sortedPointsValue)){
  $isClueTime = false;
}else{
  $isClueTime = true;
}
?>
