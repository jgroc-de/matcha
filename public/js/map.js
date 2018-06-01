function initMap() {
    var map = new google.maps.Map(
            document.getElementById('map'),
            {center: myPos, zoom: 11}
    );
    var marker, markers = [];
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
    var markerCluster = new MarkerClusterer(map, markers, {
        maxZoom: 13,
        minimumClusterSize: 8,
        imagePath: 'googleMap/markerclusterer/images/m'
    });
}

function attachInfo(marker, info)
{
    var infowindow = new google.maps.InfoWindow({
        content: '<a href="/profil/' + info.id + '"><div class="w3-bar w3-theme-l3"><img src="../' + info.img + '" class="w3-bar-item" style="height:60px"><p class="w3-bar-item">' + info.title + '</p></div></a>'
            
    });
    marker.addListener('mouseover', function() {
        infowindow.open(map, this);
    });
    marker.addListener('click', function() {
        var elem = document.getElementById('' + info.id);
        elem.style.display = "block";
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