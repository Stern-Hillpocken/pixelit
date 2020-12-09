<script>
//Check is $lobbyStatus change --> refresh
$(document).ready(function(){
  function loopCheckTeamShow(){
    $.ajax({
        url : 'php/teamShow.php',
        type : 'GET',
        data : false,
        success : function(realTeamShow){
            if(realTeamShow !== '<?php echo $teamShow; ?>'){
              location.reload();
            }
        }
    });
    setTimeout(loopCheckTeamShow, 1000);
  }
  loopCheckTeamShow();
});
</script>
