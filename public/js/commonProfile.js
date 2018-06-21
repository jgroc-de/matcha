function displayModal(url)
{
    var modal = document.getElementById('Modal');
    
    modal.childNodes[1].childNodes[1].childNodes[1].src = url;
    modal.style.display='block';
}

function addFriend(path)
{
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
            document.getElementById("flash").textContent = this.responseText;
        else
            document.getElementById("flash").textContent = 'burp';
    };
    xmlhttp.open("GET", path, true);
    xmlhttp.send();
}

function delFriendReq(path, id)
{
    if (confirm('R U SURE?'))
    {
        var request = new XMLHttpRequest();

        request.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200)
            {
                var child = document.getElementById('req' + id);
                child.parentNode.removeChild(child);
            }
        };
        request.open("GET", path, true);
        request.send();
    }
}

function delFriend(path, id)
{
    if (confirm('R U SURE?'))
    {
        var request = new XMLHttpRequest();

        request.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200)
            {
                var child = document.getElementById('friend' + id);
                child.parentNode.removeChild(child);
            }
        };
        request.open("GET", path, true);
        request.send();
    }
}

function openTchat(path)
{
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
            document.getElementById("flash").textContent = this.responseText;
        else
            document.getElementById("flash").textContent = 'burp';
    };
    xmlhttp.open("GET", path, true);
    xmlhttp.send();
}
