<? 
global $APPLICATION;
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/cardinput/card-info.min.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/cardinput/jquery.mask.min.js');
global $USER; 

if($USER->IsAdmin()):?>
<style>      
    .wrap_cart{
        width: 100%;
        font-size: 30px;
    }
      #cards {
        width: 19.5em;
        height: 10em;
        position: relative;
        margin: 0 auto;
      }
      #front, #back {
        position: absolute;
        width: 14.5em;
        height: 9em;
        border-radius: 0.5em;
      }
      #front {
        top: 0;
        left: 0;
        background: #ddd;
        z-index: 100;
      }
      #number {
        width: 100%;
        margin-bottom: 0.3em;
      }
      #front-fields {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 1em;
      }
      #bank-link {
        position: absolute;
        top: 1em;
        right: 1em;
        width: 320px;
        height: 60px;
        display: block;
        position: relative;
        left: 1em;
        background-size: contain;
        background-position: top left;
        background-repeat: no-repeat;
      }
      #brand-logo {
        position: absolute;
        bottom: 1em;
        right: 1em;
        text-align: right;
        height: 1.6em;
      }
      #back {
        bottom: 0;
        right: 0;
        background: #bbb;
        padding-top: 1em;
        padding-right: 1em;
        padding-left: 10.5em;
      }
      #code {
        width: 100%;
      }
      .field {
        padding: 0.3em 0.5em;
        border: 1px solid black;
        border-radius: 10px;
        font-size: 0.9em;
        text-indent: 0.1em;
      }
      .bottom-card{

      }
      .holder-input{
          float: left;
          width: 60%;
      }
      .holder{
            font-size: 18px;
      }
      .date-input{
          float: right;
      }
      .expired {
        font-size: 18px;
        width: 3em;
        margin-left: 0.5em;
      }
      .label {
        font-size: 0.5em;
        display: block;
        margin-top: 0.5em;
      }
      #examples {
        list-style: none;
        padding: 0;
      }
      .example {
        font-size: 0.5em;
        white-space: nowrap;
        display: inline-block;
        margin-right: 1.5em;
        margin-top: 0.3em;
      }
      .example-link {
        text-decoration: none;
        color: #07c;
        border-bottom: 1px dashed #07c;
      }
      .example-link:hover, .example-link:active {
        color: #c00;
        border-color: #c00;
      }
      .block {
        margin: 1em 0 0;
      }
      .block:first-child {
        margin: 0;
      }
      .block h2 {
        margin: 0 0 0.3em 0;
        font-size: 0.7em;
      }
      ul {
        margin: 0;
      }
      #validation {
        font-size: 0.5em;
      }
      #validation li {
        margin-bottom: 10px;
      }
      #validation li.valid {
        color: green;
      }
      #validation li.invalid {
        color: red;
      }
      #instance {
        font-size: 0.5em;
        line-height: 1.5em;
        
      }
</style>
<form class="wrap_cart">
    <div id="cards">
        <div id="front">
            <a target="_blank" href="#" id="bank-link"></a>        
            <div id="front-fields">
                <input class="field" id="number" name="card-number" type="text" placeholder="XXXX XXXX XXXX XXXX">
                <div class="bottom-card">
                    <div class="holder-input">
                        <label class="label">Имя держателя</label>
                        <input class="field holder" id="cardholder" name="card-holder" type="text" placeholder="ИМЯ ДЕРЖАТЕЛЯ">
                    </div>
                    <div class="date-input">
                        <label class="label">Срок действия</label>
                        <input class="field expired" id="mm" name="exp-m" type="text" placeholder="MM">
                        <input class="field expired" id="yy" name="exp-y" type="text" placeholder="YY">
                    </div>
                </div>
            </div>
        </div>
        <div id="back">
            <img src="" alt="" id="brand-logo">
            <input class="field" id="code" name="cvv" type="text" placeholder="XXX">
            <label id="code-label" class="label">Код защиты</label>
        </div>
    </div>
    <div>
      <div id="send" class="button">Оплатить</div>
    </div>
    <pre style="display:none" id="instance"></pre>
</form>


    <script>
      CardInfo.setDefaultOptions({
        banksLogosPath: '<?=SITE_TEMPLATE_PATH?>/js/cardinput/banks-logos/',
        brandsLogosPath: '<?=SITE_TEMPLATE_PATH?>/js/cardinput/brands-logos/'
      });

     

      $(function() {
        var $front = $('#front')
        var $bankLink = $('#bank-link')
        var $brandLogo = $('#brand-logo')
        var $number = $('#number')
        var $code = $('#code')
        var $random = $('#random')
        var $instance = $('#instance')
        var $expM = $('#mm')
        var sendedPrefix = window.location.search.substr(1);

        var validateCardInfo = function(){
          
        };


        $('#mm').mask('99');
        $('#yy').mask('99');
        $code.mask('999'); 

        $('#cardholder').on('keydown', function(e){
          
        });

        $('#send').on('click', function(){
          $.ajax({
            url: '/ajax/sib/pay.php',
            method: 'post',
            dataType: 'json',
            data: $('.wrap_cart').serialize(),
            success: function(result){
              console.log(result);
            }
          });
        });

        $number.on('keyup change paste', function () {
          var cardInfo = new CardInfo($number.val());
          if (cardInfo.bankUrl) {
            $bankLink
              .attr('href', cardInfo.bankUrl)
              .css('backgroundImage', 'url("' + cardInfo.bankLogo + '")')
              .show()
          } else {
            $bankLink.hide()
          }
          $front
            .css('background', cardInfo.backgroundGradient)
            .css('color', cardInfo.textColor)
          $code.attr('placeholder', cardInfo.codeName ? cardInfo.codeName : '')
          $number.mask(cardInfo.numberMask)
          if (cardInfo.brandLogo) {
            $brandLogo
              .attr('src', cardInfo.brandLogo)
              .attr('alt', cardInfo.brandName)
              .show()
          } else {
            $brandLogo.hide()
          }
          $instance.html(JSON.stringify(cardInfo, null, 2))
        }).trigger('keyup')


      })
    </script>
<?endif?>