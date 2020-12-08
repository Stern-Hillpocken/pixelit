<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="icon" type="image/png" href="images/favicon.png" />
        <title>pixel it : score final</title>
    </head>
    <body>
      <h1>pixel it</h1>
      <div id="endscore">
        <?php
        // Récupération des scores
        $reponse = $bdd->prepare('SELECT pseudo, score, team FROM users WHERE lobby=:currentLobby ORDER BY score DESC');
        $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
        $position = 1;
        $tableScore = '<table>';
        // Affichage
        while ($donnees = $reponse->fetch()){
          $tableScore .= '<tr';
          if($position === 1){
            $tableScore .= ' style="border:4px solid red"';
          } else if($position === 2){
            $tableScore .= ' style="border:2px solid red"';
          } else if($position === 3){
            $tableScore .= ' style="border:2px dotted red"';
          }
          $tableScore .= '><td>'.$position.'</td><td>';
          if($donnees['pseudo'] === $_SESSION['pseudo']){
            $tableScore .= '<span class="highlight">'. htmlspecialchars($donnees['pseudo']).'</span>';
          } else {
            $tableScore .= htmlspecialchars($donnees['pseudo']);
          }
          $tableScore .= '</td><td><span title="Points">'.$donnees['score'] . '</span></td></tr>';
          $position++;
        }
        $reponse->closeCursor();
        echo $tableScore.'</table>';
        ?>
        <button onclick=""
        <?php
          if($_SESSION['pseudo'] !== $host){echo ' DISABLED';}
        ?>
        >Encore !</button>
    </div>
    </body>
    <footer>Version alpha !<br/>
      Code source sur github <a href="https://github.com/Stern-Hillpocken/pixelit">>></a></footer>
</html>
