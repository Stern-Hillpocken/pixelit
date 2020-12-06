<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="icon" type="image/png" href="images/favicon.png" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script>
        //Check is $lobbyStatus change --> refresh
        $(document).ready(function(){
          function loopCheckLobbyStatus(){
            $.ajax({
                url : 'php/lobby-status.php',
                type : 'GET',
                data : false,
                success : function(realStatus){
                    if(realStatus !== '<?php echo $lobbyStatus; ?>'){
                      location.reload();
                    }
                }
            });
            setTimeout(loopCheckLobbyStatus, 1000);
          }
          loopCheckLobbyStatus();
        });
        </script>
        <title>pixel it : en attente...</title>
    </head>
    <body>
      <h1>pixel it</h1>
      <div id="lobby">
        <form action="post/start_post.php" method="post">
          <label for="rounds">Rounds :</label><input id="rounds" name="rounds" type="number" value="3" min="1" max="9"
          <?php
            if($_SESSION['pseudo'] !== $host){echo ' DISABLED';}
          ?>
          />
          <label for="timeDraw">Temps dessins (secondes) :</label><input id="timeDraw" name="timeDraw" type="number" value="30" min="5" max="99"
          <?php
            if($_SESSION['pseudo'] !== $host){echo ' DISABLED';}
          ?>
          />
          <label for="timeAnswer">Temps propositions (secondes) :</label><input id="timeAnswer" name="timeAnswer" type="number" value="30" min="5" max="99"
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
        </script>
        <p style="width:100%; text-align:center">
          Lien vers le lobby :<br/>
          <span class="highlight">
          <?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>
          </span>
        </p>
      </div>
    </body>
</html>
