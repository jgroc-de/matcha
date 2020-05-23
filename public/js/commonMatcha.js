function displayModal(url) {
    var modal = document.getElementById('Modal')
    
    modal.getElementsByTagName('img')[0].src = url
    modal.style.display='block'
}

function display(id) {
    var forms = document.forms
    var h2 = document.querySelectorAll('h2[gg-form]')

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
    while (x = x.nextSibling) {
        if ((x.className) || (x.innerHTML))
        {
            x.classList.toggle('w3-hide')
        }
    }
}

function toggleSibling(node) {
    node.nextSibling.classList.toggle('w3-hide')
}

function toggleById(id) {
    document.getElementById(id).classList.toggle('w3-hide')
}

function printNotif(args) {
    var p = document.createElement('p')
    var div = document.createElement('div')
    var notif = document.getElementById('notif')

    p.textContent = args[0]
    div.appendChild(p)
        div.className = "w3-panel w3-round"
    if (args[1])
        div.classList.add('w3-green')
    else
        div.classList.add('w3-red')
    div.style.margin = "0"
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
    switch(kind)
    {
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
