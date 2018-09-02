var slideIndex = 1;
showDivs(slideIndex);

function plusDivs(n) {
    showDivs(slideIndex += n);
}

function showDivs(n) {
    var i;
    var x = document.getElementsByClassName("mySlides");

    if (x.length > 0)
    {
        if (n > x.length)
        {
            slideIndex = 1
        };
        if (n < 1)
        {
            slideIndex = x.length
        };
        for (i = 0; i < x.length; i++)
        {
            x[i].style.display = "none";
        }
        x[slideIndex - 1].style.display = "block";
    }
}

function profilAction(path)
{
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
        {
            var txt = document.getElementById("flashText");
            
            txt.textContent = this.responseText;
            toggleDisplay("flash");
            setTimeout(function() {
                toggleDisplay("flash"); 
            }, 3500);
        }
    };
    xmlhttp.open("GET", path, true);
    xmlhttp.send();
}

var time;
function displayResponse(text)
{
    var txt = document.getElementById("flashText");

    clearTimeout(time);
    txt.textContent = text;
    if (flash.className.indexOf("w3-hide") == -1)
        toggleDisplay("flash");
    time = setTimeout(function() {
        toggleDisplay("flash"); 
    }, 3500);
}

function onlineProfil()
{
    var span = document.getElementById('online');

    span.innerHTML = "(online)</br>";
    span.className = "w3-text-green";
}

function profilStatus()
{
    var xhr = new XMLHttpRequest();

    xhr.open('GET', '/profilStatus/' + user.id);
    xhr.send();
}
