(function( d ) {
   'use strict';
function onair(){
  
   var dj = d.querySelector('#dj'),
       nd = new Date(),
       dy = nd.getDay(),
       hr = nd.getHours(),                                  
       mn = nd.getMinutes();
else {
if(( dy === 1 && hr === 21 && mn <=00 )||
   ( dy === 1 && hr === 8 && mn <=15 )) {
     dj.src='images/billy.png';
 }
 else {
if(( dy === 2 && hr === 8 && mn <=00 )||
   ( dy === 2 && hr === 8 && mn <=15 )) {
     dj.src='images/kayley.png';
 }
 else {
if(( dy === 3 && hr === 8 && mn <=00 )||
   ( dy === 3 && hr === 8 && mn <=15 )) {
     dj.src='images/off-air.png';
 }
if ( dy === 4 && hr === 21 && mn <=12 ) {
     'Thursday at 9 PM<br>Request<br>With Strawbs'dj.src='images/strawbs.png';
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
if 
   ( dy === 7 && hr === 21 && mn <=00 )) {
     'Sundayday at 9 PM<br>Request<br>with Strawbs'dj.src='images/strawbs.png';
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