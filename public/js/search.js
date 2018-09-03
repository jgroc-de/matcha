function sortCard(id2sort, id)
{
    var key = document.getElementById(id2sort).value;
    var father = document.getElementById(id);
    var child;

    generateCard(id, key);
}

function generateCard(idFeed, key)
{
    var i = 1;
    var main = document.getElementById(idFeed);

    if (key)
    {
        if ((key === 'age asc.') || (key === 'distance'))
        {
            if (key === 'age asc.')
                key = 'age';
            usersPos.sort(function(a, b) {return a[key] - b[key]});
        }
        else
        {
            if (key === 'age desc.')
                key = 'age';
            usersPos.sort(function(a, b) {return b[key] - a[key]});
        }
    }
    for (let user of usersPos)
    {
        addChildrenCard(user, main, key, i);
        if (i)
            i = 0;
    }
}

function addChildrenCard(hash, main, key, i)
{
    var div1 = document.createElement("div");
    var div2 = document.createElement("div");
    var div3 = document.createElement("div");
    var div4 = document.createElement("div");
    var a = document.createElement("a");
    var h4 = document.createElement("h4");
    var img = document.createElement("img");
    var p = document.createElement("p");

    div2.className = "w3-theme-l4 w3-row";
    div2.style.height = "300px";
    div2.style.overflow = "auto";
    div3.className = "w3-col s12";
    div3.style.height = "31px";
    div3.style.position = "sticky";
    div3.style.top = "0";
    a.setAttribute('href', '/profil/' + hash.id);
    a.setAttribute('target', '_blank');
    h4.className = "w3-margin-left w3-left";
    h4.innerHTML = hash.title;
    div3.style.backgroundColor = '#' + getColor(hash.kind);
    h4.style.backgroundColor = '#' + getColor(hash.kind);
    div4.className = "w3-right w3-black w3-col s6 w3-display-container";
    img.className = "w3-image w3-display-middle";
    img.style.maxWidth = "95%";
    img.style.maxHeight = "95%";
    div4.style.height = "89%";
    img.setAttribute('src', hash.img);
    p.className = "w3-small w3-col s6";
    p.style.marginTop = 0;
    p.innerHTML = hash.kind + ', ' + hash.age + ': ' + hash.biography;
    a.appendChild(div1);
    div3.appendChild(h4);
    div2.appendChild(div3);
    div4.appendChild(img);
    div2.appendChild(div4);
    div2.appendChild(p);
    div1.appendChild(div2);
    if (key)
    {
        var node = document.getElementById(hash.id);

        a.style.display = node.style.display;
        node.parentNode.removeChild(node);
    }
    if (i <= 0)
        a.className = " w3-hide";
    else
    {
        document.getElementById('add').setAttribute('onclick', 'addFriend(' + hash.id + ')');
        document.getElementById('next').setAttribute('onclick', 'next(' + hash.id + ')');
        a.setAttribute('name', 'visible');
    }
    a.id = hash.id;
    main.appendChild(a);
}

function uncheckTags()
{
    var dad = document.getElementById('tags');
    var tags = dad.childNodes;
    var child;

    for (let tag of tags)
    {
        if (tag.nodeType === 1)
        {
            tag.firstElementChild.removeAttribute("checked");
        }
    }
}

function checkTags()
{
    var dad = document.getElementById('tags');
    var tags = dad.childNodes;
    var child;

    for (let tag of tags)
    {
        if (tag.nodeType === 1)
        {
            tag.firstElementChild.setAttribute("checked", "");
        }
    }
}

function addFriend(id)
{
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
        {
            var txt = document.getElementById("flashText");
            
            txt.textContent = this.responseText;
            toggleDisplay("flash");
            setTimeout(function() {
                toggleDisplay("flash"); 
            }, 3500);
        }
    };
    xmlhttp.open('GET', '/addFriend/' + id, true);
    xmlhttp.send();
}

function next(id1)
{
    var id = document.getElementById(id1).nextSibling.id;

    if (id)
    {
        view(id1, id);
    }
}

function prev(id1)
{
    var id = document.getElementById(id1).previousSibling.id;

    if (id)
    {
        view(id1, id);
    }
}

function view(id1, id)
{
        toggleDisplay(id1);
        toggleDisplay(id);
        document.getElementById(id).setAttribute('name', 'visible');
        document.getElementById(id1).setAttribute('name', '');
        document.getElementById('prev').setAttribute('onclick', 'prev(' + id + ')');
        document.getElementById('add').setAttribute('onclick', 'addFriend(' + id + ')');
        document.getElementById('next').setAttribute('onclick', 'next(' + id + ')');
}

function mapView(id)
{
    var hide = document.getElementsByName('visible')[0];

    view(hide.id, id);
}

generateCard('focus');
