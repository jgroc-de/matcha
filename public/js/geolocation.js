function updateGeolocation() {
    user = {};
    if (navigator.geolocation)
        navigator.geolocation.getCurrentPosition(success, error);
    else
    {
        majLocation();
    }
}

function success(pos) {
    user = {lat: pos.coords.latitude, lng: pos.coords.longitude};
    console.log(user);
    majLocation();
}

function majLocation () {
    var request = new XMLHttpRequest();
    var params = 'lat=' + user.lat + '&lng=' + user.lng;

    request.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200)
        {
            initMap(); 
        }
    };
    request.open('POST', '/updateGeolocation', true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send(params);
}

function initMap() {
    console.log(user);
    var map = new google.maps.Map(
            document.getElementById('map'),
            {center: user, zoom: 11}
            );
    var marker = new google.maps.Marker({
        position: user,
        map: map,
        title: user.title
    });
}

function error(err) {
    console.warn(`ERROR(${err.code}): ${err.message}`);
}