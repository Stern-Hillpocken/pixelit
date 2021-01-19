<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="assets/style.css" />
        <link rel="icon" type="image/png" href="assets/favicon.png" />
        <title>pixelit : joue à deviner le pixel art</title>
    </head>
    <body>
      <h1>pixel<span class="highlight">it</span></h1>
      <div id="inscription">
        <details><summary>Comment jouer ?</summary>
          <p>
            <b>Nombre de joueur minimum :</b> 4<br/>
            <b>Durée de partie :</b> environ 2 minutes / joueurs<br/>
            <b>But du jeu :</b> Deviner le pixel art (dessin grâce à des pixels) des autres et faire deviner le sien pour marquer le plus de points.
          </p>
          <p>
            <span class="highlight">Pixel it</span> se déroule en plusieurs rondes divisés en <span class="highlight">deux phases</span> de jeu : la phase de <span class="highlight">dessin</span> et la phase de <span class="highlight">suppositions</span>.
          </p>
          <h2>La phase de dessin</h2>
          <p>
            Durant cette phase vous devez <span class="highlight">dessiner le mot qui vous est imposé</span> (il sera inscrit en haut de page). Utilisez les couleurs à votre disposition pour représenter ce mot sur votre grille. Dès que vous avez fini, appuyez sur le bouton 'Envoyer' en bas de page.<br/>
            <u>Bon à savoir :</u><br/>
            - Vous êtes limités en terme de quantité de peinture.<br/>
            - À droite du mot à représenter ce trouve une horloge qui peut être déclanchée si un autre joueur a fini avant vous. Il vous sera donc indiqué le temps restant avant que votre dessin ne soit automatiquement envoyé.<br/>
            <u>Conseils de pro :</u><br/>
            - Plus vous utilisez de peinture, plus vos 'points de couleurs' (affiché en bas) augmente. Essayez de le garder assez bas pour présenter votre oeuvre avant vos 'coéquipiers' disposant du même mot.<br/>
            - Sur la gauche se trouve le tableau de score, avec la composition des équipes entre crochets. Regardez qui est dans votre équipe et affinez votre startégie des 'points de couleurs' en fonction de vos 'coéquipiers'.
          </p>
          <h2>La phase de suppositions</h2>
          <p>
            Durant cette phase vous devrez deviner ce que les pixel arts des autres équipes représentent. Si vous pensez à quelque chose, <span class="highlight">écrivez le mot dans le chat</span> (à droite). Vous avez le droit à autant de propositions que souhaité durant le temps imparti (affiché en haut). Si vous trouvez le mot, un message dans le chat vous en tiendra informé et vous marquerez vos point à la fin du temps.<br/>
            Lorsque le temps est écoulé, deux possibilités peuvent advenir :<br/>
            1/ Au moins une personne a trouvé le bon mot : à ce moment là on passe au dessin d'un membre de l'équipe suivante (ou à une nouvelle ronde si toutes les équipes sont passées).<br/>
            2/ Personne n'a trouvé le mot : on recommence avec en plus un autre dessin d'un membre de l'équipe (ou un indice). Si même après l'indice personne n'a touvé, on passe à l'équipe suivante.<br/>
            <u>Bon à savoir :</u><br/>
            - Les dessins sont présentés par ordre croissant de 'points de couleurs'.<br/>
            - Chaque personne trouvant le mot marque 1 point.<br/>
            - Seul le(s) dernier(s) joueur(s) présenté(s) marque 1 point par personne ayant trouvé.<br/>
            - Quand tous les dessins d'une équipe ont été dévoilés, un indice doit être écrit dans le chat par un membre de cett équipe (le jeu vous dira quand).<br/>
            - Si l'indice est annoncé, les dessinateurs ne marquent pas de point.
          </p>
          <h2>Fin de partie</h2>
          <p>
            Un fois le nombre de rondes dépassé (en haut à gauche), la partie prend fin et le score est établit !
          </p>
          <h2>Jouer !</h2>
          <p>
            Pour jouer, entrez votre pseudo juste en dessous et appuyez sur 'Jouer'. Vous pourrez ensuite partager le lien de la partie une fois dans le lobby (affiché dans la barre d'url ou en bas de page).
          </p>
          <hr/>
        </details>
        <form action="post/login_post.php" method="post">
          <input type="text" name="pseudo" id="pseudo" placeholder="Pseudo ici..." maxlength="25" autofocus/>
          <input name="lobby" value=
            <?php
              echo '"'.$_SERVER['QUERY_STRING'].'"';
            ?>
          type="hidden" />
          <br/>
          <button type="submit">Jouer <span class="highlight">>></span></button>
        </form>
    </div>
    </body>
    <?php include('php/footer.php') ?>
</html>
