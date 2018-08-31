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
            icon: 'https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + getColor(usersPos[x].kind),
            title: 'click me!'
        });
        attachInfo(marker, usersPos[x]);
        markers.push(marker);
    }
    /*var markerCluster = new MarkerClusterer(map, markers, {
        maxZoom: 13,
        minimumClusterSize: 8,
        imagePath: 'googleMap/markerclusterer/images/m'
    });*/
}

function attachInfo(marker, info)
{
    var infowindow = new google.maps.InfoWindow({
        content: "<div class='w3-theme-l1' onclick='toggleDisplay(\"" + info.id + "\")'><img src='../" + info.img + "' class='gg-image'><h6 class=''>" + info.title + "</h6></div>" 
    });
    marker.addListener('click', function() {
        infowindow.open(map, this);
    });
    marker.addListener('mouseout', function() {
        //infowindow.close(map, this);
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
