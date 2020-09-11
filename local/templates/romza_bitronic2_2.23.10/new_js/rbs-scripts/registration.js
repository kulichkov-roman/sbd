
function registerHandlers(){

    var sendRequestForm = function(form, $params, $errMsg){
        if($errMsg.length > 0){
            $errHtml = '';
            for(let i = 0; i < $errMsg.length; i++){
                $errHtml += '<div>' + $errMsg[i] + '</div>';
            }
            $(form).find('.info').html($errHtml);
            $(form).find('.info').show();
        } else {
            $(form).find('.info').hide();
            $errbox = $(form).find('.info');
            $.ajax({
                url: $params.url,
                type: 'json',
                data: $(form).serialize(),
                method: 'POST',
                success: function(result){
                    result = JSON.parse(result);
                    if(result.TYPE == 'ERROR'){
                        $errbox.html(result.MESSAGE);
                        $errbox.show();
                    } else if(result.TYPE == 'OK') {
                        $params.success(result, form);
                    }
                }
            });
        }
    };

    handler2($('#user_registration'));
    $('#user_registration').on('submit', function(e){
        e.preventDefault();
        $errMsg = [];
        if($(this).find('[name="pass"]').val() !== $(this).find('[name="repass"]').val()){
            $errMsg.push('Пароли не совпадают');
        }
        if($(this).find('[name="pass"]').val().length < 6){
            $errMsg.push('Длина пароля должна быть не менее 6 символов');
        }
        if(!$(this).find('[name="is_pers"]').is(':checked')){
            $errMsg.push('Необходимо согласиться с условиями обработки персональных данных');
        }
        sendRequestForm(this, {
            url: '/ajax/sib/registration_sib.php',
            success: function(){
                document.location.reload(true);
            }
        }, $errMsg);
    });

    $('#forgot_pass_ajax').on('submit', function(e){
        e.preventDefault();
        $errMsg = [];
        if(!isEmail($(this).find('[name="forgot_email"]').val())){
            $errMsg.push('Введите корректный email');
        }
        sendRequestForm(this, {
            url: '/ajax/sib/forgot_pass.php',
            success: function(result, form){
                $strResult = '<div class="info" style="display:block">';
                    $strResult += result.MESSAGE;
                $strResult += '</div>';
                $strResult += '<div>';
                    $strResult +='<button data-fancybox-close class="login-form__button button">Закрыть</button>'
                $strResult += '</div>';

                $(form).siblings('.info-text').hide();
                $(form).html($strResult);
            }
        }, $errMsg);
    });
}
if (typeof window.frameCacheVars !== "undefined"){BX.addCustomEvent("onFrameDataReceived", function (json){registerHandlers();});} else {$(document).ready(function(){registerHandlers();});}
