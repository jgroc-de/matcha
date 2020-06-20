'use strict'

var slideIndex = 1
showDivs(slideIndex)

function plusDivs(event) {
    let n = event.currentTarget.innerText === '>' ? 1 : -1;
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

function profilAction(event) {
    ggAjax('DELETE', event.currentTarget.dataset.path, printNotif, ['response', true])
}

function onlineProfil(data) {
    let span = document.getElementById('online')
    let spanoff = document.getElementById('offline')

    if ((data['profilStatus'] && span.classList.contains("w3-hide")) || (!data['profilStatus'] && spanoff.classList.contains("w3-hide"))) {
        spanoff.classList.toggle("w3-hide")
        span.classList.toggle("w3-hide")
    }
}

function mateStatus() {
    ggAjaxGet('/profilStatus/' + user.id, function(){}, 0)
}

function setOtherProfilEventListeners() {
    let slidesButtons = document.querySelectorAll('button[matcha-slide]')

    for (let slideButton of slidesButtons) {
        slideButton.addEventListener('click', plusDivs, true)
    }

    let delButton = document.getElementById('delAction')
    if (delButton) {
        delButton.addEventListener('click',profilAction, true)
    }
}

setOtherProfilEventListeners()
setInterval(mateStatus, 10000)
