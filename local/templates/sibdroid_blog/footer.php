<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitronic2\Mobile;
use Yenisite\Core\Tools;
use Yenisite\Core\Page;

//@var $arDefIncludeParams set in header.php
\Bitrix\Main\Localization\Loc::loadMessages('header');
?>
        </div>
    </main>
  <footer>
    <div class="container">
      <div class="grid">
          <div class="grid__cell footer__logo">
            <img src="<?=SITE_TEMPLATE_PATH?>/img/logo.png" alt="">
            <div class="footer__description">
              <div class="footer__description_text">
                <div>Блог с интересными статьями и свежими новостями от интернет-магазина «Sibdroid»</div>
              </div>

              <div class="mobile-contacts">
                <div class="footer__contacts_contacts">
                  <div class="bg icon-footer icon-mail">
                    <a href="mailto:sales@sibdroid.ru">sales@sibdroid.ru</a>
                  </div>
                  <div class="bg icon-footer icon-phone">
                    <a href="tel:+78003335587">8-800-333-55-87</a>
                  </div>
                  <div class="bg icon-footer icon-site">
                    <a href="https://sibdroid.ru">sibdroid.ru</a>
                  </div>
                </div>
              </div>

              <div class="footer__description_copyritghts">2013 - 2019 (c) Sibdroid.ru</div>
            </div>
          </div>
          <div class="grid__cell footer_right va-top">
            <div class="footer__contacts">
              <div class="footer__contacts_head">
                Контактная информация:
              </div>
              <div class="footer__contacts_contacts">
                <div class="bg icon-footer icon-mail">
                  <a href="mailto:sales@sibdroid.ru">sales@sibdroid.ru</a>
                </div>
                <div class="bg icon-footer icon-phone">
                  <a href="tel:+78003335587">8-800-333-55-87</a>
                </div>
                <div class="bg icon-footer icon-site">
                  <a href="https://sibdroid.ru">sibdroid.ru</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>

<!-- Modal HTML embedded directly into document -->

<div id="auth_modal" class="modal alert-modal">
  <div class="modal_content">
    <div class="alert-modal__title">
      Авторизация на сайте
    </div>
    <div class="alert-modal__body">
      <?
        $APPLICATION->IncludeComponent(
            "ulogin:auth",
            "",
            Array(
                "SEND_MAIL" => "N",
              "SOCIAL_LINK" => "Y",
              "GROUP_ID" => array("5"),
              "ULOGINID1" => "fe5b645a",
              "ULOGINID2" => ""
          ) 
        ); 
      ?>
    </div>
  </div>
</div>

<div id="alert_modal" class="modal alert-modal">
  <div class="modal_content">
    <div class="alert-modal__title">
      Воу-воу!<br>Слишком много <span class="entity"></span>!
    </div>
    <div class="alert-modal__body">
      <img src="<?=SITE_TEMPLATE_PATH?>/img/cat.png" alt="alert">
    </div>
    <div class="alert-modal__footer"> 
      <a href="#close-modal" class="btn" rel="modal:close">Я больше не буду :(</a>
    </div>
  </div>
</div>

<?if(!$_SESSION['is_bot'] && !isset($_REQUEST['noredirect']) && !$_SESSION['is_dev'] && $_SESSION["VREGIONS_REGION"]['ID'] == 14647):?>
      <script>
          if(sib_blog.utils.getCookie('IS_REDIRECTED') !== 'redirected'){
                $.ajax({
                    url: '/ajax/sib/city_detect.php',
                    async: true,
                    dataType: 'json',
                    data: {request_uri: '<?=$_SERVER['REQUEST_URI']?>'},
                    success: function(data){ 
                        if(!!data.LINK){
                          sib_blog.utils.setCookie('IS_REDIRECTED', 'redirected');
                            if(data.LINK !== 'default'){
                                window.location.href = data.LINK;
                            }                      
                        }                        
                    }
                });
            }
      </script>
  <?endif?>
        <?/* if (!$USER->IsAdmin() && !$_SESSION['is_bot']): ?>
            <script type='text/javascript'>
                function jivo_onLoadCallback() {document.jivo_container.Audio.prototype.play = function() {};}
                (function(){ var widget_id =  '<?=\Sib\Core\Catalog::isMskRegion($_SESSION["VREGIONS_REGION"]['ID']) ? '8A6pBGdcUq' : '5dkEEcBUgv'?>';var d=document;var w=window;function l(){
                var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
            </script>
        <? endif; */ ?>
        <script async src="<?=SITE_TEMPLATE_PATH?>/js/up.min.js"></script>
    </body>
</html>
<? Page::setOGProperty('title', $APPLICATION->GetTitle(false));