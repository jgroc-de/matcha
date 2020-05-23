window.URL = window.URL || window.webkitURL

function addTag(path) {
    var tag = prompt("add a tag (without the '#'):")

    if (tag)
    {
        var xhr = new XMLHttpRequest()

        xhr.open('POST', path, true)
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                var id = this.responseText
                var span = document.createElement('span')
                var del = document.createElement('span')

                span.id = 'tag' + id
                span.textContent = "- #" + tag + " "
                del.className = 'del'
                del.setAttribute('onclick', 'delUserTag("/tag/", ' + id + ')')
                del.textContent = '(delete)'
                span.appendChild(del)
                document.getElementById('Interest').appendChild(span)
            }
        }
        xhr.send('tag=' + tag)
    }
}

function addPicture() {
    var files = document.querySelectorAll('input[type=file]')
    var len = files.length

    for (var i = 0; i < len; i++)
        files[i].addEventListener('change', addEvent)
}

function addEvent() {
    var id = this.getAttribute('data-id')
    var allowedTypes = ['image/png', 'image/jpeg', 'image/gif']
    var prev = this.parentNode.parentNode

    if (allowedTypes.indexOf(this.files[0].type) === -1) {
        printNotif(['not allowed type (png, jpg/jpeg, gif)', false])
    } else if (this.files[0].size > 400000) {
        printNotif(['file too large (> 400ko)', false])
    } else {
        var form = new FormData()
        var xhttp = new XMLHttpRequest()
        var reader = new FileReader()

        form.append('file', this.files[0])
        xhttp.open('POST', '/picture/' + id.charAt(3), true)
        xhttp.setRequestHeader('enctype', 'multipart/form-data')
        xhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200)
            {
                var path = this.responseText
                var imgElement = document.createElement('img')
                var iElement = document.createElement('i')

                while (prev.firstChild)
                    prev.removeChild(prev.firstChild)
                imgElement.style.maxWidth = '100%'
                imgElement.style.maxHeight = '100%'
                imgElement.title = path
                imgElement.alt = path
                imgElement.className = 'w3-image w3-display-middle'
                imgElement.setAttribute('onclick', 'displayModal("' + reader.result + '")')
                imgElement.src = reader.result
                prev.appendChild(imgElement)
                iElement.className = 'w3-button w3-display-topright w3-hover-red fa fa-remove'
                iElement.title = 'remove picture'
                iElement.setAttribute('onclick', 'deletePic("' + id + '")')
                prev.appendChild(iElement)
            }
            else if (this.readyState === 4 && this.status === 500)
                printNotif(['server trouble… try again later!', false])
        }
        xhttp.send(form)
        reader.readAsDataURL(this.files[0])
    }
}

function deletePic(id)
{
    if (confirm('Delete?')) {
        ggAjax('DELETE', 'picture/' + id.charAt(3), function (id) {
            var labelElmt = document.createElement('label')
            var parentNode = document.getElementById(id)
            var inputElemt = document.createElement('input')
            var inputElemt2 = document.createElement('input')
            var div = document.createElement('div')

            div.className = 'w3-display-container'
            if (id === 'img1')
                div.style.height = '250px'
            else
                div.style.height = '125px'
            labelElmt.className = 'w3-button w3-jumbo w3-display-middle fa fa-plus w3-hover-green w3-block w3-padding-large'
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
}

function ggRemoveChild(id)
{
    var child = document.getElementById(id)

    child.parentNode.removeChild(child)
}

function delFriend(path, id)
{
    if (confirm('Seriously bro?'))
        ggAjax('DELETE', path + id, ggRemoveChild, 'friend' + id)
}

function delFriendReq(path, id)
{
    if (confirm('R U SURE?'))
        ggAjax('DELETE', path + id, ggRemoveChild, 'req' + id)
}

function acceptFriendReq(path, id)
{
    var parent = document.getElementById("Friend")
    var child = document.createElement('div')
    var del = document.createElement('i')
    var a = document.getElementById('req' + id).children[0]

    child.id = "friend" + id
    child.className = "gg-friend"
    del.className = 'fa fa-remove'
    del.title = 'delete'
    del.setAttribute('onclick', 'delFriend("/friend/' + id + '")')

    child.appendChild(a)
    child.appendChild(del)
    parent.appendChild(child)

    ggAjax('POST', path + id, ggRemoveChild, 'req' + id)
    printNotif(["Friend request accepted", true])
}

function delUserTag(path, id)
{
    ggAjax('DELETE', path + id, ggRemoveChild, 'tag' + id)
}

addPicture()
