<?php
namespace Sib\Core;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Diag\Debug;

Loc::loadMessages(__FILE__);

class Logger
{
    private static $traceDepth = 6;
    private static $maxFileSize = 2048; //KB

    private static $fileDebugLog = 'debug.log';
    private static $fileExchangeLog = 'exchange.log';

    static function addEventLog($msg = '')
    {

    }

    static function debugMsg($logVar = null, $fileName = '')
    {
        if(empty($fileName)){
            $fileName = self::$fileDebugLog;
        }   
        $logFile = self::getLogFile($fileName);
        
        $date = new \DateTime();
        //$backTrace = self::getBackTrace();

        $logPices = [
            $date->format('d.m.Y H:i:s'),
            print_r($logVar, true),
            //$backTrace,
            '__________________________'
        ];

        $logStr = implode("\n\n", $logPices);
        $logStr = str_replace("\n\n\n", "\n\n", $logStr);
        
        Debug::writeToFile($logStr, '', $logFile);
    }
    
    static function exchangeMsg($message = '', $type = 'debug')
    {        
        $logFile = self::getLogFile(self::$fileExchangeLog);

        $date = new \DateTime();

        $logPices = [
            "<div class='log-message log-{$type}'>",
                "<span>",
                    $message,
                "</span>",
                "<span>",
                    $date->format('d.m.Y H:i:s'),
                "</span>",
            "</div>"
        ];

        $logStr = implode("", $logPices);
        Debug::writeToFile($logStr, '', $logFile);
    }

    static function getExchangeLog()
    {
        $fileLog = self::getLogFullDir() . self::$fileExchangeLog;
        if(file_exists($fileLog)){
            return file_get_contents($fileLog);
        }
    }

    static function getDebugLog()
    {
        $fileLog = self::getLogFullDir() . self::$fileDebugLog;
        if(file_exists($fileLog)){
            return file_get_contents($fileLog);
        }
    }

    static function getLogFile($file = '')
    {
        if(empty($file)){
            $fileName = 'otherlog.log';
        } else {
            $fileName = $file;
        }
        
        $fullFileRealPath = self::getLogFullDir() . $fileName;
        $fullFilePath = self::getLogDir() . $fileName;

        if(\file_exists($fullFileRealPath)){
            $maxFileSize = 0;
            if($maxFileSize <= 0){
                $maxFileSize = self::$maxFileSize;
            }

            if(filesize($fullFileRealPath) / 1024 >= $maxFileSize){
                rename($fullFileRealPath, str_replace($fileName, $fileName . "_" . time(), $fullFileRealPath));
            }
        }
        
        return $fullFilePath;
    }

    static function getBackTrace()
    {
        $arBacktrace = \Bitrix\Main\Diag\Helper::getBackTrace(self::$traceDepth, DEBUG_BACKTRACE_IGNORE_ARGS);
        $bShowArgs = true;

        $strFunctionStack = "";
        $strFilesStack = "";
        $firstFrame = (count($arBacktrace) == 1? 0 : 2);
        $iterationsCount = min(count($arBacktrace), self::$traceDepth);
        for ($i = $firstFrame; $i < $iterationsCount; $i++)
        {
            if (strlen($strFunctionStack)>0)
                $strFunctionStack .= " < ";

            if (isset($arBacktrace[$i]["class"])){
                $strFunctionStack .= $arBacktrace[$i]["class"]."::";
            }

            $strFunctionStack .= $arBacktrace[$i]["function"];

            if(isset($arBacktrace[$i]["file"])){
                $strFilesStack .= "\t".$arBacktrace[$i]["file"].":".$arBacktrace[$i]["line"]."\n";
            }
        }

        return $strFunctionStack."\n".$strFilesStack;
    }

    static function getLogFullDir()
    {
        return \realpath(__DIR__) . '/../logs/';
    }

    static function getLogDir()
    {
        return \str_replace(\Bitrix\Main\Application::getDocumentRoot(), '', self::getLogFullDir());
    }

    static function clearLogsDir()
    {
        $files = glob(self::getLogFullDir() . "*");
        $c = count($files);
        if (count($files) > 0) {
            foreach ($files as $file) {      
                if (file_exists($file)) {
                    unlink($file);
                }   
            }
        }
    }
}