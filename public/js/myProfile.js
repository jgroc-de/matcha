function delUserTag(path, id) {
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
        {
            var child = document.getElementById('tag' + id);
            child.parentNode.removeChild(child);
        }
    };
    xhr.open('GET', path, true);
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
    var xhr = new XMLHttpRequest();
        
    if (tag = prompt("add a tag (without the '#'):"))
    {
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200)
            {
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
                button.setAttribute('onclick', 'delUserTag("/delUserTag/' + id + '", ' + id + ')');
                document.getElementById('userTag').appendChild(div);
                document.getElementById(div.id).appendChild(span);
                document.getElementById(div.id).appendChild(button);
            }
            else
                console.log('fail');
        };
        xhr.open('POST', path, true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send('tag=' + tag);
    }
}
