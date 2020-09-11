$('[name="romza_feedback[EMAIL]"], [name="romza_feedback[TEXT]"]').prop('required', !0);
$('#ys-guestbook').on('submit', function(e){
    e.preventDefault();
    e.stopPropagation();
    
    var dataSend = $(this).serialize();
    $.ajax({
        url: '/ajax/sib/rbs_contact.php',
        type: "POST",
        method : "POST",
        data: dataSend,
        success: function(data){
            $('body').append($(data));
            document.getElementById('ys-guestbook').reset();
        },
        dataType: 'html'
    });
    return false; 
}); 

//<script src="https://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU"></script>
//Yandex Map
/* if ($("#map").length && 'regionCenter' in window && 'regionAddress' in window) {
    ymaps.ready(function () {

        var myMap = new ymaps.Map('map', {
            center: regionCenter,
            zoom: 10
        });

        myMap.behaviors.enable('scrollZoom');

        myPlacemark = new ymaps.Placemark(regionCenter, {
            hintContent: 'Интернет-магазин современной электроники Sibdroid',
            balloonContent: regionAddress
        }, {
            iconImageHref: SITE_TEMPLATE_PATH + '/new_img/marker.png',
            iconImageSize: [28, 39]
        });

        myMap.geoObjects.add(myPlacemark);
    });
}; */