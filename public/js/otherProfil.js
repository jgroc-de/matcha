'use strict'

var slideIndex = 1
showDivs(slideIndex)

function plusDivs(n) {
    showDivs(slideIndex += n)
}

function showDivs(n) {
    let slides = document.getElementsByClassName("mySlides")

    if (slides.length > 0) {
        if (n > slides.length) {
            slideIndex = 1
        } else if (n < 1) {
            slideIndex = slides.length
        }
        for (let slide of slides)
            slide.style.display = "none"
        slides[slideIndex - 1].style.display = "block"
    }
}

function profilAction(path, del = false) {
    ggAjax(del ? 'DELETE':'POST', path, printNotif, ['response', true])
}

function onlineProfil(data) {
    let span = document.getElementById('online')
    let spanoff = document.getElementById('offline')

    if ((data['profilStatus'] && span.classList.contains("w3-hide")) || (!data['profilStatus'] && spanoff.classList.contains("w3-hide"))) {
        spanoff.classList.toggle("w3-hide")
        span.classList.toggle("w3-hide")
    }
}

function profilStatus() {
    ggAjaxGet('/profilStatus/' + user.id, function(){}, 0)
}

setInterval(profilStatus, 10000)
