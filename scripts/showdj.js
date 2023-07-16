(function( d ) {
   'use strict';
function onair(){
  
   var dj = d.querySelector('#dj'),
       nd = new Date(),
       dy = nd.getDay(),
       hr = nd.getHours(),
       mn = nd.getMinutes();

if ( dy === 4 && hr === 20 && mn <=12 ) {
   ( dy === 4 && hr === 7 && mn <=19 )||
   ( dy === 4 && hr === 6 && mn >=20 )||
   ( dy === 4 && hr === 3 && mn <=19 ) 
     dj.src='images/strawbs.gif';
 }
else {
if(( dy === 5 && hr === 13 && mn <=59 )||
   ( dy === 5 && hr === 14 && mn <=59 )) {
     'Friday at 10 AM<br>Christian Music<br>with Dj Bunny'; 
 }
else {
if(( dy === 6 && hr === 8 && mn <=14 )||
   ( dy === 6 && hr === 9 && mn <=15 )) {
     'Saturday at 8 AM<br>Christian Music<br>with Dj Bunny'; 
 }
 else {
if(( dy === 1 && hr === 8 && mn <=14 )||
   ( dy === 1 && hr === 8 && mn <=15 )) {
     dj.src='images/kayley.jpg';
 }
else {
     dj.src='images/off-air.png';
  
    }
   }
  }
 }

setInterval( function(){ onair();}, 1000 );
}
}( document )) ;