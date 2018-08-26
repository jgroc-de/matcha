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

function addFriend(path)
{
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
            document.getElementById("flash").textContent = this.responseText;
    };
    xmlhttp.open("GET", path, true);
    xmlhttp.send();
}

