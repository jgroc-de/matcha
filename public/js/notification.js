'use strict'

function addNotification(data) {
    let template = document.getElementById("repeatSideNotif")
    let link = document.importNode(template.content, true).firstElementChild
    let notif = document.getElementById("notification")
    let badge = document.getElementById("badge")

    badge.classList.remove("w3-hide")
    badge.innerHTML = Number(badge.innerHTML) + 1
    link.innerHTML = data.msg
    link.setAttribute('href', data.link)
    notif.insertBefore(link, notif.firstElementChild)
}

function resetBadge(badge) {
    badge.classList.add("w3-hide")
    badge.innerHTML = ""
}

(function() {
    var notifSocket = new ab.Session('ws://localhost:3001',
        function () {
            notifSocket.subscribe('"' + myId + '"', function (topic, data) {
                //console.log('New notification received')
                if (data.hasOwnProperty('msg')) {
                    addNotification(data)
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
})()
