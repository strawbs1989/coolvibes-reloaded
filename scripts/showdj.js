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
        if ((hr === 8) { // 8:00 - 8:59 - 60 minutes - 1 hour
          dj.src = 'https://drive.google.com/file/d/1K6Ki9fxFxVo3UBlA0fjPzLv6D_EpKwIX/view?usp=drive_link';
        break;
      case 2: // Tuesday
        if (hr === 8 && (mn >= 0 && mn <= 15)) // 8:00 - 8:15 - 15 minutes
          dj.src = 'https://drive.google.com/file/d/17QW0zuz_PDgUZgXvCz7ybvK9acYraK4K/view?usp=drive_link';
        break;
      case 3: // Wednesday
        if (hr === 8 && (mn >= 0 && mn <= 15)) // 8:00 - 8:15 - 15 minutes
          dj.src = 'https://drive.google.com/file/d/1D77ONt7JnNX1rTpNZd6lLYzfTCrXEAcJ/view?usp=drive_link';
        break;
      case 4: // Thursday
        if (hr === 21) { // 21:00 - 21:59 - 60 minutes - 1 hour
          dj.src = 'https://drive.google.com/file/d/1SaYUrmEwalwxXwygXP0ZthYLKW7aji_H/view?usp=drive_link';
          tx.innerHTML = 'Thursday at 9 PM<br>Request<br>With Strawbs';
        }
        break;
      case 5: // Friday
                if (hr === 13 || hr === 14) // 13:00 - 14:59 - 2 hours
          tx.innerHTML = 'Friday at 10 AM<br>Christian Music<br>with Dj Bunny';
        break;
      case 6: // Saturday
        if (hr === 2 && mn >=03) { // 2:03 - 2:59 - 57 minutes
          dj.src = 'https://drive.google.com/file/d/1D77ONt7JnNX1rTpNZd6lLYzfTCrXEAcJ/view?usp=drive_link';
          tx.innerHTML = 'Saturday at 8 AM<br>Christian Music<br>with Dj Bunny';
        }
        break;
      case 0: // Sunday
        if (hr === 21) { // 21:00 - 21:59 - 60 minutes - 1 hour
          dj.src = 'https://drive.google.com/file/d/1SaYUrmEwalwxXwygXP0ZthYLKW7aji_H/view?usp=drive_link';
          tx.innerHTML = 'Sunday at 9 PM<br>Request<br>with Strawbs';
        }
        break;
      default:
        dj.src='https://drive.google.com/file/d/1D77ONt7JnNX1rTpNZd6lLYzfTCrXEAcJ/view?usp=drive_link';
        break;
    }
  }

  setInterval(function() { onair(); }, 1000);
})(document);