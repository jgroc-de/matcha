function sortCard(id2sort, id) {
    var key = document.getElementById(id2sort).value
    var father = document.getElementById(id)

    generateCard(id, key)
}

function generateCard(idFeed, key) {
    var show = 1
    var main = document.getElementById(idFeed)

    if (key) {
        if ((key === 'age asc.') || (key === 'distance')) {
            if (key === 'age asc.')
                key = 'age'
            usersPos.sort(function(a, b) {return a[key] - b[key]})
        } else {
            if (key === 'age desc.')
                key = 'age'
            usersPos.sort(function(a, b) {return b[key] - a[key]})
        }
    }
    for (let user of usersPos) {
        main.appendChild(addChildrenCard(user, key, show))
        show = 0
    }
}

function addChildrenCard(hash, key, show) {
    let template = document.querySelector("#repeatProfil")
    let clone = document.importNode(template.content, true)

    if (show) {
        let body = clone.querySelector('div')
        body.classList.remove('w3-hide')
    }
    let link = clone.querySelector('a')
    link.href = link.href + hash.id
    link.id = hash.id

    let img = clone.querySelector('img')
    img.src = hash.img

    let description = clone.querySelector('div[gg-bio]')
    description.innerText = hash.biography
    let name = clone.querySelector('div[gg-name]')
    name.innerText = hash.title

    return clone
}

function uncheckTags() {
    var dad = document.getElementById('tags')
    var tags = dad.childNodes

    for (let tag of tags) {
        if (tag.nodeType === 1) {
            tag.firstElementChild.removeAttribute("checked")
        }
    }
}

function checkTags() {
    var dad = document.getElementById('tags')
    var tags = dad.childNodes

    for (let tag of tags) {
        if (tag.nodeType === 1) {
            tag.firstElementChild.setAttribute("checked", "")
        }
    }
}

function addFriend(id) {
    ggAjax('POST', '/friend/' + id, printNotif, ['response', true])
}

function next(id1) {
    var nextNode = document.getElementById(id1)

    if (nextNode.nextElementSibling) {
        view(id1, nextNode.nextElementSibling.id)
    }
}

function prev(id1) {
    var prevNode = document.getElementById(id1)

    if (prevNode.previousElementSibling.id) {
        view(id1, prevNode.previousElementSibling.id)
    }
}

function setImg(node) {
    if (node.firstChild) {
        var img = node.getElementsByTagName('img')[0]

        if (img) {
            img.setAttribute('src', img.name)
            img.setAttribute('alt', 'profil\'s image')
        }
    }
}

function setPrevNext(node) {
    var prev = node.previousElementSibling
    var prev2 = prev.previousElementSibling
    var next = node.nextElementSibling
    var next2 = next.nextElementSibling

    setImg(node)
    setImg(prev)
    setImg(next)
    setImg(prev2)
    setImg(next2)
}

function view(id1, id) {
    var divSelected = document.getElementById(id)

    setPrevNext(divSelected)
    toggleById(id1)
    toggleById(id)
    divSelected.setAttribute('name', 'visible')
    document.getElementById(id1).setAttribute('name', '')
    document.getElementById('prev').setAttribute('onclick', "prev(" + id + ")")
    document.getElementById('add').setAttribute('onclick', "addFriend(" + id + ")")
    document.getElementById('next').setAttribute('onclick', "next(" + id + ")")
}

function mapView(id) {
    var hide = document.getElementsByName('visible')[0]

    view(hide.id, id)
    document.getElementsByTagName('h3')[0].scrollIntoView()
}

generateCard('focus')
