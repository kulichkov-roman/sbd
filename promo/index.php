<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

   <!-- Add the slick-theme.css if you want default styling -->
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css"/>
<!-- Add the slick-theme.css if you want default styling -->
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css"/>

    <title>Promo</title>
</head>
<body style="margin:0;padding:0;">
    <style>
        img{
            max-width: 100%;
        }
    </style>
    <div class="test" style="max-width:100%;overflow:hidden;">
        <div><img src="https://images.wallpaperscraft.ru/image/samolet_okno_illiuminator_146240_1920x1080.jpg" alt=""></div>
        <div><img src="https://images.wallpaperscraft.ru/image/relsy_zheleznaia_doroga_derevia_146233_1920x1080.jpg" alt=""></div>
        <div><img src="https://images.wallpaperscraft.ru/image/koloski_pshenitsa_pole_146223_1920x1080.jpg" alt=""></div>

        <div><img src="https://images.wallpaperscraft.ru/image/zvezdnoe_nebo_lodka_otrazhenie_125803_3840x2160.jpg" alt=""></div>
        <div><img src="https://images.wallpaperscraft.ru/image/gory_odinochestvo_domik_124060_3840x2160.jpg" alt=""></div>
        <div><img src="https://images.wallpaperscraft.ru/image/podsolnuhi_odinochestvo_uedinenie_124387_3840x2160.jpg" alt=""></div>
    </div>
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
    
        $(".test").slick({
            dots:!1,
            infinite:!0,
            autoplay:!0,
            autoplaySpeed:5e3,
            speed:700,
            cssEase:"linear",
            slidesToShow:1,
            slidesToScroll:1,
            pauseOnHover:!1
        });
    </script>
</body>
</html>