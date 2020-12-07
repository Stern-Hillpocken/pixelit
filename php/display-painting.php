<?php
if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){

  $requete = $bdd->prepare('SELECT teamShow FROM lobbies WHERE name = :currentLobby');
  $requete->execute(array('currentLobby' => $_SESSION['lobby']));

  $teamShow = null;

  while ($donnees = $requete->fetch()){
    $teamShow = $donnees['teamShow'];
  }
  $requete->closeCursor();

  $teamShowSplit = array(intval(floor($teamShow/10)), intval(($teamShow/10-floor($teamShow/10))*10));

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

  // Choisir la clée
  $minPointsKey = array_keys($teamGridPoints,min($teamGridPoints));
  $medPointsKey = array();
  for($i = 0; $i < count($teamGridPoints); $i++){
    if($teamGridPoints[$i] > min($teamGridPoints)){
      array_push($medPointsKey, $teamGridPoints[$i]);
    }
  }
  $medPointsKey = array_keys($medPointsKey,min($medPointsKey));
  if($teamShowSplit[1] === 0){
    $teamGridKey = $minPointsKey;
  } else if($teamShowSplit[1] === 1 AND count($medPointsKey) !== 0){
    $teamGridKey = $medPointsKey;
  } else {
    echo 'go to php/next-guessing';
    //include 'php/next-guessing.php';
  }
  // Retourner le(s) tableau(x)
  $teamGridTable = null;
  for($i = 0; $i < count($teamGridKey); $i ++){//Tous ceux qui ont fait le même nombre de points
    $teamGridTable .= '<table class="painting-show">';
    for($r = 0; $r < 9; $r++){
      $teamGridTable .= '<tr>';
      for($c = 0; $c < 9; $c++){
        $teamGridTable .= '<td style="background-color:';
        if($teamGrid[$teamGridKey[$i]][$r*9+$c] === '1'){$teamGridTable.='black';}
        else if($teamGrid[$teamGridKey[$i]][$r*9+$c] === '2'){$teamGridTable.='red';}
        else{$teamGridTable.='white';}
        $teamGridTable .= '"></td>';
      }
      $teamGridTable .= '</tr>';
    }
    $teamGridTable .= '</table><span class="artist">'.$teamPseudo[$teamGridKey[$i]].' ('.$teamGridPoints[$teamGridKey[$i]].' pts)</span>';
  }
  echo $teamGridTable;
}
?>
