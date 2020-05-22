var slideIndex = 1
showDivs(slideIndex)

function plusDivs(n) {
    showDivs(slideIndex += n)
}

function showDivs(n) {
    var x = document.getElementsByClassName("mySlides")

    if (x.length > 0)
    {
        if (n > x.length)
            slideIndex = 1
        if (n < 1)
            slideIndex = x.length
        for (var i = 0; i < x.length; i++)
            x[i].style.display = "none"
        x[slideIndex - 1].style.display = "block"
    }
}

function profilAction(path)
{
    ggAjax('POST', path, printNotif, ['response', true])
}

function onlineProfil(data)
{
    var span = document.getElementById('online')
    var spanoff = document.getElementById('offline')

    if ((data['profilStatus']) && (span.classList.contains("w3-hide")))
    {
        spanoff.classList.toggle("w3-hide")
        span.classList.toggle("w3-hide")
    }
    else if (!(data['profilStatus']) && (spanoff.classList.contains("w3-hide")))
    {
        spanoff.classList.toggle("w3-hide")
        span.classList.toggle("w3-hide")
    }
}

function profilStatus()
{
    ggAjaxGet('/profilStatus/' + user.id, function(){}, 0)
}

setInterval(profilStatus, 30000) 
