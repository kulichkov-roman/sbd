<?
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

$textArr = isset($_POST['text']) ? $_POST['text'] : [];
unset($_POST['text']);

$params = array_map('htmlspecialchars', $_POST);
if(is_array($textArr) && count($textArr) > 0){
    $params['text'] = array_map('strip_tags', $textArr);
}

use Sib\Core\BlogComments as BC;

switch($params['sort']){
    case 'RATING':
        $params['sort'] = 'RATING';
    break;
    default:
        $params['sort'] = 'NEW';
}

if(\Bitrix\Main\Loader::includeModule('sib.core') && check_bitrix_sessid()){
    switch($params['method']){
        case 'get':
            echo json_encode(BC::getComments((int)$params['elementId'], (int)$params['parentCommentId'], (int)$params['page'], $params['sort']));
        break;
        case 'add':
            echo json_encode(BC::addComment((int)$params['elementId'], (int)$params['parentCommentId'], $params['text']));
        break;
        case 'edit':
            echo json_encode(BC::editComment((int)$params['commentId'], $params['text']));
        break;
    }
    
    die();
}

echo json_encode(['ID' => 0]);