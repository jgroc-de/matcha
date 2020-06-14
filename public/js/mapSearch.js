'use strict'

function initMap() {
    let mapNode = document.getElementById('map')
    mapNode.innerText = ''
    let map = new google.maps.Map(mapNode, {center: myPos, zoom: 11});
    let marker = [];

    for (let key in usersPos) {
        marker = new google.maps.Marker({
            position: usersPos[key],
            map: map,
            icon: 'https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + getColor(usersPos[key].gender),
            title: 'click me!'
        });
        attachInfo(marker, usersPos[key]);
    }
}

function attachInfo(marker, info) {
    let infowindow = new google.maps.InfoWindow({})
    marker.addListener('click', function() {
        infowindow.setContent("<div class='w3-button w3-theme-l5' id=map" + info.id + " data-id=" + info.id + "><h6 class='w3-container' style='background-color:#" + getColor(info.gender) + "'>" + info.pseudo + "</h6><p class='w3-padding' style='margin:0'>" + info.gender + ", "  + info.age + "yo<br>score: " + info.score +"<br></p><img src=" + info.img + " style='height:40px'><div>");
        infowindow.open(map, this)
    })
    google.maps.event.addListener(infowindow, 'domready', function() {
        document.getElementById('map' + info.id).addEventListener("click", mapView, true)
    })
}
