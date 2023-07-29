(function( d ) {
   'use strict';
function onair(){
 
   var dj = d.querySelector('#dj'),
       nd = new Date(),
       dy = nd.getDay(),
       hr = nd.getHours(),
       mn = nd.getMinutes();

if(( dy === 1 && hr === 8 && mn >=0 )||
   ( dy === 1 && hr === 18 && mn <=20 )||
    {
     dj.src='djimages/kayley.png';
 }
 else {
 if(( dy === 2 && hr === 8 && mn >=0 )||
    ( dy === 2 && hr === 18 && mn <=20 )||
    {
     dj.src='djimages/kayley.png';
 }	 
else {
if(( dy === 3 && hr === 13 && mn <=59 )||
   ( dy === 3 && hr === 20 && mn <=59 )) {
     dj.src='djimages/off-air.jpg';
 }
else {
if(( dy === 4 && hr === 8 && mn <=0 )||
   ( dy === 4 && hr === 9 && mn <=59 )) {
     dj.src='djimages/strawbs.png';
else {
if(( dy === 5 && hr === 21 && mn >=0 )||
   ( dy === 5 && hr === 18 && mn <=20 )||
    {
     dj.src='djimages/off-air.jpg';
else }
if(( dy === 6 && hr === 21 && mn >=0 )||
   ( dy === 6 && hr === 18 && mn <=20 )||
    {
     dj.src='djimages/off-air.jpg';

else  }
if(( dy === 7 && hr === 21 && mn >=0 )||
   ( dy === 7 && hr === 22 && mn <=0 )||
    {
     dj.src='djimages/strawbs.png';
 }
else {
     dj.src='djimages/off-air.jpg';
    }
   }
  }
 }

setInterval( function(){ onair();}, 1000 );
}( document ));