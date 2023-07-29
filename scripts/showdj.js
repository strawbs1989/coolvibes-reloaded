(function( d ) {
   'use strict';
function onair(){
  
   var dj = d.querySelector('#dj'),
       nd = new Date(),
       dy = nd.getDay(),
       hr = nd.getHours(),
       mn = nd.getMinutes();

if(( dy === 1 && hr === 21 && mn >=00 )||
   ( dy === 1 && hr === 8 && mn <=00 )||
   ( dy === 1 && hr === 6 && mn >=20 )||
   ( dy === 1 && hr === 7 && mn <=19 )) {
     dj.src='https://github.com/strawbs1989/coolvibes-reloaded/tree/main/images/billy.png';
 }
else {
if(( dy === 2 && hr === 8 && mn <=00 )||
   ( dy === 2 && hr === 14 && mn <=59 )) {
     dj.src='https://github.com/strawbs1989/coolvibes-reloaded/tree/main/images/kayley.png''; 
 }
else {
if(( dy === 3 && hr === 8 && mn <=59 )||
   ( dy === 3 && hr === 9 && mn <=59 )) {
     dj.src='https://github.com/strawbs1989/coolvibes-reloaded/blob/main/images/off-air.jpg'';
 }
 else {
if(( dy === 4 && hr === 21 && mn <=00 )||
   ( dy === 4 && hr === 21 && mn <=00 )) {
     dj.src='https://github.com/strawbs1989/coolvibes-reloaded/tree/main/images/strawbs.png'';
 }
 else {
if(( dy === 5 && hr === 8 && mn <=59 )||
   ( dy === 5 && hr === 9 && mn <=59 )) {
     tx.innerHTML = 'Friday at 10 AM<br>Christian Music<br>with Dj Bunny';
 }
 else {
if(( dy === 6 && hr === 8 && mn <=59 )||
   ( dy === 6 && hr === 9 && mn <=59 )) {
     tx.innerHTML = 'Saturday at 8 AM<br>Christian Music<br>with Dj Bunny';
 }
 else {
if(( dy === 7 && hr === 21 && mn <=00 )||
   ( dy === 7 && hr === 9 && mn <=59 )) {
     dj.src = 'https://github.com/strawbs1989/coolvibes-reloaded/tree/main/images/strawbs.png';
            tx.innerHTML = 'Sunday at 9 PM<br>Request<br>with Strawbs';
 }
else {
     dj.src='https://github.com/strawbs1989/coolvibes-reloaded/blob/main/images/off-air.jpg';
    }
   }
  }
 }

setInterval( function(){ onair();}, 1000 );
}( document ));