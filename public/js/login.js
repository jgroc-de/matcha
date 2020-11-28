'use strict'

function onSignIn(googleUser) {
	var id_token = googleUser.getAuthResponse().id_token
	var xhr = new XMLHttpRequest();

	xhr.open('POST', '/apiLogin/google')
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
	xhr.onreadystatechange = function() {
		if (this.readyState === 4 && this.status === 200) {
			window.location.replace(xhr.responseText);
		}
	}
	xhr.send('idtoken=' + id_token)
}
