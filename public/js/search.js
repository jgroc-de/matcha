function sortCard(id2sort, id)
{
    var key = document.getElementById(id2sort).value;
    var father = document.getElementById(id);

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
        main.appendChild(addChildrenCard(user, key, i));
        if (i)
            i = 0;
    }
}

function createTitle(hash)
{
    var h4 = document.createElement("div");
    
    h4.className = "w3-col s12 w3-padding";
    h4.innerHTML = hash.title;
    h4.style.backgroundColor = '#' + getColor(hash.kind);
    h4.style.height = "10%";
    return h4;
}

function createProfil(hash, i)
{
    var divProfil = document.createElement("div");
    var divImg = document.createElement("div");
    var img = document.createElement("img");
    var p = document.createElement("div");
    
    img.name = hash.img;
    img.className = "w3-image w3-display-middle";
    img.style.maxWidth = "95%";
    img.style.maxHeight = "95%";
    p.className = "w3-col s6 w3-padding";
    p.innerHTML = hash.kind + ', ' + hash.age + 'yo, ' + hash.popularity + 'pts<br>' + hash.biography;
    divImg.className = "w3-right w3-black w3-col s6 w3-display-container";
    divImg.style.height = "100%";
    divImg.appendChild(img);
    divProfil.style.height = "90%";
    divProfil.className = "w3-col s12 w3-row";
    divProfil.appendChild(divImg);
    divProfil.appendChild(p);
    if(i === 1)
        img.setAttribute('src', hash.img);
    return divProfil;
}

function createSkeleton(hash, i)
{
    var div2 = document.createElement("div");
    
    div2.appendChild(createTitle(hash));
    div2.appendChild(createProfil(hash, i));
    div2.className = "w3-theme-l4 w3-row";
    div2.style.height = "500px";
    div2.style.overflow = "auto";
    return div2;
}

function addChildrenCard(hash, key, i)
{
    var a = document.createElement("a");
    var node = document.getElementById(hash.id);

    a.appendChild(createSkeleton(hash, i));
    if (key)
    {
        a.style.display = node.style.display;
        node.parentNode.removeChild(node);
    }
    a.className = 'w3-col s12';
    if (i <= 0)
        a.classList.add("w3-hide");
    else
    {
        document.getElementById('add').setAttribute('onclick', 'addFriend(' + hash.id + ')');
        document.getElementById('next').setAttribute('onclick', 'next(' + hash.id + ')');
        a.setAttribute('name', 'visible');
    }
    a.setAttribute('href', '/profil/' + hash.id);
    a.setAttribute('target', '_blank');
    a.id = hash.id;
    return a;
}

function uncheckTags()
{
    var dad = document.getElementById('tags');
    var tags = dad.childNodes;

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
    ggAjaxGet('/addFriend/' + id, printNotif, ['response', true]);
}

function next(id1)
{
    var nextNode = document.getElementById(id1);

    if (nextNode.nextSibling)
    {
        view(id1, nextNode.nextSibling.id);
    }
}

function prev(id1)
{
    var prevNode = document.getElementById(id1);

    if (prevNode.previousSibling.id)
    {
        view(id1, prevNode.previousSibling.id);
    }
}

function setImg(node)
{
    if (node.firstChild)
    {
        var img = node.getElementsByTagName('img')[0];

        if (img)
            img.setAttribute('src', img.name);
    }
}

function setPrevNext(node)
{
    var prev = node.previousSibling;
    var prev2 = prev.previousSibling;
    var next = node.nextSibling;
    var next2 = next.nextSibling;

    setImg(node);
    setImg(prev);
    setImg(next);
    setImg(prev2);
    setImg(next2);
}

function view(id1, id)
{
    var divSelected = document.getElementById(id);

    setPrevNext(divSelected);
    toggleById(id1);
    toggleById(id);
    divSelected.setAttribute('name', 'visible');
    document.getElementById(id1).setAttribute('name', '');
    document.getElementById('prev').setAttribute('onclick', "prev(" + id + ")");
    document.getElementById('add').setAttribute('onclick', "addFriend(" + id + ")");
    document.getElementById('next').setAttribute('onclick', "next(" + id + ")");
}

function mapView(id)
{
    var hide = document.getElementsByName('visible')[0];

    view(hide.id, id);
    document.getElementsByTagName('h3')[0].scrollIntoView();
}

generateCard('focus');
