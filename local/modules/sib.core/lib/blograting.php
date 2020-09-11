<?
namespace Sib\Core;

use Bitrix\Main\Loader; 
use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;

class BlogRating
{
    private static $entityRateId = 7;

    public static function updateRate($entity = false, $id = 0, $rate = 0, $currentRate = 0)
    {
        if(!isset($_SESSION['BLOG_RATE_COUNT'])){
            $_SESSION['BLOG_RATE_COUNT'] = 0;
        } else {
            if($_SESSION['BLOG_RATE_COUNT'] > 20){
                return ['TYPE' => 'ERROR_ACTIVE_SESSION', 'MSG' => 'Многа жмем', 'NEED_DISABLED' => true];
            }
            $_SESSION['BLOG_RATE_COUNT'] += 1;
        }        

        global $USER;
        $uid = $USER->getID();

        $entity = $entity === 'comment' ? 'comment' : 'item';
        $needDisabled = false;
        $isDeleted = false;

        if($uid > 0){
            $ratingDataClass = self::getEntityDataClass(self::$entityRateId);
            $currRate = $ratingDataClass::getList([
                'filter' => [
                    'UF_ENTITY' => $entity,
                    'UF_ITEM_ID' => $id,
                    'UF_USER' => (int)$uid
                ]
            ])->fetch();

            if($currRate['ID'] > 0){
                if((int)$currRate['UF_RATE'] !== $rate){
                    //$ratingDataClass::delete($currRate['ID']);
                    $ratingDataClass::update($currRate['ID'], ['UF_RATE' => $rate]);
                } else {
                    $ratingDataClass::delete($currRate['ID']);
                    $isDeleted = true;
                }
            } else {
                $currRate = $ratingDataClass::add([
                    'UF_ENTITY' => $entity,
                    'UF_ITEM_ID' => $id,
                    'UF_USER' => $uid,
                    'UF_RATE' => $rate
                ]);
            }

            if($isDeleted){
                $ratingClass = 'neitral';
            } else {
                $ratingClass = $rate ? 'positive' : 'negative';
            }

            
            
            $rateRs = $ratingDataClass::getList([
                'select' => ['UF_RATE'],
                'filter' => [
                    'UF_ENTITY' => $entity,
                    'UF_ITEM_ID' => $id
                ]
            ]);

            $cnt = 0;
            while($ob = $rateRs->fetch()){
                $rate = (int)$ob['UF_RATE'] === 1 ? 1 : -1;
                $cnt += $rate;
            }

            if($entity === 'comment'){
                $ratingCount = BlogComments::updateRating($id, $cnt);
            } else {
                $ratingCount = Blog::updateItemRating($id, $cnt);
            }                

           

            return ['TYPE' => 'OK', 'CNT' => $cnt, 'RATING_CLASS' => $ratingClass];
        }

        return ['TYPE' => 'ERROR'];
    }

    public static function getUserRateList($checkList = [])
    {
        global $USER;
        $uid = $USER->GetId();
        $ratingDataClass = self::getEntityDataClass(self::$entityRateId);

        $result = ['item' => [], 'comment' => []];
        foreach($checkList as $entity => $items){
            $rs = $ratingDataClass::getList([
                'filter' => [
                    'UF_USER' => (int)$uid,
                    'UF_ENTITY' => $entity,
                    'UF_ITEM_ID' => $items
                ]
            ]);

            if($entity === 'item'){

                $countItems = Blog::getRatingInfo($items);
                $showCountItems = [];
                if(count($items) === 1){
                    $showCountItems = Blog::getShowCounterInfo($items);
                }
                
            }

            while($ob = $rs->fetch()){
                $result[$entity][] = ['ID' => $ob['UF_ITEM_ID'], 'RATE' => $ob['UF_RATE']];
            }
        }
        
        return ['TYPE' => 'OK', 'ITEMS' => $result, 'ITEM_COUNTS' => $countItems, 'SHOWS' => $showCountItems];
    }
   
    public static function getEntityDataClass($entityId = false)
    {
        if(!Loader::includeModule("highloadblock")) return false;

        if(!$entityId){
            $entityId = self::$entityRateId;
        }

        $hlblock = HL\HighloadBlockTable::getById($entityId)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
        return $entity->getDataClass();
    }

    public function getRatingClass($rating = 0)
    {
        $ratingClass = 'neitral';
        if($rating > 0){
            $ratingClass = 'positive';
        } else if($rating < 0) {
            $ratingClass = 'negative';
        }
        return $ratingClass;
    }
}
