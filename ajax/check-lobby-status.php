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
