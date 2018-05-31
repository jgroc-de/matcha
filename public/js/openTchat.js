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
