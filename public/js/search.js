function sortCard(id2sort, id) {
    console.log('sortcard')
    let key = document.getElementById(id2sort).value

    generateCard(id, key)
}

function generateCard(idFeed, key) {
    console.log('generateCard')
    console.log(idFeed)
    let show = 1
    let main = document.getElementById(idFeed)
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
    document.getElementById('add').addEventListener('click', getUrl)
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
        document.getElementById('add').dataset.url = "/friend/" + hash.id
        document.getElementById('next').setAttribute('onclick', "next(" + hash.id + ")")
    }
    let link = clone.querySelector('a')
    link.href = link.href + hash.id
    link.id = hash.id

    let img = clone.querySelector('img')
    img.dataset.src = hash.img
    img.parentElement.style.backgroundColor = '#' + getColor(hash.kind)

    if (show)
        img.src = hash.img

    let name = clone.querySelector('span.matcha-name')
    name.innerText = hash.title + ', ' + hash.age
    let score = clone.querySelector('div.matcha-pop-score')
    score.innerText = hash.popularity + ' %'
    score.style.backgroundColor = '#' + getColor(hash.kind)
    let description = clone.querySelector('span[matcha-bio]')
    description.innerText = hash.biography

    return clone
}

function uncheckTags() {
    let dad = document.getElementById('tags')
    let tags = dad.childNodes

    for (let tag of tags) {
        if (tag.nodeType === 1) {
            tag.firstElementChild.removeAttribute("checked")
        }
    }
}

function checkTags() {
    let dad = document.getElementById('tags')
    let tags = dad.childNodes

    for (let tag of tags) {
        if (tag.nodeType === 1) {
            tag.firstElementChild.setAttribute("checked", "")
        }
    }
}

function next(id1) {
    let nextNode = document.getElementById(id1)

    if (nextNode.parentElement.nextElementSibling) {
        view(id1, nextNode.parentElement.nextElementSibling.children[0].id)
    }
}

function prev(id1) {
    let prevNode = document.getElementById(id1)

    if (prevNode.parentElement.previousElementSibling) {
        view(id1, prevNode.parentElement.previousElementSibling.children[0].id)
    }
}

function setImg(node) {
    console.log(node)
    if (node.firstChild) {
        let img = node.getElementsByTagName('img')[0]

        if (img.dataset.src != "") {
            img.setAttribute('src', img.dataset.src)
            img.setAttribute('alt', 'profil\'s image')
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

function view(node, id) {
    let divSelected = document.getElementById(id)

    setPrevNext(divSelected.parentElement)
    node.classList.add('w3-hide')
    divSelected.parentElement.classList.remove('w3-hide')
    document.getElementById('prev').setAttribute('onclick', "prev(" + id + ")")
    document.getElementById('add').dataset.url = "/friend/" + id
    document.getElementById('next').setAttribute('onclick', "next(" + id + ")")
}

function mapView(id) {
    view(document.querySelector('#focus>div:not(.w3-hide)'), id)
}

generateCard('focus')
