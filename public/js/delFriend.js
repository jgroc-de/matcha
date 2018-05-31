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
