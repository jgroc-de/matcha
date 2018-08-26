window.URL = window.URL || window.webkitURL;

function dropdown(target) {
    var x = document.getElementById(target);

    if (x.className.indexOf("w3-show") == -1) { 
        x.className += " w3-show";
    } else {
        x.className = x.className.replace(" w3-show", "");
    }
}

function addTag(path) {
    var tag;
        
    if (tag = prompt("add a tag (without the '#'):"))
    {
        var xhr = new XMLHttpRequest();
       
        xhr.open('POST', path, true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var id = this.responseText;
                var div = document.createElement('div');
                var span = document.createElement('span');
                var button = document.createElement('i');
                
                div.id = 'tag' + id;
                div.setAttribute('class', 'w3-bar w3-theme-d1 w3-round-xxlarge w3-half w3-row');
                span.setAttribute('class', 'w3-padding  w3-theme-l1 w3-hover-green w3-col s8');
                span.textContent = '#' + tag;
                span.style.height = '40px';
                button.setAttribute('class', 'w3-button w3-right w3-theme-d1 w3-hover-red w3-col s4 fa fa-remove');
                button.setAttribute('onclick', 'delUserTag("/delUserTag/", ' + id + ')');
                button.style.height = '40px';
                document.getElementById('Interest').appendChild(div);
                document.getElementById(div.id).appendChild(span);
                document.getElementById(div.id).appendChild(button);
            }
        };
        xhr.send('tag=' + tag);
    }
}

function deletePic(id) {
    var xhttp = new XMLHttpRequest();

    if (confirm('Delete?')) {
        xhttp.open('GET', 'delPicture/' + id.charAt(3), true);
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var labelElmt = document.createElement('label');
                var parentNode = document.getElementById(id);
                var inputElemt = document.createElement('input');
                var inputElemt2 = document.createElement('input');
                var div = document.createElement('div');
                
                div.setAttribute('class', 'w3-display-container');
                if (id == 'img1')
                    div.style.height = '250px';
                else
                    div.style.height = '125px';
                labelElmt.setAttribute('class', 'w3-button w3-jumbo w3-display-middle fa fa-plus w3-hover-green w3-block w3-padding-large');
                labelElmt.title = 'add picture';
                inputElemt.id = id;
                inputElemt.type = 'file';
                inputElemt2.type = 'hidden';
                inputElemt.style.display = 'none';
                while (parentNode.hasChildNodes()) {
                    parentNode.removeChild(parentNode.firstChild);
                }
                parentNode.appendChild(div); 
                div.appendChild(labelElmt); 
                labelElmt.appendChild(inputElemt2);
                labelElmt.appendChild(inputElemt);
                addPicture();
            }
        };
        xhttp.send();
    }
}

function addPicture() {
    var files = document.querySelectorAll('input[type=file]');
    var len = files.length;
    var i;

    for (i = 0; i < len; i++)
    {
        files[i].addEventListener('change', addEvent);
    }
}

function addEvent() {
    var id = this.id;
    var allowedTypes = ['png', 'jpg', 'jpeg', 'gif'];
    var prev = this.parentNode.parentNode;

    imgType = this.files[0].name.split('.').pop().toLowerCase();
    if (allowedTypes.indexOf(imgType) != -1) {
        var form = new FormData();
        var xhttp = new XMLHttpRequest();
        var reader = new FileReader();
        
        form.append('file', this.files[0]);
        xhttp.open('POST', '/addPicture/' + id.charAt(3), true);
        xhttp.setRequestHeader('enctype', 'multipart/form-data');
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var path = this.responseText;
                var imgElement = document.createElement('img');
                var iElement = document.createElement('i');

                if (path !== 'fail')
                {
                    while (prev.firstChild) {
                        prev.removeChild(prev.firstChild);
                    }
                    imgElement.style.maxWidth = '100%';
                    imgElement.style.maxHeight = '100%';
                    imgElement.title = path;
                    imgElement.alt = path;
                    imgElement.setAttribute('class', 'w3-image w3-display-middle');
                    imgElement.setAttribute('onclick', 'displayModal("' + reader.result + '")');
                    imgElement.src = reader.result;
                    prev.appendChild(imgElement);
                    iElement.setAttribute('class', 'w3-button w3-display-topright w3-hover-red fa fa-remove');
                    iElement.title = 'remove picture';
                    iElement.setAttribute('onclick', 'deletePic("' + id + '")');
                    prev.appendChild(iElement);
                }
            }
        };
        xhttp.send(form);
        reader.readAsDataURL(this.files[0]);
    }
}

function delFriend(path, id)
{
    if (confirm('R U SURE?'))
    {
        var request = new XMLHttpRequest();

        request.open('GET', path + id, true);
        request.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200)
            {
                var child = document.getElementById('friend' + id);
                child.parentNode.removeChild(child);
            }
        };
        request.send();
    }
}

function delFriendReq(path, id)
{
    if (confirm('R U SURE?'))
    {
        var request = new XMLHttpRequest();

        request.open('GET', path + id, true);
        request.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200)
            {
                var child = document.getElementById('req' + id);

                child.parentNode.removeChild(child);
            }
        };
        request.send();
    }
}

function delUserTag(path, id) {
    var xhr = new XMLHttpRequest();

    xhr.open('GET', path + id, true);
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var child = document.getElementById('tag' + id);

            child.parentNode.removeChild(child);
        }
    };
    xhr.send();
}


addPicture();
