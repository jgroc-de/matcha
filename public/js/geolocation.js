function updateGeolocation() {
    if (navigator.geolocation)
        navigator.geolocation.getCurrentPosition(success, error);
    else
        error();
}

function success(pos) {
    user = {lat: pos.coords.latitude, lng: pos.coords.longitude};
    majLocation();
}

function majLocation () {
    var request = new XMLHttpRequest();
    var params = 'lat=' + user.lat + '&lng=' + user.lng;

    request.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
        {
            var response = JSON.parse(this.responseText);
            user.lat = response.lat;
            user.lng = response.lng;
            initMap(); 
        }
    };
    request.open('POST', '/updateGeolocation', true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(params);
}

function initMap() {
    var map = new google.maps.Map(
            document.getElementById('map'),
            {center: user, zoom: 11}
            );
    var marker, markers = [];
    marker = new google.maps.Marker({
        position: user,
        map: map,
        title: user.title
    });
    markers.push(marker);
    for (x in usersPos)
    {
        marker = new google.maps.Marker({
            position: usersPos[x],
            map: map,
            icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + getColor(usersPos[x].kind),
            title: usersPos[x].title
        });
        attachInfo(marker, usersPos[x]);
        markers.push(marker);
    }
}

function error(err) {
    user.lat = 0;
    user.lng = 0;
    majLocation();
}

function attachInfo(marker, info)
{
    var infowindow = new google.maps.InfoWindow({
        content: '<div class="w3-theme-l1"><img src="../' + info.img + '" class="w3-image" style="height:100px"><h3 class="">' + info.title + '</h3></div>'
            
    });
    marker.addListener('mouseover', function() {
        infowindow.open(map, this);
    });
    marker.addListener('mouseout', function() {
        infowindow.close(map, this);
    });
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
