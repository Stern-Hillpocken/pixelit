<?php
if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){

  include 'php/guessing-values.php';

  // Retourner le(s) tableau(x)
  $teamGridTable = '';
  for($i = 0; $i < count($teamDisplayKey); $i ++){//Tous ceux qui ont fait le même nombre de points
    $teamGridTable .= '<div style="display:inline-block"><table class="painting-show">';
    for($r = 0; $r < 9; $r++){
      $teamGridTable .= '<tr>';
      for($c = 0; $c < 9; $c++){
        $teamGridTable .= '<td style="background-color:';
        if($teamGrid[$teamDisplayKey[$i]][$r*9+$c] === '1'){$teamGridTable.='black';}
        else if($teamGrid[$teamDisplayKey[$i]][$r*9+$c] === '2'){$teamGridTable.='red';}
        else{$teamGridTable.='white';}
        $teamGridTable .= '"></td>';
      }
      $teamGridTable .= '</tr>';
    }
    $teamGridTable .= '</table><span class="artist">'.$teamPseudo[$teamDisplayKey[$i]].' ('.$teamGridPoints[$teamDisplayKey[$i]].' pts)</span></div> ';
  }

  $display = '';
  if($isClueTime === true){
    $display = '<p>Indice !<br/>Un membre de l\'équipe (';
    for($i = 0; $i < count($teamPseudo); $i++){
      $display .= '<span class="highlight">'.$teamPseudo[$i].'</span>';
      if($i < count($teamPseudo)-1){$display .= ', ';}
    }
    $display .= ') doit donner un mot indice dans le chat !</p>';
  }

  echo $teamGridTable.$display;

}
?>
