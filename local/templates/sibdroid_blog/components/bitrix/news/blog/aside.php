<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $blogApp;
?>
<aside class="grid__cell">
    <div class="aside__block">
        <div class="aside__block_head">Темы</div>
        <div class="aside__block_body aside__block_body__menu">

            <div class="list-aside">
                <?=$blogApp->getAsideSectionsListTemplate();?>
            </div>

            <div class="list-aside">
                <?=$blogApp->getAsideTagListTemplate();?>
            </div>

        </div>
    </div>
    <div class="aside__block">
        <div class="aside__block_head">Мы в социальных сетях</div>
        <div class="aside__block_body">
            <a class="icon-social icon-vk" target="_blank" href="https://vk.com/sibdroid">VK</a>
            <a class="icon-social icon-inst" target="_blank" href="https://www.instagram.com/sibdroid/">INSTAGRAMM</a>
            <a class="icon-social icon-yt" target="_blank" href="https://www.youtube.com/channel/UCBimjgo8woDwT3gX-ntehlQ">YOUTUBE</a>
            <a class="icon-social icon-fb" target="_blank" href="https://www.facebook.com/sibdroid/">FACEBOOK</a>
            <a class="icon-social icon-zen" target="_blank" href="https://sibdroid.ru/sibzen">Yandex Zen</a>
        </div>
    </div>
    <?
        $date = new \DateTime();
        echo $blogApp->getAsideItemsBlockTemplate([
            'TITLE' => 'Популярное за три дня',
            'ORDER' => ['PROPERTY_BLOG_RATING' => 'DESC', 'ID' => 'DESC'],
            'FILTER' => [
                'IBLOCK_ID' => 1,
                'ACTIVE' => 'Y',
                '<=DATE_ACTIVE_FROM' => $date->format('d.m.Y') . ' 23:59:59',
                '>=DATE_ACTIVE_FROM' => $date->modify('-3 days')->format('d.m.Y') . ' 00:00:00'
            ]
        ]);
    ?>
    <?
        $date = new \DateTime();
        echo $blogApp->getAsideItemsBlockTemplate([
            'TITLE' => 'Обсуждаемое',
            'ORDER' => ['PROPERTY_BLOG_COMMENTS' => 'DESC', 'ID' => 'DESC'],
            'FILTER' => [
                'IBLOCK_ID' => 1,
                'ACTIVE' => 'Y',
                '<=DATE_ACTIVE_FROM' => $date->format('d.m.Y') . ' 23:59:59',
                '>=DATE_ACTIVE_FROM' => $date->modify('-1 month')->format('d.m.Y') . ' 00:00:00',
                '>PROPERTY_BLOG_COMMENTS' => 0
            ]
        ]);
    ?>
    
    <?=$blogApp->getAsideCommentTemplate();?>
</aside>