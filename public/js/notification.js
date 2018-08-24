function addNotification(data)
{
    var link = document.createElement("a");
    var notif = document.getElementById("notification");
    var badge = document.getElementById("badge");

    badge.className = badge.className.replace("w3-hide", "");
    badge.innerHTML = Number(badge.innerHTML) + 1;
    link.innerHTML = data.msg;
    link.className = "w3-bar-item w3-button w3-theme-l4 w3-border w3-leftbar w3-hover-border-green";
    link.setAttribute('href', data.link);
    notif.insertBefore(link, notif.firstChild);
}

function resetBadge()
{
    var badge = document.getElementById("badge");

    badge.className += "w3-hide";
    badge.innerHTML = "";
}

function highlightMate(data)
{
    var div;
    var name;

    for (name in data.mateStatus)
    {
        div = document.getElementById(name);
        div.className += " gg-card-4";
    }
}

function mateStatus() {
    var xhr = new XMLHttpRequest();

    xhr.open('GET', '/chatStatus');
    xhr.send();
}

(function ()
{
    var notifSocket;

    notifSocket = new ab.Session('ws://localhost:8100',
        function()
        {
            notifSocket.subscribe('"' + myId + '"', function(topic, data)
            {
                console.log('New notification received');
                if (data.hasOwnProperty('msg'))
                {
                    addNotification(data);
                }
                else if (data.hasOwnProperty('mateStatus'))
                {
                    highlightMate(data);
                }
            });
            console.warn('WebSocket connection opened');
            mateStatus();
        },
        function() {console.warn('WebSocket connection closed');},
        {'skipSubprotocolCheck': true}
    );
})();
