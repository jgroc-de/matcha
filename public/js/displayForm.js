var forms = document.forms;
var h2 = document.getElementsByTagName('h2');
var len = forms.length, i;

function display(id)
{
    var x = document.getElementById(id);

    for (i = 0; i < len; i++)
    {
        h2[i].className = h2[i].className.replace("w3-theme-l1", "w3-theme-d1");
        if (forms[i].className.indexOf("w3-show") != -1 && x != forms[i])
        {
            forms[i].className = forms[i].className.replace(" w3-show", "");
        }
    }
    if (x.className.indexOf("w3-show") == -1)
    {
        x.className += " w3-show";
    }
    else
    {
        x.className = x.className.replace(" w3-show", "");
    }
}

function displayMenu(id)
{
    var x = document.getElementById(id);

    if (x.className.indexOf("w3-show") == -1)
        x.className += " w3-show";
    else
        x.className = x.className.replace(" w3-show", "");
}

function toggleDisplay(id)
{
    var x = document.getElementById(id);

    if (x.className.indexOf("w3-hide") == -1)
        x.className += " w3-hide";
    else
        x.className = x.className.replace(" w3-hide", "");
}

function toggleDisplay2(x)
{
    while (x = x.nextSibling)
    {
        if ((x.className) || (x.innerHTML))
        {
            if (x.className.indexOf("w3-hide") == -1)
                x.className += " w3-hide";
            else
                x.className = x.className.replace(" w3-hide", "");
        }
    }
}

function closeInfo(id) {
    document.getElementById('' + id).style.display = "none";
}

function getColor(kind)
{
    switch(kind)
    {
        case 'Rick':
            return '878f99';
            break;
        case 'Jerry':
            return 'ff7b25';
            break;
        case 'Beth':
            return '6b5b95';
            break;
        case 'Morty':
            return 'feb236';
            break;
        case 'Summer':
            return 'd64161';
            break;
        default:
            return '000';
    }
}
