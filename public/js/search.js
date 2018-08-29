function sortCard(id2sort, id)
{
    var key = document.getElementById(id2sort).value;
    var father = document.getElementById(id);
    var child;

    if (id !== 'focus')
    {
        while (child = father.firstChild)
        {
            father.removeChild(child);
        }
    }
    generateCard(id, key);
}

function generateCard(idFeed, key)
{
    var i = 0;
    var j = 7;
    var main = document.getElementById(idFeed);

    if (idFeed === 'focus')
    {
        j = usersPos.length;
    }
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
        if (i++ > j)
            break;
        addChildrenCard(user, main, key);
    }
}

function addChildrenCard(hash, main, key)
{
    var div1 = document.createElement("div");
    var div2 = document.createElement("div");
    var div3 = document.createElement("div");
    var a = document.createElement("a");
    var h4 = document.createElement("h4");
    var img = document.createElement("img");
    var p = document.createElement("p");

    div2.className = "w3-theme-l4";
    div2.style.height = "100px";
    div2.style.overflow = "auto";
    div3.className = "w3-theme-l1";
    div3.style.width = "100%";
    div3.style.height = "31px";
    div3.style.position = "sticky";
    div3.style.top = "0";
    a.setAttribute('href', '/profil/' + hash.id);
    h4.className = "w3-margin-left w3-left w3-theme-l1";
    h4.innerHTML = hash.title;
    img.className = "w3-right";
    img.style.width = "50%";
    img.setAttribute('src', hash.img);
    p.className = "w3-small";
    p.style.marginTop = 0;
    p.innerHTML = hash.kind + ', ' + hash.age + ': ' + hash.biography;
    a.appendChild(h4);
    div3.appendChild(a);
    div2.appendChild(div3);
    div2.appendChild(img);
    div2.appendChild(p);
    div1.appendChild(div2);
    if (main.id === 'focus')
    {
        var i = document.createElement("i");

        if (key)
        {
            var node = document.getElementById(hash.id);

            div1.style.display = node.style.display;
            node.parentNode.removeChild(node);
        }
        div1.className = "hide";
        div1.id = hash.id;
        i.className = "w3-button w3-right fa fa-remove w3-hover-red";
        i.setAttribute('onclick', 'closeInfo(' + hash.id + ')');
        div3.appendChild(i);
    }
    else
        div1.className = "w3-col s12 m3";
    main.appendChild(div1);
}

generateCard('focus');
generateCard('try');
