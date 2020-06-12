'use strict'

window.URL = window.URL || window.webkitURL

function addTag(path) {
    var tag = prompt("add a tag (without the '#'):")

    if (!tag) {
        return
    }
    tag = tag.replace(/(?:\s)/g, "")
    let xhr = new XMLHttpRequest()

    xhr.open('POST', path, true)
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    xhr.onreadystatechange = function() {
        if (this.readyState === 4) {
            if (this.status === 200) {
                let id = this.responseText
                let span = getTemplate("repeatTag")

                span.id = 'tag' + id
                span.children[1].setAttribute('onclick', 'delUserTag("/tag/", ' + id + ')')
                span.firstElementChild.textContent = "- #" + tag + " "
                document.getElementById('Interest').appendChild(span)
                printNotif(['added!', true])
            } else {
                printNotif(['already Added?', false])
            }
        }
    }
    xhr.send('tag=' + tag)
}

function addPicture() {
    let files = document.querySelectorAll('input[type=file]')

    for (let file of files) {
        file.addEventListener('change', sendPicture)
    }
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

            while (prev.firstChild)
                prev.removeChild(prev.firstChild)
            imgElement.title = path
            imgElement.alt = path
            imgElement.setAttribute('onclick', 'displayModal(this)')
            imgElement.src = reader.result
            prev.appendChild(imgElement)
            iElement.setAttribute('onclick', 'deletePic("' + id + '")')
            prev.appendChild(iElement)
        }
        else if (this.readyState === 4 && this.status === 500)
            printNotif(['server trouble… try again later!', false])
    }
    xhttp.send(form)
    reader.readAsDataURL(this.files[0])
}

function deletePic(id) {
    ggAjax('DELETE', 'picture/' + id.charAt(3), function (id) {
        var parentNode = document.getElementById(id)
        var labelElmt = document.createElement('label')
        var inputElemt = document.createElement('input')
        var inputElemt2 = document.createElement('input')
        var div = document.createElement('div')

        div.className = 'w3-display-container'
        if (id === 'img1')
            div.style.height = '250px'
        else
            div.style.height = '125px'
        labelElmt.className = 'w3-section w3-jumbo w3-center fa fa-plus check-green w3-block w3-padding-large'
        labelElmt.title = 'add picture'
        inputElemt.setAttribute('data-id', id)
        inputElemt.type = 'file'
        inputElemt2.type = 'hidden'
        inputElemt.style.display = 'none'
        while (parentNode.hasChildNodes())
            parentNode.removeChild(parentNode.firstChild)
        parentNode.appendChild(div)
        div.appendChild(labelElmt)
        labelElmt.appendChild(inputElemt2)
        labelElmt.appendChild(inputElemt)
        addPicture()
    }, id)
}

function ggRemoveChild(id) {
    let child = document.getElementById(id)

    child.parentNode.removeChild(child)
}

function delFriend(path, id) {
    if (confirm('Seriously bro?'))
        ggAjax('DELETE', path + id, ggRemoveChild, 'friend' + id)
}

function delFriendReq(path, id) {
    ggAjax('DELETE', path + id, ggRemoveChild, 'req' + id)
}

function acceptFriendReq(path, id) {
    let parent = document.getElementById("Friend")
    let child = document.createElement('div')
    let del = document.createElement('i')
    let a = document.getElementById('req' + id).firstElementChild

    child.id = "friend" + id
    child.className = "gg-friend"
    del.className = 'fa fa-remove del-red'
    del.title = 'delete'
    del.setAttribute('onclick', 'delFriend("/friend/' + id + '")')

    child.appendChild(a)
    child.appendChild(del)
    parent.appendChild(child)

    ggAjax('POST', path + id, ggRemoveChild, 'req' + id)
    printNotif(["Friend request accepted", true])
}

function delUserTag(path, id) {
    ggAjax('DELETE', path + id, ggRemoveChild, 'tag' + id)
}

function mateStatus() {
    ggAjaxGet('/chatStatus', function(){}, 0)
}

function highlightMate(data) {
    let div, name, darker

    for (name in data.mateStatus) {
        div = document.getElementById("friend" + name).children[0].children[0]
        darker = data.mateStatus[name] ? '' : '60'
        div.style.borderColor = div.classList[0] + darker
    }
}

addPicture()
setInterval(mateStatus, 60000)
