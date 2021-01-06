<?php
  $req = $bdd->prepare('SELECT rounds, timeDraw, timeAnswer FROM lobbies WHERE name = :currentLobby');
  $req->execute(array('currentLobby' => $_SESSION['lobby']));
  while($donnees = $req->fetch()){
    $rounds = $donnees['rounds'];
    $timeDraw = $donnees['timeDraw'];
    $timeAnswer = $donnees['timeAnswer'];
  }
  $req->closeCursor();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="assets/style.css" />
        <link rel="icon" type="image/png" href="assets/favicon.png" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <?php include 'ajax/check-lobby-status.php'; ?>
        <title>pixelit : en attente...</title>
    </head>
    <body>
      <h1>pixel<span class="highlight">it</span></h1>
      <div id="lobby">
        <form action="post/start_post.php" method="post">
          <label for="rounds">Rounds :</label><input id="rounds" name="rounds" type="number" value="<?php echo $rounds; ?>" min="1" max="9"
          <?php
            if($_SESSION['pseudo'] !== $host){echo ' DISABLED';}
          ?>
          />
          <label for="timeDraw">Temps pour finir son dessin (secondes) :</label><input id="timeDraw" name="timeDraw" type="number" value="<?php echo $timeDraw; ?>" min="5" max="99"
          <?php
            if($_SESSION['pseudo'] !== $host){echo ' DISABLED';}
          ?>
          />
          <label for="timeAnswer">Temps pour les propositions (secondes) :</label><input id="timeAnswer" name="timeAnswer" type="number" value="<?php echo $timeAnswer; ?>" min="5" max="99"
          <?php
            if($_SESSION['pseudo'] !== $host){echo ' DISABLED';}
          ?>
          />
          <label for="words">Mots :</label><textarea id="words" name="words" maxlength="65000" placeholder="Mots à faire deviner séparés par une virgule..."
          <?php
            if($_SESSION['pseudo'] !== $host){echo ' DISABLED';}
          ?>
          ></textarea>
          <label for="add-words">Ajouter des mots pré-enregistrés :</label> <input id="add-words" name="add-words" type="checkbox" style="width:16px;height:16px"
          <?php
            if($_SESSION['pseudo'] !== $host){echo ' DISABLED';}
          ?>
          />
          <input name="lobby" value=
            <?php
              echo '"'.$_SERVER['QUERY_STRING'].'"';
            ?>
          type="hidden" />
          <button type="submit"
          <?php
            if($_SESSION['pseudo'] !== $host){echo ' DISABLED';}
          ?>
          >Jouer <span class="highlight">>></span></button>
        </form>
        <div id="players-in-lobby">
        </div>
        <script>
$(document).ready(function(){
          function loadNewPlayersInLobby(){
            var premierID = $('#players-in-lobby span:last').attr('id'); // on récupère l'id le plus récent
            $.ajax({
                url : 'php/players-in-lobby.php?id=' + premierID, // on passe l'id le plus récent au fichier de chargement
                type : 'GET',
                success : function(html){
                    $('#players-in-lobby').append(html);
                }
            });
            setTimeout(loadNewPlayersInLobby, 3000);
          }
          loadNewPlayersInLobby();
});

/*function copyLink(){
  navigator.clipboard.writeText(document.getElementById('link-to-copy').innerText);
}*/
function copyLink() {
  var copyText = document.getElementById('link-to-copy');
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand('copy');
}
        </script>
        <p style="width:100%; text-align:center">
          Lien vers le lobby :<br/>
          <!--<span class="highlight"><?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?></span>-->
          <input style="color:red;text-align:center;" type="text" value="<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" id="link-to-copy">
          <span class="pseudo-button" onclick="copyLink()"><svg viewBox="0 0 24 24">
    <path d="M19,21H8V7H19M19,5H8A2,2 0 0,0 6,7V21A2,2 0 0,0 8,23H19A2,2 0 0,0 21,21V7A2,2 0 0,0 19,5M16,1H4A2,2 0 0,0 2,3V17H4V3H16V1Z" /></svg> Copier</span>
        </p>
      </div>
      <?php include('php/disconnection.php') ?>
    </body>
</html>
