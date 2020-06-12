'use strict'

var TWindow = document.getElementById('tchatWindow')
var title = document.getElementById('tchatTitle')
var messages = document.getElementById('tchatMessages')
var msgBox = document.getElementById('tchatMsg')
var sendButton = document.getElementById('tchatButton')
var websocket
var shiftDown = 0

function highlightMate(data) {
    let div, name, darker

    for (name in data.mateStatus) {
        div = document.getElementById(name).firstElementChild
        darker = data.mateStatus[name] ? '':'70';
        div.setAttribute('style', 'background-color:#' + div.dataset.color + darker + ';')
    }
}

function mateStatus() {
    ggAjaxGet('/chatStatus', function(){}, 0)
}

function addMessage(text, owner, myId) {
    let div = getTemplate("repeatChat")
    let position, color

    if (owner == myId) {
        position = "w3-right"
        color = "w3-theme-d1"
    } else {
        position = "w3-left"
        color = "w3-theme-l2"
    }
    div.firstElementChild.innerHTML = text.replace(/(?:\r\n|\r|\n)/g, "<br>")
    div.firstElementChild.classList.add(position, color)
    messages.appendChild(div)
    div.scrollIntoView()
}

function tchatWith(event) {
    let xhr = new XMLHttpRequest()
    var dataset = event.currentTarget.dataset

    if (websocket)
        websocket.close()
    messages.innerHTML = ""
    title.innerHTML = "flame " + dataset.pseudo
    sendButton.dataset.targetId = dataset.id
    xhr.open('GET', '/startChat/' + dataset.id, true)
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let history = JSON.parse(this.response)
            websocket = new ab.Session('ws://localhost:3001',
                function() {
                    websocket.subscribe(dataset.token, function(topic, data) {
                        //console.log('topic: "' + topic)
                        //console.log('New msg received from' + data.exp + ' to ' + data.dest + " . my id: " + data.myId)
                        addMessage(data.msg, data.exp, dataset.myId)
                    })
                    //console.log(websocket)
                    //console.warn('WebSocket connection opened')
                },
                function() {//console.warn('WebSocket connection closed')
                },
                {'skipSubprotocolCheck': true}
            )

            TWindow.classList.remove('w3-hide')
            history.forEach(function(value, index, array) {
                addMessage(value.message, value.owner, dataset.myId)
            })
            msgBox.scrollIntoView()
            msgBox.focus()
        }
    }
    xhr.send()
}

function sendMessageTo(event) {
    let text = msgBox.value
    let targetId = event.currentTarget.dataset.targetId
    let dataset = document.getElementById(targetId).dataset


    if (text && text !== "") {
        let xhr = new XMLHttpRequest()

        xhr.open('POST', '/sendMessage', true)
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
        xhr.send('myId=' + dataset.myId + ',&msg=' + text + '&id=' + dataset.id + '&token=' + dataset.token)
    }
    msgBox.value = ""
    msgBox.focus()
}

function setChatEventListener(msgBox) {
    msgBox.addEventListener("keydown", function(event) {
        if (event.code === "ShiftLeft" || event.code === "ShiftRight")
            shiftDown = 1
    }, true)

    msgBox.addEventListener("keyup", function(event) {
        if (event.code === "ShiftLeft" || event.code === "ShiftRight")
            shiftDown = 0

        if (event.code === "Enter" && shiftDown === 1) {
            event.preventDefault()
            sendButton.click()
        }
    }, true)

    let users = document.querySelectorAll('div[matcha-chat]')
    for (let user of users) {
        user.addEventListener('click', tchatWith, true)
    }
    sendButton.addEventListener('click', sendMessageTo, true)
}

setChatEventListener(msgBox)
setInterval(mateStatus, 60000)
