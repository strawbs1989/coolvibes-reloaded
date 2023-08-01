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
          dj.src = 'djimages/kayley.jpg';
       
        if ((hr === 13) { // 13:00 - 13:59 - 60 minutes - 1 hour
	  dj.src = 'djimages/djbunny.jpg';
          tx.innerHTML = 'Monday at 1 PM<br>Auto<br>With Bunny';

        if ((hr === 21) { // 21:00 - 21:59 - 60 minutes - 1 hour
          dj.src = 'djimages/billy.jpg';

        break;
      case 2: // Tuesday
        if (hr === 8 && (mn >= 0 && mn <= 15)) // 8:00 - 8:15 - 15 minutes
          dj.src = 'djimages/kayley.jpg';
		  

		  if ((hr === 13) { // 13:00 - 13:59 - 60 minutes - 1 hour
		  dj.src = 'djimages/djbunny.jpg';
          tx.innerHTML = 'Tuesday at 1 PM<br>Auto<br>With Bunny';
		  
		  if ((hr === 17) { // 17:00 - 17:59 - 60 minutes - 1 hour
		  dj.src = 'djimages/djbunny.jpg';

	      if ((hr === 13) { // 13:00 - 13:59 - 60 minutes - 1 hour
	        dj.src = 'djimages/djbunny.jpg';
            tx.innerHTML = 'Tuesday at 1 PM<br>Auto<br>With Bunny';
		  
		  if ((hr === 17) { // 17:00 - 17:59 - 60 minutes - 1 hour
			dj.src = 'djimages/djbunny.jpg';
			tx.innerHTML = 'Tuesday at 5 PM<br>Auto<br>With Bunny';
		  
        break;
      case 3: // Wednesday
        if (hr === 8 && (mn >= 0 && mn <= 15)) // 8:00 - 8:15 - 15 minutes
          dj.src = 'djimages/off-air.jpg';
		  

	    if ((hr === 15) { // 15:00 - 15:59 - 60 minutes - 1 hour
		  dj.src = 'djimages/djbunny.jpg';

	    if ((hr === 15) { // 15:00 - 15:59 - 60 minutes - 1 hour
        tx.innerHTML = 'Wednesday at 3 PM<br>Auto<br>With Bunny';
		  
        break;
      case 4: // Thursday
        if (hr === 21) { // 21:00 - 21:59 - 60 minutes - 1 hour
          dj.src = 'djimages/strawbs.jpg';
          tx.innerHTML = 'Thursday at 9 PM<br>Request<br>With Strawbs';
		  
		  if ((hr === 16) { // 16:00 - 16:59 - 60 minutes - 1 hour
		  dj.src = 'djimages/djbunny.jpg';
          tx.innerHTML = 'Monday at 1 PM<br>Auto<br>With Bunny';
		  
        }
        break;
      case 5: // Friday
                if (hr === 13 || hr === 14) // 13:00 - 14:59 - 2 hours
          dj.src = 'djimages/djbunny.jpg';
          tx.innerHTML = 'Saturday at 8 AM<br>Christian Music<br>with Dj Bunny';
        break;
      case 6: // Saturday
        if (hr === 2 && mn >=03) { // 2:03 - 2:59 - 57 minutes
          dj.src = 'djimages/djbunny.jpg';
          tx.innerHTML = 'Saturday at 8 AM<br>Christian Music<br>with Dj Bunny';
        }
        break;
      case 0: // Sunday
        if (hr === 21) { // 21:00 - 21:59 - 60 minutes - 1 hour
          dj.src = 'djimages/strawbs.jpg';
          tx.innerHTML = 'Sunday at 9 PM<br>Request<br>with Strawbs';
        }
        break;
      default:
        dj.src = 'djimages/strawbs.jpg';
        tx.innerHTML = 'Thursday at 9 PM<br>Request<br>With Strawbs';
        break;
    }
  }

  setInterval(function() { onair(); }, 1000);
})(document);
