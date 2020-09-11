<?
$MESS["NEWS_COUNT"] = "Кол-во показываемых баннеров";
$MESS["YOUTUBE_PARAMETERS"] = "Дополнительные параметры для плеера YouTube";
$MESS["YOUTUBE_PARAMETERS_TIP"] = 'Строка с параметрами проигрывателя из <a href="https://developers.google.com/youtube/player_parameters?hl=ru#Parameters" class="text-primary">YouTube IFrame Player API</a>. Параметры нужно писать слитно без пробелов, разделяя знаком амперсанда.
<br>
<br>Например, не показывать в конце похожие видео:
<br><span class="text-info">rel=0</span>
<br>Зациклить воспроизведение видеоролика:
<br><span class="text-info">version=3&amp;loop=1&amp;playlist=%VIDEO_ID%</span>
<br>
<br>Вместо макроса <span class="text-primary">%VIDEO_ID%</span> будет подставлен идентификатор видеоролика из свойства инфоблока.
<br><span class="text-muted">
<br>Со списком всех доступных параметров можно ознакомиться в <a href="https://developers.google.com/youtube/player_parameters?hl=ru#Parameters">справочнике YouTube</a>.
<br>Параметр autoplay уже имеется в свойствах инфоблока с баннерами и включается через API Javascript, поэтому не нужно указывать его в списке общих параметров.</span>';

// Resizer
$MESS["RESIZER_SET"] = "Набор Ресайзера";
?>