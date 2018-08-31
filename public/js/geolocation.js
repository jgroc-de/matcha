function updateGeolocation() {
    if (navigator.geolocation)
        navigator.geolocation.getCurrentPosition(success, error);
    else
        error();
}

function setLocation() {
    user.lat = Number(document.getElementById('lat').value);
    user.lng = Number(document.getElementById('lng').value);
    majLocation();
}

function success(pos) {
    user = {lat: pos.coords.latitude, lng: pos.coords.longitude};
    majLocation();
}

function majLocation () {
    var request = new XMLHttpRequest();
    var params = 'lat=' + user.lat + '&lng=' + user.lng;

    request.open('POST', '/updateGeolocation', true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
        {
            var response = JSON.parse(this.responseText);
            var p = document.getElementById('textLocation');

            user.lat = response.lat;
            user.lng = response.lng;
            p.innerHTML = 'You are currently located at ' + user.lat + '° of lattitude north and ' + user.lng +'° of longitude east.';
            document.getElementById('lat').value = user.lat;
            document.getElementById('lng').value = user.lng;
            initMap(); 
        }
    };
    request.send(params);
}

function initMap() {
    var map = new google.maps.Map(
            document.getElementById('Location'),
            {center: user, zoom: 11}
            );
    var marker, markers = [];
    marker = new google.maps.Marker({
        position: user,
        map: map,
        draggable:true,
        title: user.title + ": drag me!"
    });
    google.maps.event.addListener(marker, 'dragend', function (event) {
        document.getElementById('lat').value = this.getPosition().lat().toFixed(7);
        document.getElementById('lng').value = this.getPosition().lng().toFixed(7);
    });
    markers.push(marker);
    for (x in usersPos)
    {
        marker = new google.maps.Marker({
            position: usersPos[x],
            map: map,
            icon: 'https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + getColor(usersPos[x].kind),
            title: usersPos[x].title
        });
    }
}

function error(err) {
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

function changeLocation()
{
    user.lat = Number(document.getElementById('lat').value);
    user.lng = Number(document.getElementById('lng').value);
    initMap();
}
