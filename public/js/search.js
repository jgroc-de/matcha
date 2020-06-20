'use strict'

const nextE = document.getElementById('next')
const prevE = document.getElementById('prev')
const addE = document.getElementById('add')
const myTags = JSON.parse(document.getElementById('myTagsJS').dataset.tags)
var usersDefault = usersPos

var opt = {"age" : [], "tag" : [], "pop" : []}


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

function updateUsers() {
    usersPos = []
    user_for: for (let user of usersDefault) {
        for (let n = 0; n < opt['age'].length; n++) {
            let age_min = ((opt['age'][n] === 0) ? 18 : opt['age'][n] * 10 + 15);
            let age_max = opt['age'][n] * 10 + 25;

            if (user.age >= age_min && user.age <= age_max)
                break;
            if (n + 1 === opt['age'].length)
                continue user_for;
        }

        for (let i = 0; i < opt['pop'].length; i++) {
            if (user.popularity >= (opt['pop'][i] * 10) && user.popularity <= (opt['pop'][i] * 10 + 10))
                break;
            if (i + 1 === opt['pop'].length)
                continue user_for;
        }

        tag: for (let m = 0; m < opt['tag'].length; m++) {
            for (let tag of user.tag) {
                console.log(tag.toString())
                console.log(opt['tag'][m])
                if (tag.toString() === opt['tag'][m])
                    break tag;
            }

            if (m + 1 === opt['tag'].length)
                continue user_for;
        }

        usersPos.push(user)
    }
    reloadProfilCards();
    initMap();
}

function filter(event) {
    let f_opt = event.target.options;
    let f_name = event.target.classList[0];

    opt[f_name] = []
    for (let i = 0; i < f_opt.length; i++) {
        if (f_opt[i].selected)
            opt[f_name].push(f_opt[i].value);
    }

    updateUsers();
}

function addChildrenCard(hash, show) {
    let clone = getTemplate("repeatProfil")

    if (show) {
        clone.classList.remove('w3-hide')
        addE.dataset.url = "/friend/" + hash.id
        nextE.dataset.id = hash.id
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

function next(event) {
    let id1 = event.currentTarget.dataset.id
    let nextNode = document.getElementById(id1)

    if (nextNode && nextNode.parentElement.nextElementSibling) {
        view(id1, nextNode.parentElement.nextElementSibling.children[0].id)
    }
}

function prev(event) {
    let id1 = event.currentTarget.dataset.id
    let prevNode = document.getElementById(id1)

    if (prevNode && prevNode.parentElement.previousElementSibling) {
        view(id1, prevNode.parentElement.previousElementSibling.children[0].id)
    }
}

function setImg(node) {
    if (node.firstChild) {
        let img = node.getElementsByTagName('img')[0]

        if (img.dataset.src !== "") {
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

    if (oldId) {
        let divOld = document.getElementById(oldId)
        divOld.parentElement.classList.add('w3-hide')
    }
    setPrevNext(divSelected.parentElement)
    divSelected.parentElement.classList.remove('w3-hide')
    prevE.dataset.id = id
    addE.dataset.url = "/friend/" + id
    nextE.dataset.id = id
}

function mapView(event) {
    let id = event.currentTarget.dataset.id
    let hide = document.querySelector('#focus>div:not(.w3-hide)')

    view(hide ? hide.children[0].id : '', id)
}

function searchForm(event) {
    event.preventDefault()
    postData(event.currentTarget.action, new FormData(event.currentTarget)).then(data => {
        if (data.failure) {
            printNotif([data.failure, false])
        } else {
            usersPos = data
            usersDefault = data
            reloadProfilCards()
            initMap()
        }
    })
}

function setSearchEvents() {
    let select = document.getElementById('sort1')
    select.addEventListener('change', generateCard)

    let filter_age = document.getElementById('filter_age');
    filter_age.addEventListener('change', filter, true);

    let filter_tag = document.getElementById('filter_tag');
    filter_tag.addEventListener('change', filter, true);

    let filter_pop = document.getElementById('filter_pop');
    filter_pop.addEventListener('change', filter, true);


    let nameForm = document.getElementById('searchByName')
    nameForm.addEventListener('submit', searchForm)
    let critForm = document.getElementById('searchByCriteria')
    critForm.addEventListener('submit', searchForm)
    prevE.addEventListener('click', prev, true)
    nextE.addEventListener('click', next, true)
}

reloadProfilCards()
setSearchEvents()
