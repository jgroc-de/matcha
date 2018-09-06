var TWindow = document.getElementById('tchatWindow');
var title = document.getElementById('tchatTitle');
var messages = document.getElementById('tchatMessages');
var msg = document.getElementById('tchatMsg');
var button = document.getElementById('tchatButton');
var websocket;

setInterval(mateStatus, 60000);

function highlightMate(data)
{
    var div;
    var name;

    for (name in data.mateStatus)
    {
        div = document.getElementById(name);
        if (data.mateStatus[name])
        {
            if (div.className.indexOf("gg-card-4") == -1)
                div.className += " gg-card-4";
        }
        else if (div.className.indexOf("gg-card-4") != -1)
            div.className = div.className.replace(" gg-card-4","");
    }
}

function mateStatus() {
    var xhr = new XMLHttpRequest();

    xhr.open('GET', '/chatStatus');
    xhr.send();
}

function addMessage(text, owner, myId)
{
    var div = document.createElement("div");
    var p = document.createElement("span");

    div.className = "w3-bar";
    p.innerHTML = text;
    if (owner == myId)
    {
        p.className = "w3-theme-l2 w3-bar-item w3-round w3-right w3-padding";
    }
    else
    {
        p.className = "w3-theme-d1 w3-bar-item w3-round w3-left w3-padding";
    }
    p.style.wordWrap = "break-word";
    p.style.maxWidth = "70%";
    messages.appendChild(div);
    div.appendChild(p);
    p.scrollIntoView();
}

function tchatWith(name, id, myId, token)
{
    var xhr = new XMLHttpRequest();

    if (websocket)
        websocket.close();
    messages.innerHTML = "";
    title.innerHTML = "flame " + name;
    button.setAttribute("onclick", "sendMessageTo(" + myId + ", '" + name + "'," + id + ", '" + token + "')");
    xhr.open('GET', '/startChat/' + id, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
        {
            var history = JSON.parse(this.response);
            websocket = new ab.Session('ws://localhost:8100',
                function()
                {
                    websocket.subscribe(token, function(topic, data)
                    {
                        //console.log('topic: "' + topic);
                        //console.log('New msg received from' + data.exp + ' to ' + data.dest + " . my id: " + data.myId);
                        addMessage(data.msg, data.exp, myId)
                    });
                    //console.log(websocket);
                    //console.warn('WebSocket connection opened');
                },
                function() {//console.warn('WebSocket connection closed');
                },
                {'skipSubprotocolCheck': true}
            );

            if (TWindow.className.indexOf('w3-hide') != -1)
            {
                TWindow.className = TWindow.className.replace('w3-hide', '');
            }
            history.forEach(function(value, index, array)
            {
                addMessage(value.message, value.owner, myId)
            });
            msg.scrollIntoView();
            msg.focus();
        }
    }
    xhr.send();
}

function sendMessageTo(myId, name, id, token)
{
    var text = msg.value;

    if (text)
    {
        var xhr = new XMLHttpRequest();

        xhr.open('POST', '/sendMessage', true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send('myId=' + myId + ',&msg=' + text + '&id=' + id + '&token=' + token);
    }
    msg.value = "";
    msg.focus();
}
