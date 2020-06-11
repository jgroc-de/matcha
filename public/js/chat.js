'use strict'

var TWindow = document.getElementById('tchatWindow')
var title = document.getElementById('tchatTitle')
var messages = document.getElementById('tchatMessages')
var msgBox = document.getElementById('tchatMsg')
var button = document.getElementById('tchatButton')
var websocket
var shiftDown = 0

msgBox.addEventListener("keydown", function(event) {
    if (event.code === "ShiftLeft" || event.code === "ShiftRight")
        shiftDown = 1
})

msgBox.addEventListener("keyup", function(event) {
    if (event.code === "ShiftLeft" || event.code === "ShiftRight")
        shiftDown = 0

    if (event.code === "Enter" && shiftDown === 1) {
        event.preventDefault()
        button.click()
    }
})


function highlightMate(data) {
    let div, name

    for (name in data.mateStatus) {
        div = document.getElementById(name).children[0].children[0]
        if (data.mateStatus[name])
            div.setAttribute('style', 'background-color:' + div.classList[0] + ';')
        else
            div.setAttribute('style', 'background-color:' + div.classList[0] + '70;')
    }
}

function mateStatus() {
    ggAjaxGet('/chatStatus', function(){}, 0)
}

function addMessage(text, owner, myId) {
    let template = document.getElementById("repeatChat")
    let div = document.importNode(template.content, true).firstElementChild
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

function tchatWith(name, id, myId, token) {
    let xhr = new XMLHttpRequest()

    if (websocket)
        websocket.close()
    messages.innerHTML = ""
    title.innerHTML = "flame " + name
    button.setAttribute("onclick", "sendMessageTo(" + myId + ", '" + name + "'," + id + ", '" + token + "')")
    xhr.open('GET', '/startChat/' + id, true)
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let history = JSON.parse(this.response)
            websocket = new ab.Session('ws://localhost:3001',
                function() {
                    websocket.subscribe(token, function(topic, data) {
                        //console.log('topic: "' + topic)
                        //console.log('New msg received from' + data.exp + ' to ' + data.dest + " . my id: " + data.myId)
                        addMessage(data.msg, data.exp, myId)
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
                addMessage(value.message, value.owner, myId)
            })
            msgBox.scrollIntoView()
            msgBox.focus()
        }
    }
    xhr.send()
}

function sendMessageTo(myId, name, id, token) {
    let text = msgBox.value

    if (text && text !== "") {
        let xhr = new XMLHttpRequest()

        xhr.open('POST', '/sendMessage', true)
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
        xhr.send('myId=' + myId + ',&msg=' + text + '&id=' + id + '&token=' + token)
    }
    msgBox.value = ""
    msgBox.focus()
}

setInterval(mateStatus, 60000)
