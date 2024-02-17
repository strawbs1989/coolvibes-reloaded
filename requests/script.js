const form = document.querySelector('form');

form.addEventListener('submit', (e) => {
	e.preventDefault();
	
	const captchaResponse = grecaptcha.getResponse();
	
	if (caprchaResponse.length > 0) {
		throw new Error("Caprcha not complete");
		
	}
	
	const fd = new FormData(e.target);
	const params = new URLSearchParams(fd);
	
	fetch('httpbin.org/post', {
		method: "POST",
		body: params,
	})
	.then(res => res.json())
	
	
});