function initMap() {
    var map = new google.maps.Map(
            document.getElementById('map'),
            {center: myPos, zoom: 11}
    );
    var marker;
    for (x in usersPos)
    {
        marker = new google.maps.Marker({
            position: usersPos[x],
            map: map,
            title: usersPos[x].title,
        });
        attachInfo(marker, usersPos[x]); 
    }
}

function attachInfo(marker, info)
{
    var infowindow = new google.maps.InfoWindow({
        content: '<a href="/profil/' + info.id + '"><div class="w3-bar w3-theme-l3"><img src="../' + info.img + '" class="w3-bar-item" style="height:60px"><p class="w3-bar-item">' + info.title + '</p></div></a>'
            
    });
    marker.addListener('click', function() {
        infowindow.open(map, this);
    });
}
