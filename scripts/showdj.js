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
          dj.src = 'djimages/kayley.png';
        break;
		if ((hr === 21) { // 21:00 - 21:59 - 59 minutes - 0 hour
          dj.src = 'djimages/billy.png';
        break;
      case 2: // Tuesday
        if (hr === 8 && (mn >= 0 && mn <= 15)) // 8:00 - 8:15 - 15 minutes
          dj.src = 'djimages/kayley.png';
        break;
      case 3: // Wednesday
        if (hr === 8 && (mn >= 0 && mn <= 15)) // 8:00 - 8:15 - 15 minutes
          dj.src = 'djimages/off-air.jpg';
        break;
      case 4: // Thursday
        if (hr === 21) { // 21:00 - 21:59 - 60 minutes - 1 hour
          dj.src = 'djimages/strawbs.png';
          tx.innerHTML = 'Thursday at 9 PM<br>Request<br>With Strawbs';
        }
        break;
      case 5: // Friday
                if (hr === 13 || hr === 14) // 13:00 - 14:59 - 2 hours
          dj.src = 'djimages/djbunny.png';
          tx.innerHTML = 'Saturday at 8 AM<br>Christian Music<br>with Dj Bunny';
        break;
      case 6: // Saturday
        if (hr === 2 && mn >=03) { // 2:03 - 2:59 - 57 minutes
          dj.src = 'djimages/djbunny.png';
          tx.innerHTML = 'Saturday at 8 AM<br>Christian Music<br>with Dj Bunny';
        }
        break;
      case 0: // Sunday
        if (hr === 21) { // 21:00 - 21:59 - 60 minutes - 1 hour
          dj.src = 'djimages/strawbs.png';
          tx.innerHTML = 'Sunday at 9 PM<br>Request<br>with Strawbs';
        }
        break;
      default:
        dj.src = 'djimages/strawbs.png';
        tx.innerHTML = 'Thursday at 9 PM<br>Request<br>With Strawbs';
        break;
    }
  }

  setInterval(function() { onair(); }, 1000);
})(document);