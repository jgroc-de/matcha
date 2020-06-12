'use strict'

function generateCard(event) {
    if (event) {
        let key = event.target.value
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
    reloadProfilCards()
}

function reloadProfilCards() {
    let show = 1
    let main = document.getElementById('focus')
    main.textContent = ''
    for (let user of usersPos) {
        main.appendChild(addChildrenCard(user, show))
        show = 0
    }
}

function tagSort(event) {
    let tags = [];
    let childrens = event.target.parentElement.children
    let i = 0
    while (i < childrens.length) {
        if (childrens[i].checked) {
            tags.push(parseInt(childrens[i].name))
        }
        i++;
    }
    usersPos.sort(function(a, b) {
        let bRes = 0
        let aRes = 0
        let i = 0
        while (i < tags.length) {
            if (b['tag'].includes(tags[i])) {
                bRes++
            }
            if (a['tag'].includes(tags[i])) {
                aRes++
            }
            i++
        }
        return bRes - aRes
    })
    reloadProfilCards()
}

function addChildrenCard(hash, show) {
    let clone = getTemplate("repeatProfil")

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
    img.parentElement.style.backgroundColor = '#' + getColor(hash.gender)

    if (show)
        img.src = hash.img

    let name = clone.querySelector('span.matcha-name')
    name.innerText = hash.pseudo + ', ' + hash.age
    let score = clone.querySelector('div.matcha-pop-score')
    score.innerText = hash.popularity + ' %'
    score.style.backgroundColor = '#' + getColor(hash.gender)
    let description = clone.querySelector('span[matcha-bio]')
    description.innerText = hash.biography
    if (hash.tag && hash.tag.length) {
        let tags = clone.querySelector('span[matcha-tags]')
        for(let tag in hash.tag) {
            tags.innerText += '#' + myTags[hash.tag[tag]] + ' '
        }
    }

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
    if (node.firstChild) {
        let img = node.getElementsByTagName('img')[0]

        if (img.dataset.src != "") {
            img.setAttribute('src', img.dataset.src)
            img.setAttribute('alt', 'profil\'s image')
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

function view(oldId, id) {
    let divSelected = document.getElementById(id)
    let divOld = document.getElementById(oldId)

    setPrevNext(divSelected.parentElement)
    divOld.parentElement.classList.add('w3-hide')
    divSelected.parentElement.classList.remove('w3-hide')
    document.getElementById('prev').setAttribute('onclick', "prev(" + id + ")")
    document.getElementById('add').dataset.url = "/friend/" + id
    document.getElementById('next').setAttribute('onclick', "next(" + id + ")")
}

function mapView(id) {
    view(document.querySelector('#focus>div:not(.w3-hide)').children[0].id, id)
}

function searchForm(event) {
    event.preventDefault()
    postData(event.currentTarget.action, new FormData(event.currentTarget))
        .then(data => {
            if (data.failure) {
                printNotif([data.failure, false])
            } else {
                usersPos = data
                reloadProfilCards()
                initMap()
            }
        });
}

function setSearchEvents() {
    let tags = document.getElementById('myTags')
    let select = document.getElementById('sort1')
    let nameForm = document.getElementById('searchByName')
    let critForm = document.getElementById('searchByCriteria')

    select.addEventListener('change', generateCard)
    if (tags) {
        tags.addEventListener('change', tagSort, true)
    }
    nameForm.addEventListener('submit', searchForm)
    critForm.addEventListener('submit', searchForm)
}

reloadProfilCards()
setSearchEvents()
