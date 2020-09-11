<?if(!empty($params['URL'])):?>
    <div style="text-align:center"><a class="button" href="<?=$params['URL']?>" target="_blank">Перейти к оплате</a></div>
<?else:?>
    <div style="text-align:center">Что-то пошло не так с оплатой. Пожалуйста напишите консультанту в чат.</div>
<?endif;?>