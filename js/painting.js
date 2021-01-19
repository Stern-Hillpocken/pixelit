let colorPoints;
let colorInt;
let colorValue = ['white', 'black', 'red'];
let colorPool;
let painting;
clearPainting();

function clearPainting(){
  coloPoints = 0;
  colorInt = 1;
  colorPool = [81,20,1];

  painting = '';
  for(let i = 0; i < 81; i++){
    painting += '0';
  }
  updatePainting();
}

function updatePainting(){
  //options
  let optionsTable = '<img alt="erase" src="assets/erase.png" style="cursor:pointer" onclick="clearPainting()"/>';
  for(let i = 0; i < 3; i++){
    optionsTable += ' <span style="position:relative;cursor:pointer"><img alt="'+colorValue[i]+'-color" src="assets/'+colorValue[i]+'-color.png" onclick="changeColor('+i+')"';
    if(i === colorInt){
      optionsTable += ' class="color-selected"';
    }
    optionsTable += '/>';
    if(i > 0){
      optionsTable += '<span class="color-quantity" onclick="changeColor('+i+')">x'+colorPool[i]+'</span>';
    }
    optionsTable += '</span>';
  }
  //table
  let paintingTable = '';
  for(let r = 0; r < 9; r++){
    paintingTable += '<tr>';
    for(let c = 0; c < 9; c++){
      paintingTable += '<td style="background-color:'+colorValue[painting[(r*9+c)]]+'" onclick="paintColor('+(r*9+c)+')"></td>';
    }
    paintingTable += '</tr>';
  }
  //points
  colorPoints = (81-colorPool[0])*0+(20-colorPool[1])*1+(1-colorPool[2])*4;
  //
  document.getElementById("painting-options").innerHTML = optionsTable;
  document.getElementById("painting").innerHTML = paintingTable;
  document.getElementById("color-points").innerHTML = colorPoints+' pt';
  if(colorPoints >= 2){document.getElementById("color-points").innerHTML += 's';}
  document.getElementById("sended-painting").value = painting;
}

function paintColor(pos){
  if(colorPool[colorInt] > 0 || (colorInt === 1 && painting[pos] === '1')){
    //gain color
    if(painting[pos] === '1'){
      colorPool[1] ++;
    } else if (painting[pos] === '2'){
      colorPool[2] ++;
    } else {
      colorPool[0] ++;
    }

    if(colorInt === 1 && painting[pos] === '1'){
      //black+black --> white
      painting = painting.substring(0, pos)+'0'+painting.substring(pos+1, painting.length);
      colorPool[0] --;
    }else{
      //loose color
      painting = painting.substring(0, pos)+colorInt+painting.substring(pos+1, painting.length);
      colorPool[colorInt] --;
    }

  } else if (colorInt === 2){//no colorPool but red
    //remove red
    painting = painting.replace('2', '0');//once
    //add red
    painting = painting.substring(0, pos)+'2'+painting.substring(pos+1, painting.length);
  }
  updatePainting();
}

function changeColor(i){
  colorInt = i;
  updatePainting();
}

updatePainting();
