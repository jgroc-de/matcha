function sortCard(id2sort, id) {
    console.log('sortcard')
    var key = document.getElementById(id2sort).value
    var father = document.getElementById(id)

    generateCard(id, key)
}

function generateCard(idFeed, key) {
    console.log('generateCard')
    console.log(idFeed)
    var show = 1
    var main = document.getElementById(idFeed)
    main.textContent = ''

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
        document.getElementById('add').setAttribute('onclick', "addFriend(" + hash.id + ")")
        document.getElementById('next').setAttribute('onclick', "next(" + hash.id + ")")
    }
    let link = clone.querySelector('a')
    link.href = link.href + hash.id
    link.id = hash.id

    let img = clone.querySelector('img')
    img.name = hash.img
    if (show)
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

    if (nextNode.parentElement.nextElementSibling) {
        view(id1, nextNode.parentElement.nextElementSibling.children[0].id)
    }
}

function prev(id1) {
    var prevNode = document.getElementById(id1)

    if (prevNode.parentElement.previousElementSibling) {
        view(id1, prevNode.parentElement.previousElementSibling.children[0].id)
    }
}

function setImg(node) {
    console.log(node)
    if (node.firstChild) {
        var img = node.getElementsByTagName('img')[0]

        if (img.name != "") {
            img.setAttribute('src', img.name)
            img.setAttribute('alt', 'profil\'s image')
            img.name = ""
            console.log(img)
        }
    }
}

function setPrevNext(node) {
    setImg(node)
    if (node.nextElementSibling)
        setImg(node.nextElementSibling)

    if (node.previousElementSibling)
        setImg(node.previousElementSibling)

}

function view(id1, id) {
    var divSelected = document.getElementById(id)
    console.log(id);
    setPrevNext(document.getElementById(id).parentElement)

    document.getElementById(id).parentElement.classList.remove('w3-hide')
    document.getElementById(id1).parentElement.classList.add('w3-hide')

    divSelected.setAttribute('name', 'visible')
    document.getElementById(id1).setAttribute('name', '')
    document.getElementById('prev').setAttribute('onclick', "prev(" + id + ")")
    document.getElementById('add').setAttribute('onclick', "addFriend(" + id + ")")
    document.getElementById('next').setAttribute('onclick', "next(" + id + ")")
}

function mapView(id) {
    var hide = document.getElementsByName('visible')[0]

    view(hide.id, id)
}

generateCard('focus')
