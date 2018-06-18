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
                var button = document.createElement('button');
                
                div.id = 'tag' + id;
                div.setAttribute('class', 'w3-bar w3-theme-l1 w3-round-xxlarge');
                span.setAttribute('class', 'w3-padding');
                span.textContent = '#' + tag;
                button.setAttribute('class', 'w3-button w3-theme-d1 w3-hover-red fa fa-remove');
                button.setAttribute('style', 'border-radius: 0 100px 100px 0');
                button.setAttribute('onclick', 'delUserTag("/delUserTag/", ' + id + ')');
                document.getElementById('userTag').appendChild(div);
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
        xhttp.open('GET', 'delPicture/' + id, true);
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var labelElmt = document.createElement('label');
                var parentNode = document.getElementById('img' + id);
                var inputElemt = document.createElement('input');
                var inputElemt2 = document.createElement('input');
                
                labelElmt.setAttribute('class', 'w3-button w3-jumbo w3-display-middle fa fa-plus w3-hover-green w3-block w3-padding-large');
                labelElmt.setAttribute('title', 'add picture');
                inputElemt.setAttribute('id', id);
                inputElemt.setAttribute('type', 'file');
                inputElemt2.setAttribute('type', 'hidden');
                inputElemt.setAttribute('style', 'display:none');
                inputElemt2.setAttribute('style', 'MAX_FILE_SIZE');
                inputElemt2.setAttribute('value', '30000');
                while (parentNode.hasChildNodes()) {
                    parentNode.removeChild(parentNode.firstChild);
                }
                parentNode.appendChild(labelElmt); 
                labelElmt.appendChild(inputElemt2);
                labelElmt.appendChild(inputElemt);
                addPicture();
            }
        };
        xhttp.send();
    }
}

