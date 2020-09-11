<?
$MESS['EMPTY_ASK'] = 'Пока нет вопросов :( Задайте свой вопрос первым!';
$MESS['ASK'] = 'Задать вопрос';
$MESS['ANS'] = 'Ответить на вопрос';
$MESS['MAIN_ASK'] = 'Самый ценный ответ';
$MESS['ASK_TEXT'] = 'Вопрос:';
$MESS['COMPANY_USER'] = '— Специалист компании';

$MESS['NEW_ANS'] = 'Новые ответы';
$MESS['SORT_BY_DATE'] = 'Сортировать по дате';
$MESS['SORT_BY_SHOWS'] = 'Сортировать по популярности'; 

for($i=0;$i<10;$i++){
    if($i == 0 || ($i >=5 && $i <= 9)){
        $MESS['COUNT_ANSWERS_' . $i] = '#COUNT# ответов';
    } else if($i >= 2 && $i <= 4){
        $MESS['COUNT_ANSWERS_' . $i] = '#COUNT# ответа';
    } else if($i == 1){
        $MESS['COUNT_ANSWERS_' . $i] = '#COUNT# ответ';
    }
}

?>