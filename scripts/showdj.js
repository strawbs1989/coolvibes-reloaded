(function(d) {
    function onair() {
      const dj = d.querySelector('#dj'),
            tx = document.createElement('div'),
            nd = new Date(),
            dy = nd.getDay(),
            hr = nd.getHours(),                               
            mn = nd.getMinutes();
        
      tx.style.color = 'white';
      dj.insertAdjacentElement('afterend', tx);

      switch (dy) {
        case 1: // Monday
          if ((hr === 21 && mn <=59) || (hr === 8 && mn <=15))
            dj.src = 'https://github.com/strawbs1989/coolvibes-reloaded/tree/main/images/billy.png';
          break;
        case 2: // Tuesday
          if ((hr === 8 && mn >=0) || (hr === 8 && mn <=15))
            dj.src = 'https://github.com/strawbs1989/coolvibes-reloaded/tree/main/images/kayley.png';
          break;
        case 3: // Wednesday
          if ((hr === 8 && mn >=0) || (hr === 8 && mn <=15))
            dj.src = 'https://github.com/strawbs1989/coolvibes-reloaded/tree/main/images/off-air.png';
          break;
        case 4: // Thursday
          if (hr === 21 && mn >=0) {
            dj.src = 'https://github.com/strawbs1989/coolvibes-reloaded/tree/main/images/strawbs.png';
            tx.innerHTML = 'Thursday at 9 PM<br>Request<br>With Strawbs';
          }
          break;
        case 5: // Friday
          if ((hr === 13 && mn <=59) || (hr === 14 && mn <=59))
            tx.innerHTML = 'Friday at 10 AM<br>Christian Music<br>with Dj Bunny';
          break;
        case 6: // Saturday
          if ((hr === 0 && mn <=14) || (hr === 9 && mn <=15))
            tx.innerHTML = 'Saturday at 12 PM<br>Christian Music<br>with Dj Bunny';
          break;
        case 0: // Sunday
          if (hr === 21 && mn >=0) {
            dj.src = 'https://github.com/strawbs1989/coolvibes-reloaded/tree/main/images/strawbs.png';
            tx.innerHTML = 'Sunday at 9 PM<br>Request<br>with Strawbs';
          }
          break;
        default:
          dj.src='images/off-air.png';
          break;
      }
    }
 
    setInterval(function() { onair(); }, 1000);
  })(document);