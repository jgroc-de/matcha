function display(id)
{
    var x = document.getElementById(id);
    var forms = document.forms;
    var h2 = document.getElementsByTagName('h2');
    var len = forms.length, i;

    console.log(x);
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

