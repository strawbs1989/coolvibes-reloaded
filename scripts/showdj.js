(function(d) {
    function onair() {
        const dj = d.querySelector('#dj'),
            nd = new Date(),
            dy = nd.getDay(),
            hr = nd.getHours(),
            mn = nd.getMinutes();
        let txt = null;

        switch (dy) {
        case 1: // Monday
            if (hr === 8) // 8:00 - 8:59 - 60 minutes - 1 hour
                dj.src = 'djimages/kayley.jpg';

            if (hr === 22) { // 22:00 - 12:59 - 59 minutes - 2 hour
                dj.src = 'djimages/camino.jpg';
                
            }

            if (hr === 21) // 21:00 - 21:59 - 60 minutes - 1 hour
                dj.src = 'djimages/billy.jpg';
            break;
        case 2: // Tuesday
            if (hr === 8 && (mn >= 0 && mn <= 15)) // 8:00 - 8:15 - 15 minutes
                dj.src = 'djimages/kayley.jpg';

            if (hr === 11) { // 11:00 - 11:59 - 60 minutes - 1 hour
                dj.src = 'djimages/steve.jpg';
                
				
            }

            if (hr === 17) // 17:00 - 17:59 - 60 minutes - 1 hour
                dj.src = 'djimages/djbunny.jpg';
				txt = '<a href="https://coolvibes-reloaded.com/requests" target="_blank">Tuesday at 5 PM<br>Requests With<br>With Bunny</a>';
            break;
        case 3: // Wednesday
            if (hr === 8 && (mn >= 0 && mn <= 15)) // 8:00 - 8:15 - 15 minutes
                dj.src = 'djimages/off-air.jpg';

            if (hr === 15) // 15:00 - 15:59 - 60 minutes - 1 hour
                dj.src = 'djimages/djbunny.jpg';
				txt = '<a href="https://coolvibes-reloaded.com/requests" target="_blank">Wednesday at 3 PM<br>Requests<br>With Bunny</a>';
            break;
        case 4: // Thursday
            if (hr === 21) // 21:00 - 21:59 - 60 minutes - 1 hour
                dj.src = 'djimages/strawbs.jpg';
				txt = '<a href="https://forms.gle/jphWAhp2RGgELphSA" target="_blank">Thursday at 9 PM<br>Requests<br></a>';

            if (hr === 16) { // 16:00 - 16:59 - 60 minutes - 1 hour
                dj.src = 'djimages/djbunny.jpg';
                txt = '<a href="https://coolvibes-reloaded.com/requests" target="_blank">Thurday at 4 PM<br>Requests With<br>With Bunny</a>';
            }
            break;
        case 5: // Friday
            if (hr === 13 || hr === 14) { // 13:00 - 14:59 - 2 hours
                dj.src = 'djimages/djbunny.jpg';
                txt = '<a href="https://coolvibes-reloaded.com/requests" target="_blank">Friday at 1 PM<br>Requests With<br>With Bunny</a>';
            }
            break;
        case 6: // Saturday
            if (hr === 2 || hr === 3) { // 2:00 - 3:59 - 2 hours
                dj.src = 'djimages/djbunny.jpg';
                txt = '<a href="https://coolvibes-reloaded.com/requests" target="_blank">Saturday at 2 AM<br>Requests With<br>With Bunny</a>';
            }
            break;
        case 0: // Sunday
            if (hr === 21) // 21:00 - 21:59 - 60 minutes - 1 hour
                dj.src = 'djimages/strawbs.jpg';
				txt = '<a href="https://forms.gle/jphWAhp2RGgELphSA" target="_blank">Sunday at 9 PM<br>Requests<br></a>';
            break;
        default:
            dj.src = 'djimages/strawbs.jpg';
            break;
        }

        // this code is necessary, do not change --------- 
        if (txt) {
            if (document.querySelector('#who-on-air-txt'))
                document.querySelector('#who-on-air-txt').innerHTML = txt;
            else {
                tx = document.createElement('div');
                tx.style.color = 'white';
                tx.id = 'who-on-air-txt';
                tx.innerHTML = txt;
                dj.insertAdjacentElement('afterend', tx);
            }
        }
        // -----------------------------------------------
    }

    setInterval(function() { onair(); }, 1000);
})(document);