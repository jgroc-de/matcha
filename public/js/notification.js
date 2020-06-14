'use strict'

function setNotifEventListeners() {
	var notifButton = document.querySelector('i[matcha-notif]')
	var notifPanel = document.getElementById('notification')
	var badge = notifButton.nextElementSibling

	notifButton.addEventListener('click', function() {
		notifPanel.classList.toggle('w3-hide')
		badge.classList.add("w3-hide")
		badge.innerHTML = ""
	}, true)
}

function mateStatus() {
	ggAjaxGet('/chatStatus', function(){}, 0)
}

function socket() {
	var notifPanel = document.getElementById("notification")
	var badge = document.getElementById("badge")
	var notifSocket = new ab.Session('ws://localhost:3001',
		function () {
			notifSocket.subscribe('"' + myId + '"', function (topic, data) {
				//console.log('New notification received')
				if (data.hasOwnProperty('msg')) {
					let link = getTemplate("repeatSideNotif")

					if (badge) {
						badge.classList.remove("w3-hide")
						badge.innerHTML = Number(badge.innerHTML) + 1
					}
					link.innerHTML = data.msg
					link.href = data.link
					notifPanel.insertBefore(link, notifPanel.firstElementChild)
				} else if (data.hasOwnProperty('mateStatus')) {
					if (typeof mateStatus === "function")
						highlightMate(data)
				} else if (data.hasOwnProperty('profilStatus')) {
					if (typeof profilStatus === "function")
						onlineProfil(data)
				}
			})
			//console.warn('WebSocket connection opened')
			if (typeof mateStatus === "function") {
				mateStatus()
			} else if (typeof profilStatus === "function") {
				profilStatus()
			}
		},
		function () {//console.warn('WebSocket connection closed')
		},
		{'skipSubprotocolCheck': true}
	)
}

setNotifEventListeners()
socket()