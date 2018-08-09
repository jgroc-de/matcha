var TWindow = document.getElementById('tchatWindow');
var title = document.getElementById('tchatTitle');
var messages = document.getElementById('tchatMessages');
var msg = document.getElementById('tchatMsg');
var button = document.getElementById('tchatButton');
var sessionId;

function addMessage(text, id, owner)
{
    var div = document.createElement("div");
    var p = document.createElement("span");

    div.className = "w3-bar";
    p.innerHTML = text;
    if (owner == sessionId)
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

function tchatWith(name, id, myId)
{
    var xhr = new XMLHttpRequest();

    sessionId = myId;
    messages.innerHTML = "";
    title.innerHTML = "flame " + name;
    button.setAttribute("onclick", "sendMessageTo(" + myId + ", '" + name + "'," + id + ")");
    xhr.open('POST', '/startTchat', true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var history = JSON.parse(this.response);
            var conn = new ab.Session('ws://localhost:8100',
                    function() {
                        conn.subscribe('msg', function(topic, data) {
                            // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
                            console.log('New msg recieved: "' + data.exp + '" to ' + data.dest + " . me: " + data.myId);
                            addMessage(data.msg, data.dest, data.exp)
                        });
                        console.warn('WebSocket connection opened');
                    },
                    function() {
                        console.warn('WebSocket connection closed');
                    },
                    {'skipSubprotocolCheck': true}
                    );

            if (TWindow.className.indexOf('w3-hide') != -1)
            {
                TWindow.className = TWindow.className.replace('w3-hide', '');
            }
            history.forEach(function(value, index, array)
                    {
                        addMessage(value.message, value.owner, id)
                    });
            msg.scrollIntoView();
            msg.focus();
        }
    }
    xhr.send('myid=' +myId + '&id=' + id);
}

function sendMessageTo(myId, name, id)
{
    var text = msg.value;

    if (text)
    {
        var xhr = new XMLHttpRequest();

        xhr.open('POST', '/sendMessage', true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            /*if (this.readyState == 4 && this.status == 200) {
            }*/
        }
        xhr.send('myId=' + myId + ',&msg=' + text + '&id=' + id);
    }
    msg.value = "";
    msg.focus();
}
