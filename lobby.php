<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="icon" type="image/png" href="images/favicon.png" />
        <title>pixel it : en attente...</title>
    </head>
    <body>
      <h1>pixel it</h1>
      <div id="lobby">
        <form action="post/start_post.php" method="post">
          <label for="timeDraw">Temps dessins (secondes) :</label><input id="timeDraw" name="timeDraw" type="number" value="30" min="5" max="99"/>
          <label for="timeAnswer">Temps propositions (secondes) :</label><input id="timeAnswer" name="timeAnswer" type="number" value="30" min="5" max="99"/>
          <label for="words">Mots :</label><textarea id="words" name="words" maxlength="65000" placeholder="Mots à faire deviner séparés par une virgule..."></textarea>
          <input name="lobby" value=
            <?php
              echo '"'.$_SERVER['QUERY_STRING'].'"';
            ?>
          type="hidden" />
          <button type="submit">Jouer <span class="highlight">>></span></button>
        </form>
        <div>
          <?php
          // Récupération
          $reponse = $bdd->prepare('SELECT pseudo FROM users WHERE lobby=:currentLobby ORDER BY ID');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          // Affichage
          while ($donnees = $reponse->fetch()){
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              echo '<span class="highlight">'.htmlspecialchars($donnees['pseudo']).'</span> ';
            } else {
              echo '<u>'.htmlspecialchars($donnees['pseudo']).'</u> ';
            }
          }
          $reponse->closeCursor();
          ?>
        </div>
        <p>Lien vers le lobby : <span class="highlight">
          <?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>
        </span></p>
    </div>
    </body>
</html>
