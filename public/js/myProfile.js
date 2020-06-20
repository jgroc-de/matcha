'use strict'

window.URL = window.URL || window.webkitURL

function addTag(data) {
    let span = getTemplate("repeatTag")

    span.id = 'tag' + data.id
    span.lastElementChild.setAttribute('data-id', data.id)
    span.lastElementChild.addEventListener('click', delUserTag, true)
    span.firstElementChild.textContent = "- #" + data.tag + " "
    document.getElementById('myTag').appendChild(span)
}

function sendPicture(event) {
    var id = event.currentTarget.getAttribute('data-id')
    var prev = this.parentNode.parentNode
    let allowedTypes = ['image/png', 'image/jpeg', 'image/gif']

    if (allowedTypes.indexOf(this.files[0].type) === -1) {
        printNotif(['not allowed type (png, jpg/jpeg, gif)', false])
        return
    } else if (this.files[0].size > 400000) {
        printNotif(['file too large (> 400ko)', false])
        return
    }
    let form = new FormData()
    let xhttp = new XMLHttpRequest()
    var reader = new FileReader()

    form.append('file', this.files[0])
    xhttp.open('POST', '/picture/' + id.charAt(3), true)
    xhttp.setRequestHeader('enctype', 'multipart/form-data')
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let path = this.responseText
            let template = getTemplate("repeatImage")
            let imgElement = template.firstElementChild
            let iElement = template.lastElementChild

            while (prev.hasChildNodes())
                prev.removeChild(prev.firstChild)
            imgElement.title = path
            imgElement.alt = path
            imgElement.addEventListener('click', displayModal, true)
            imgElement.src = reader.result
            prev.appendChild(imgElement)
            iElement.dataset.id = id
            iElement.addEventListener('click', deletePic, true)
            prev.appendChild(iElement)
        }
        else if (this.readyState === 4 && this.status === 500)
            printNotif(['server trouble… try again later!', false])
    }
    xhttp.send(form)
    reader.readAsDataURL(this.files[0])
}

function deletePic(event) {
    var id = event.currentTarget.dataset.id
    ggAjax('DELETE', 'picture/' + id.charAt(3), function (id) {
        let parentNode = document.getElementById(id)
        let div = getTemplate('repeatAddImage')
        let inputElemt = div.querySelector('input')

        if (id === 'img1') {
            div.style.height = '250px'
        }
        inputElemt.setAttribute('data-id', id)
        while (parentNode.hasChildNodes())
            parentNode.removeChild(parentNode.firstChild)
        parentNode.appendChild(div)
        inputElemt.addEventListener('change', sendPicture, true)
    }, id)
}

function ggRemoveChild(id) {
    let child = document.getElementById(id)

    child.parentNode.removeChild(child)
}

function delFriend(event) {
    let id = event.currentTarget.dataset.id
    let url = event.currentTarget.dataset.url + id
    if (confirm('Seriously bro?'))
        ggAjax('DELETE', url, ggRemoveChild, 'friend' + id)
}

function delFriendReq(event) {
    let id = event.currentTarget.dataset.id
    let url = event.currentTarget.dataset.url + id
    ggAjax('DELETE', url, ggRemoveChild, 'req' + id)
}

function acceptFriendReq(event) {
    let id = event.currentTarget.dataset.id
    let url = event.currentTarget.dataset.url + id
    let parent = document.getElementById("Friend")
    let child = getTemplate('repeatFriends')
    let del = child.querySelector('i')
    let acopy = child.querySelector('a')
    let a = document.getElementById('req' + id).firstElementChild

    child.id = "friend" + id
    acopy.href = a.href
    acopy.firstElementChild.src = a.firstElementChild.src
    acopy.firstElementChild.alt = a.firstElementChild.alt
    acopy.firstElementChild.style = a.firstElementChild.style
    acopy.appendChild(a.lastChild)
    del.dataset.url = "/friend/"
    del.dataset.id = id
    del.addEventListener('click', delFriend, true)
    parent.appendChild(child)

    ggAjax('POST', url, ggRemoveChild, 'req' + id)
    printNotif(["Friend request accepted", true])
}

function delUserTag(event) {
    let id = event.currentTarget.dataset.id
    ggAjax('DELETE', '/tag/' + id, ggRemoveChild, 'tag' + id)
}

function highlightMate(data) {
    let div, name, darker

    for (name in data.mateStatus) {
        div = document.getElementById("friend" + name).children[0].children[0]
        darker = data.mateStatus[name] ? '' : '60'
        div.style.borderColor = div.classList[0] + darker
    }
}

function setProfileEventsListener() {
    let delButtons = document.querySelectorAll('span.del')

    for (let delButton of delButtons) {
        delButton.addEventListener('click', delUserTag, true)
    }
    let files = document.querySelectorAll('input[type=file]')
    for (let file of files) {
        file.addEventListener('change', sendPicture, true)
    }
    let dels = document.querySelectorAll('i[matcha-delete]')
    for (let del of dels) {
        del.addEventListener('click', deletePic, true)
    }
    dels = document.querySelectorAll('i[matcha-delfr]')
    for (let del of dels) {
        del.addEventListener('click', delFriendReq, true)
    }
    dels = document.querySelectorAll('i[matcha-delfriend]')
    for (let del of dels) {
        del.addEventListener('click', delFriend, true)
    }
    dels = document.querySelectorAll('i[matcha-addfr]')
    for (let del of dels) {
        del.addEventListener('click', acceptFriendReq, true)
    }
}

function mateStatus() {
    ggAjaxGet('/chatStatus', function(){}, 0)
}

setProfileEventsListener()
setInterval(mateStatus, 60000)
