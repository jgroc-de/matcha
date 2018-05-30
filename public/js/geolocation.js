function success(pos) {
    user = {lat: pos.coords.latitude, lng: pos.coords.longitude};
    console.log('success');
}

function initMap() {
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
