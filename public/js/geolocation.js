'use strict'

function initMap() {
    let map = new google.maps.Map(
            document.getElementById('Location'),
            {center: user, zoom: 11}
    )
    let marker = new google.maps.Marker({
        position: user,
        map: map,
        draggable:true,
        title: user.title + ": drag me!"
    })
    google.maps.event.addListener(marker, 'dragend', function (event) {
        document.getElementById('lat').value = this.getPosition().lat().toFixed(7)
        document.getElementById('lng').value = this.getPosition().lng().toFixed(7)
    })
    for (let x in usersPos) {
        marker = new google.maps.Marker({
            position: usersPos[x],
            map: map,
            icon: 'https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + getColor(usersPos[x].kind),
            title: usersPos[x].title
        })
    }
}

function majLocation (user) {
    let request = new XMLHttpRequest()
    let params = 'lat=' + user.lat + '&lng=' + user.lng

    request.open('PUT', '/updateGeolocation', true)
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    request.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let response = JSON.parse(this.responseText)
            let p = document.getElementById('textLocation')

            user.lat = response.lat
            user.lng = response.lng
            p.innerHTML = 'You are currently located at ' + user.lat + '° of lattitude north and ' + user.lng +'° of longitude east.'
            document.getElementById('lat').value = user.lat
            document.getElementById('lng').value = user.lng
            initMap() 
        }
    }
    request.send(params)
}

function changeLocation() {
    user.lat = Number(document.getElementById('lat').value)
    user.lng = Number(document.getElementById('lng').value)
    initMap()
}

function success(pos) {
    user = {lat: pos.coords.latitude, lng: pos.coords.longitude}
    majLocation(user)
}

function setGeoEventlisteners() {
    document.getElementById('resetGeo').addEventListener('click', function() {
        navigator.geolocation ? navigator.geolocation.getCurrentPosition(success, error):error()
    })
    var latInput = document.getElementById('lat')
    var lngInput = document.getElementById('lng')
    document.getElementById('setGeo').addEventListener('click', function() {
        user.lat = Number(latInput.value)
        user.lng = Number(lngInput.value)
        majLocation(user)
    })
    latInput.addEventListener('change', changeLocation)
    lngInput.addEventListener('change', changeLocation)
}

function error(err) {
    printNotif(['Your navigator can not geolocalise you. Switch ON the GPS?', false])
}

setGeoEventlisteners()

