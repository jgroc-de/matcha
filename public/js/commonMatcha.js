'use strict'

let tokenId = document.getElementById('myId')
if (tokenId) {
    var myId = tokenId.dataset.token
}

function display(event) {
    let h2s = document.querySelectorAll('h2[matcha-title]')

    for (let h2 of h2s) {
        h2.classList.replace("w3-theme-l1", "w3-theme-d1")
        if (h2 === event.currentTarget) {
            h2.parentNode.nextElementSibling.classList.add('w3-show')
            h2.classList.replace("w3-theme-d1", "w3-theme-l1")
        } else {
            h2.parentNode.nextElementSibling.classList.remove('w3-show')
        }
    }
}

function displayModal(event) {
    let modal = document.getElementById('modal')

    modal.getElementsByTagName('img')[0].src = event.currentTarget.src
    modal.style.display='block'
}

function toggleSibling(event) {
    event.currentTarget.nextElementSibling.classList.toggle('w3-hide')
}

function toggleShowSibling(event) {
    event.currentTarget.nextElementSibling.classList.toggle('w3-show')
}

async function postData(url = '', data = {}) {
    // Default options are marked with *
    let response = await fetch(url, {
        method: 'POST', // *GET, POST, PUT, DELETE, etc.
        mode: 'same-origin', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        body: data // body data type must match "Content-Type" header
    });

    return response.json();
}

function getUrl(event) {
    postData(event.currentTarget.dataset.url)
        .then(data => {
            if (data.success) {
                printNotif([data.success, true])
            } else {
                printNotif([data.failure['1'], false])
            }
        });
}

function submitForm(event) {
    event.preventDefault()
    var action = event.currentTarget.action
    var baseURI = event.currentTarget.baseURI
    postData(action, new FormData(event.currentTarget))
        .then(data => {
            data.success ? printNotif([data.success, true]) : printNotif([data.failure['1'], false])
            switch (action) {
                case baseURI + 'tag':
                    addTag(data)
                    break
            }
    });
}

function printNotif(args) {
    let div = getTemplate("repeatNotif")
    let notif = document.getElementById('notif')

    div.querySelector('p').textContent = args[0]
    div.classList.add(args[1] ? 'w3-green':'w3-red')
    notif.appendChild(div)

    setTimeout(function() {
        notif.removeChild(div)
    }, 4500, notif, div)
}

function ggAjaxGet(path, callback, args) {
    let request = new XMLHttpRequest()

    request.open('GET', path, true)
    request.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            if (args[0] === 'response')
                args[0] = this.responseText
            callback(args)
        }
    }
    request.send()
}

function ggAjax(method, path, callback, args) {
    let request = new XMLHttpRequest()

    request.open(method, path, true)
    request.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            if (args[0] === 'response')
                args[0] = this.responseText
            callback(args)
        }
    }
    request.send()
}

function getColor(kind) {
    switch(kind) {
        case 'Rick':
            return '878f99'
        case 'Jerry':
            return 'ff7b25'
        case 'Beth':
            return '6b5b95'
        case 'Morty':
            return 'feb236'
        case 'Summer':
            return 'd64161'
        default:
            return '000'
    }
}

function revokeAllScopes(event) {
    gapi.auth2.getAuthInstance().signOut();
    window.location.href = event.currentTarget.dataset.url
}

function getTemplate(id) {
    let template = document.getElementById(id)
    return document.importNode(template.content, true).firstElementChild
}

function setCommonEvents() {
    let titles = document.querySelectorAll('[matcha-toggle]')
    for (let title of titles) {
        title.addEventListener('click', toggleSibling, true)
    }
    let items = document.querySelectorAll('[matcha-show]')
    for (let item of items) {
        item.addEventListener('click', toggleShowSibling, true)
    }
    let logout = document.getElementById('logout')
    if (logout) {
        logout.addEventListener('click', revokeAllScopes, true)
    }
    let forms = document.querySelectorAll('form[matcha-form]')
    for (let form of forms) {
        form.addEventListener('submit', submitForm, true)
    }
    let actionButtons = document.querySelectorAll('button[data-url]')
    for (let action of actionButtons) {
        action.addEventListener('click', getUrl, true)
    }
    let images = document.querySelectorAll('img[matcha-modal]')
    for (let image of images) {
        image.addEventListener('click', displayModal, true)
    }
    let h2s = document.querySelectorAll('h2[matcha-title]')
    for (let h2 of h2s) {
        h2.addEventListener('click', display, true)
    }

    let modal = document.getElementById('modal')
    if (modal) {
        modal.addEventListener('click', function(event) {
            event.currentTarget.style.display='none'
        }, true)
    }
}

setCommonEvents()
