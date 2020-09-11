<?

namespace Aristov\Vregions\EventHandlers;

use Aristov\VRegions\Meta;
use Aristov\VRegions\Tools;

class OnEpilog{

    static $MODULE_ID = "aristov.vregions";

    public static function handler(){
        \CModule::IncludeModule(static::$MODULE_ID);

        return \AristovVregionsHandlersHelper::onEpilogHandler();
    }
}

?>