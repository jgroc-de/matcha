function initMap() {
    var map = new google.maps.Map(
            document.getElementById('map'),
            {center: myPos, zoom: 11}
    );
    var marker = [];
    for (x in usersPos)
    {
        marker = new google.maps.Marker({
            position: usersPos[x],
            map: map,
            icon: 'https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + getColor(usersPos[x].kind),
            title: 'click me!'
        });
        attachInfo(marker, usersPos[x]);
    }
}

function attachInfo(marker, info)
{
    var infowindow = new google.maps.InfoWindow({
    });
    marker.addListener('click', function() {
        infowindow.setContent("<div class='w3-button w3-theme-l5' onclick='mapView(" + info.id + ")'><h6 class='w3-container' style='background-color:#" + getColor(info.kind) + "'>" + info.title + "</h6><p class='w3-padding' style='margin:0'>" + info.kind + ", "  + info.age + "yo<br>score: " + info.score +"<br></p><img src=" + info.img + " style='height:40px'><div>"); 
        infowindow.open(map, this);
    });
}
