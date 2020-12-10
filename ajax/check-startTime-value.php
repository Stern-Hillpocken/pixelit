<script>
//Check is $lobbyStatus change --> refresh
$(document).ready(function(){
  function loopCheckStartTimeValue(){
    $.ajax({
        url : 'php/startTime-value.php',
        type : 'GET',
        data : false,
        success : function(times){
          let startTime = parseInt(times.match(/^[0-9]*/)[0]);
          let maxTime = parseInt(times.match(/[0-9]*$/)[0]);
            if(startTime > 0){
              var d = new Date();
              var n = d.getTime();
              var timeRemaining = startTime + maxTime - Math.floor(n/1000);
              if(timeRemaining >= 0){
                document.getElementById('time').textContent = timeRemaining;
              }
              if(timeRemaining === 0 && '<?php echo $lobbyStatus; ?>' === 'drawing'){
                document.getElementById('sended-painting-form').submit();
              }
              if(timeRemaining <= -1 && '<?php echo $_SESSION['pseudo']; ?>' === '<?php echo $host; ?>'){
                //On passe en guessing
                if('<?php echo $lobbyStatus; ?>' === 'drawing' || '<?php echo $lobbyStatus; ?>' === 'guessing'){
                  $.ajax({
                      url : 'php/next-guessing.php',
                      type : 'GET',
                      data : false
                  });
                } else {
                  $.ajax({
                      url : 'php/next-guessing-next.php',
                      type : 'GET',
                      data : false
                  });
                }
              }
            }
          }
    });
    setTimeout(loopCheckStartTimeValue, 1000);
  }

  loopCheckStartTimeValue();

});
</script>
