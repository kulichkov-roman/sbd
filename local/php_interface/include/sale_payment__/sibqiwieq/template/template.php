<? //global $USER; if($USER->IsAdmin()){echo '<pre>'; print_r($params); echo '</pre>';} ?>
<?if($params['SIBQIWIEQ_IS_PAY'] === 'Y'):?>
  <div class="thanks-box__title">Ваш заказ успешно оплачен!</div>
<?else:?>
  <form action="<?=$params['FORM_ACTION']?>" name="payForm" method="POST">
    <input type="hidden" name="request" value="<?=$params['FORM_REQUEST']?>">
  </form>
  <button class="button" style="display:block;max-width:200px;margin:10px auto;" id="sendBtn">Оплатить</button>
  <script>
    $(function(){
      $('#sendBtn').on('click', function(){
        $('[name="payForm"]').trigger('submit');
      });
      $('[name="payForm"]').trigger('submit');
    }) 
  </script> 
<?endif?>