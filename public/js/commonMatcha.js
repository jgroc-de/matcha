function displayModal(url) {
    var modal = document.getElementById('Modal')
    
    modal.getElementsByTagName('img')[0].src = url
    modal.style.display='block'
}

function display(id) {
    var forms = document.forms
    var h2 = document.querySelectorAll('h2[matcha-title]')

    for (var i = 0; i < forms.length; i++) {
        h2[i].classList.replace("w3-theme-l1", "w3-theme-d1")
        if (forms[i].id === id) {
            forms[i].classList.add('w3-show')
            h2[i].classList.replace("w3-theme-d1", "w3-theme-l1")
        } else {
            forms[i].classList.remove('w3-show')
        }
    }
}

function toggleDisplay2(x) {
    while (x = x.nextElementSibling) {
        if ((x.className) || (x.innerHTML))
        {
            x.classList.toggle('w3-hide')
        }
    }
}

function toggleSibling(node) {
    node.nextElementSibling.classList.toggle('w3-hide')
}

function toggleById(id) {
    document.getElementById(id).classList.toggle('w3-hide')
}

async function postData(url = '', data = {}) {
    console.log(data)
    // Default options are marked with *
    const response = await fetch(url, {
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
    postData(event.currentTarget.action, new FormData(event.currentTarget))
        .then(data => {
            if (data.success) {
                printNotif([data.success, true])
            } else {
                printNotif([data.failure['1'], false])
            }
    });
}

(function() {
    let forms = document.querySelectorAll('form[matcha-form]')

    forms.forEach(function(form) {
        form.addEventListener('submit', submitForm, true)
    })
})()

function xhrButtons() {
    let buttons = document.querySelectorAll('button[data-url]')

    buttons.forEach(function(button) {
        button.addEventListener('click', getUrl)
    })
}

xhrButtons()

function printNotif(args) {
    let template = document.querySelector("#repeatNotif")
    let div = document.importNode(template.content, true).firstElementChild
    let notif = document.getElementById('notif')

    div.querySelector('p').textContent = args[0]
    if (args[1]) {
        div.classList.add('w3-green')
    } else {
        div.classList.add('w3-red')
    }
    notif.appendChild(div)

    setTimeout(function() {
        notif.removeChild(div)
    }, 4500, notif, div)
}

function ggAjaxGet(path, callback, args) {
    var request = new XMLHttpRequest()

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
    var request = new XMLHttpRequest()

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

function revokeAllScopes(url) {
    gapi.auth2.getAuthInstance().signOut();
    window.location.href = url
}
