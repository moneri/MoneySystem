<?php

/*
* __  __       _                             __    ___    ___   _______
*|  \/  | ___ | |_  ___   _    _  ____  _   |  |  / _ \  / _ \ |___   /
*| |\/| |/ _ \| __|/ _ \ | |  | |/  _ \/ /  |  | |_// / |_// /    /  /
*| |  | |  __/| |_| (_) || |__| || (_)   |  |  |   / /_   / /_   /  /
*|_|  |_|\___| \__|\___/ |__/\__||____/\_\  |__|  /____| /____| /__/
*
*All this program is made by hand of metowa 1227.
*I certify here that all authorities are in metowa 1227.
*Expiration date of certification: unlimited
*Secondary distribution etc are prohibited.
*The update is also done by the developer.
*This plugin is a developer API plugin to make it easier to write code.
*When using this plug-in, be sure to specify it somewhere.
*Warning if violation is confirmed.
*
*Developer: metowa 1227
*Development Team: metowa 1227 Plugin Development Team (Members: metowa 1227 only)
*/

namespace metowa1227\MoneySystemAPI;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\OfflinePlayer;
use pocketmine\utils\TextFormat;

use metowa1227\MoneySystemAPI\event\MoneyAddedEvent;
use metowa1227\MoneySystemAPI\event\MoneyTakedEvent;
use metowa1227\MoneySystemAPI\event\MoneySettedEvent;
use metowa1227\MoneySystemAPI\event\MoneyChangedEvent;
use metowa1227\MoneySystemAPI\Delete;

class MoneySystemAPI extends PluginBase implements Listener{

    /**
    **@author metowa1227
    **/

    /*  getMonitorUnit  */
    /**
    * @var array
    */
    /*  THIS_PLUGIN_NAME  */
    /**
    * @var MoneySystemAPI
    */

    const PLUGIN_NAME = "MoneySystemAPI";

    /*  ECONOMY_TYPE  */
    /**
    * @var Economy type
    */

    const ECONOMY_NAME = "MoneySystem";

    /*  PLUGIN_VERSION  */
    /**
    * @var Plugin version
    */

    const PLUGIN_VERSION = "11";

    const PACKAGE_VERSION = "10.5.0 API";

    const CODENAME = "GREEN PAPAYA (Green papaya / beta edition / poker face {build::9.0.1,productid::10.92.2163.41367}";

    const API_VERSION = "8.0";

    /**
    * @var ahthor metowa1227
    */

    private $author = "metowa1227";

    const PLUGIN_AUTHOR = "metowa1227";

    const PLUGIN_REGISTED_DATE = "2016/09/03";

    const PLUGIN_LAST_UPDATE = "2017/09/30";

    private static $instance = null;

    /* Segmantation fault */

    /**
    * @var Load plugin database files.
    */

    /**
    * @var Load config
    */

    private $loadconfig = false;

    /**
    * @var Load datafile.
    */

    private $loaddata = false;

    /**
    * @var Load database file.
    */

    private $loaddatabase = false;

    private $langlist = 
    [
        "japanese" => "日本語",
        "english" => "English",
        "chinese" => "简体中文",
        "korean" => "한국어"
    ];

    /**
    **@param \PluginBase
    **@param \MoneySystem enable event
    **/

    /**
    **@var Configlation
    **/

    public $lang, $langdata, $player, $Money, $config = null;

    /**
    **@return bool
    **/

    private $enable = null;

    /** Auto update checker **/

	public function onEnable(){
        $this->getLogger()->notice(self::PLUGIN_NAME." is Loading... please wait...");
        if(!file_exists($this->getDataFolder())){
            @mkdir($this->getDataFolder(), 0777, true); 
        }
        $c = $this->LoadConfig();
        $this->saveDefaultConfig();
        $this->saveResource("Config.yml", false);
        $this->saveResource("jpn.yml", false);
        $this->saveResource("eng.yml", false);
        $this->saveResource("chi.yml", false);
        $this->saveResource("kor.yml", false);
        if(!isset($c)){
            $this->getServer()->getPluginManager()->disablePlugin($this);
            $this->getLogger()->warning("[MoneySystem ERROR] システムファイルの読み取りに失敗しました。　ファイルを確認してください。 ERROR-CODE 1001-cdf");
        }
        $this->lang[0] = new Config($this->getDataFolder() . "jpn.yml", Config::YAML);
        $this->lang[1] = new Config($this->getDataFolder() . "eng.yml", Config::YAML);
        $this->lang[2] = new Config($this->getDataFolder() . "chi.yml", Config::YAML);
        $this->lang[3] = new Config($this->getDataFolder() . "kor.yml", Config::YAML);
        $this->langdata = new Config($this->getDataFolder() . "PlayersLang.yml", Config::YAML);
        $this->player = new Config($this->getDataFolder() . "PlayerPaing.yml", Config::YAML);
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        /*if(!$this->config->exists("datasave")){
            $this->config->set("ConfigVersion", 10.0);
            $this->config->set("datasave", false);
            $this->config->save();
            $this->getLogger()->notice("Configファイルを自動的に更新しました。 内容をご確認ください。");
        }*/
        if($this->config->get("DataBaseType") == "YAMLFILE")
            $this->Money = new Config($this->getDataFolder() . "Money.yml", Config::YAML);
        elseif($this->config->get("DataBaseType") == "SQLITEFILE")
            if(!file_exists($this->getDataFolder() . "Money.db"))
                $this->db = new \SQLite3($this->getDataFolder()."Money.db", SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
            else
                $this->db = new \SQLite3($this->getDataFolder()."Money.db", SQLITE3_OPEN_READWRITE);
        $this->dt = $this->config->get("DataBaseType");
        if(!$this->config->exists("ConfigVersion")){
            $this->config->set("ConfigVersion", 10.0);
            $this->config->set("datasave", false);
            $this->config->save();
            $this->getLogger()->warning("Configのバージョンが古いです。Configファイルは自動的に、内容は変更されないまま更新されました。");
        }
        if($this->config->exists("AutoBackup")){
            $configresult = $this->config->get("AutoBackup");
        }else{
            $this->getLogger()->error(TextFormat::RED."リカバリ不能な重大なエラーが発生。 ERROR-CODE cfnfd-0001");
            $this->getServer()->shutdown();
            return true;
        }
        if($configresult){
            $result = $this->BackupFiles();
            if($result){
                $this->getLogger()->info(TextFormat::GREEN."[Sucsess] Backup compleate.");
            }
        }else{
            if($this->config->get("AutoBackup") !== false){
                $this->config->set("AutoBackup", false);
                $this->config->save();
            }
        }
        if(!$this->langdata->exists("CONSOLE")){
            $this->langdata->set("CONSOLE", "english");
            $this->langdata->save();
        }
        $this->unit = $this->config->get("MonitorUnit");
        $d = $this->LoadData();
        if(!isset($d)){
            $this->getServer()->getPluginManager()->disablePlugin($this);
            $this->getLogger()->warning("[MoneySystem ERROR] プラグインのデータの読み取りに失敗しました。　データファイルを確認してください。 ERROR-CODE 1002-cdf");
        }
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if($this->dt !== "YAMLFILE" && $this->dt !== "SQLITEFILE"){
            $this->config->set("DataBaseType", "YAMLFILE");
            $this->config->save();
        }
        if($this->dt == "YAMLFILE"){
            $this->Money->set("CONSOLE", 999999999999);
            $this->Money->save();
        }elseif($this->dt == "SQLITEFILE"){
            $this->DB("CREATE TABLE IF NOT EXISTS player (name TEXT PRIMARY KEY, money INT)");
            $this->DB("INSERT OR REPLACE INTO player VALUES (\"CONSOLE\", 999999999999)");
        }
        $f = $this->LoadDataBase();
        if(!isset($f)){
            $this->getServer()->getPluginManager()->disablePlugin($this);
            $this->getLogger()->warning("[MoneySystem ERROR] プラグインのデータの読み取りに失敗しました。　データファイルを確認してください。 ERROR-CODE 1003-cdf");
        }
        /*if(!define('DELETESYSTEMPATH', Server::getInstance()->getDataPath()."MoneySystemBackupFiles"))
            exit(1);
        if(!is_dir(self::DELETESYSTEMPATH))
            exit(1);
        $safe = $this->config->get('savedata');
        $this->save = false;
        if($safe){
            $this->save = true;
        }
        if(!define('DSYSTEM', Server::getInstance()->getDataFolder()."Delete.php"))
            exit(1);
        if(!is_dir(self::DELETESYSTEMPATH))
            exit(1);*/
        $this->getLogger()->info("Checking update... New version MoneySystem exists checking...");
        $this->AutoUpdateChecker();
        $this->GetUpdateDescription();
        $this->getLogger()->notice("§a[Sucsess] MoneySystemを起動しました。 /moneysystem でhelpを確認できます。");
        $this->getLogger()->debug(self::CODENAME." | enabled.");
        $this->getLogger()->notice("Plugin info: ver.".self::PLUGIN_VERSION." author.".self::PLUGIN_AUTHOR." PACK.".self::PACKAGE_VERSION." date.".self::PLUGIN_REGISTED_DATE." API.".self::API_VERSION." DATABASE.".$this->dt." LASTUPDATE.".self::PLUGIN_LAST_UPDATE);
        /*if($safe){
            $safe = "有効";
        }else{
            $safe = "無効";
        }*/
        $this->getLogger()->info("データ保護は"./*.$safe.*/"有効です。");
        $this->enable = (boolean) true;
    }

/**
    **@internal \GetPlayerDefaultLanguage
**/

    public function getDefaultLang(string $name){
        if(!$this->ExistsAccountForName($name)){
            return false;
        }
        if(!$this->langdata->exists($name)){
          return false;
        }
        $lang = $this->langdata->get($name);
        return $lang;
    }

    public function getMessage(string $name, $message){
        if($this->getDefaultLang($name) == false){
            return false;
        }

        $lang = $this->getDefaultLang($name);
        switch($lang){
            case "japanese":
                if(!$this->lang[0]->exists($message)){
                    return false;
                }
                return $this->lang[0]->get($message);
                break;
            case "english":
                if(!$this->lang[1]->exists($message)){
                    return false;
                }
                return $this->lang[1]->get($message);
                break;
            case "chinese":
                if(!$this->lang[2]->exists($message)){
                    return false;
                }
                return $this->lang[2]->get($message);
                break;
            case "korean":
                if(!$this->lang[3]->exists($message)){
                    return false;
                }
                return $this->lang[3]->get($message);
                break;
            default:
                return null;
            }
        }

    public function ExistsLang($lang) : bool{
        switch($lang){
            case "jpn":
            case "japanese":
                return true;
                break;
            case "eng":
            case "english":
                return true;
                break;
            case "chi":
            case "chinese":
                return true;
                break;
            case "kor":
            case "korean":
                return true;
                break;
            default:
                return false;
        }
    }

    public function setLang(string $name, $lang) : bool{
        if(!$this->langdata->exists($name)){
            return false;
        }
        switch($lang){
            case "jpn":
            case "japanese":
                $this->langdata->set($name, "japanese");
                $this->langdata->save();
                return true;
                break;
            case "eng":
            case "english":
                $this->langdata->set($name, "english");
                $this->langdata->save();
                return true;
                break;
            case "chi":
            case "chinese":
                $this->langdata->set($name, "chinese");
                $this->langdata->save();
                return true;
                break;
            case "kor":
            case "korean":
                $this->langdata->set($name, "korean");
                $this->langdata->save();
                return true;
                break;
            default:
                return false;
        }
    }

/*

    private function getLang(string $name, $message){
        if($this->getDefaultLang($name) == false){
            return false;
        }
        $langtype = $this->getDefaultLang($name);
        switch($langtype){
            case "japanese":
                $lang = "jpn":
                break;
            case "english":
                $lang = "eng":
                break;
            case "chinese":
                $lang = "chi":
                break;
            case "korean":
                $lang = "kor":
                break;
            default:
                $lang = "default":
                break;
        }
    }

*/

/**
    **@param \PlayerJoinEvent
    **@param \Setplayer default money for new player
    **@var Add money
**/

    public static function getInstance(){
        return self::$instance;
    }

    public function onLoad(){
        self::$instance = $this;
    }

    public function onJoin(PlayerJoinEvent $event){
        $prefix = TextFormat::AQUA."[MoneySystem]".TextFormat::WHITE;
        $player = $event->getPlayer();
        $name = $player->getName();
        if(!$this->langdata->exists($name)){
            $this->langdata->set($name, "japanese");
            $this->langdata->save();
        }
        if($this->player->exists($name)){
            $paydata = $this->player->get($name);
            $price = $paydata["price"];
            $issuer = $paydata["player"];
            $nowdata = $this->Money->get($name);
            $this->AddMoney($player, $price);
            $player->sendMessage($prefix.str_replace(array("%GIVEN%", "%MONITORUNIT%", "%AMOUNT%"), array($payner, $this->unit, $price)), $this->getMessage($name, "join.pay"));
            $this->player->remove($name);
            $this->player->save();
            return true;
        }
        if(!$this->ExistsAccountForName($name)){
            $this->CreateAccount($name, $this->getDefaultMoney());
            $player->sendMessage($prefix.str_replace(array("%MONITORUNIT%", "%AMOUNT%"), array($this->unit, $this->getDefaultMoney()), $this->getMessage($name, "join.new.player")));
        }
    }

/**
    **@var Add money for target player
    **@internal Max money have player is set money '999999999999'
    **@return false

**/

/**

     public function setCancel(string $event, string $name, $type = true){
        if($event == "MoneyAddedEvent"){
            if($type == true){
                $this->isCancelled["MoneyAddedEvent"][$name] = true;
            }elseif($type == false){
                $this->isCancelled["MoneyAddedEvent"][$name] = false;
            }
        }elseif($event == "MoneyTakedEvent"){
            if($type == true){
                $this->isCancelled["MoneyTakedEvent"][$name] = true;
            }elseif($type == false){
                $this->isCancelled["MoneyTakedEvent"][$name] = false;
            }  
        }elseif($event == "MoneySettedEvent"){
            if($type == true){
                $this->isCancelled["MoneySettedEvent"][$name] = true;
            }elseif($type == false){
                $this->isCancelled["MoneySettedEvent"][$name] = false;
            }
        }else{
            return false;
        }
    }

    public function isCancel(string $event, string $name) : bool{
        return $this->isCancelled[$event][$name];
    }

**/

    public function AddMoney(Player $player, $amount, $type = "other.plugin", $issuer = "undefined issuer") : bool{
        $name = $player->getName();
        if($this->ExistsAccount($player)){
            if(!is_numeric($amount)){
                return false;
            }else{
                if(!is_int($amount) || !is_float($amount) || is_string($amount) && is_int($amount) || is_string($amount) && is_float($amount)){
                    $amount = intval($amount);
                }
            }
            $this->getServer()->getPluginManager()->callEvent(new MoneyAddedEvent($this, $player, $amount, $type, $issuer));
            $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, $name, $amount, $type, $issuer));
/**
            if($this->isCancelled["MoneyAddedEvent"][$name] == true){
                return true;
            }elseif($this->isCancelled["MoneyAddedEvent"][$name] == false){
**/
            $nowmoney = $this->CheckForName($name);
            $added = $nowmoney + $amount;
            if($this->dt == "YAMLFILE"){
                $this->Money->set($name, $added);
                $this->Money->save();
            }elseif($this->dt == "SQLITEFILE"){
                $this->DB("UPDATE player SET money = $added WHERE name=\"$name\"");
            }
            if($this->dt == "YAMLFILE"){
                if($this->Money->get($name) > 999999999999){
                    $this->Money->set($name, 999999999999);
                    $this->Money->save();
                }
            }elseif($this->dt == "SQLITEFILE"){
                $now = $this->CheckForName($name);
                if($now > 999999999999){
                    $this->DB("UPDATE player SET money = 999999999999 WHERE name=\"$name\"");
                }
            }
            $result = $this->MissionCleardChecker();
            if($result){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
//          }
        }
    }

    public function AddMoneyForName(string $name, $amount, $type = "other.plugin", $issuer = "undefined issuer") : bool{
        if($this->ExistsAccountForName($name)){
            if(!is_numeric($amount)){
                return false;
            }else{
                if(!is_int($amount) || !is_float($amount) || is_string($amount) && is_int($amount) || is_string($amount) && is_float($amount)){
                    $amount = intval($amount);
                }
            }
            $player = $this->getServer()->getPlayer($name);
            if(!$player){
                $player = $this->getServer()->getOfflinePlayer($name);
            }
            $this->getServer()->getPluginManager()->callEvent(new MoneyAddedEvent($this, $player, $amount, $type, $issuer));
            $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, $name, $amount, $type, $issuer));
/**
            if($this->isCancelled["MoneyAddedEvent"][$name] == true){
                return true;
            }elseif($this->isCancelled["MoneyAddedEvent"][$name] == false){
**/
            $nowmoney = $this->CheckForName($name);
            $added = $nowmoney + $amount;
            if($this->dt == "YAMLFILE"){
                $this->Money->set($name, $added);
                $this->Money->save();
            }elseif($this->dt == "SQLITEFILE"){
                $this->DB("UPDATE player SET money = $added WHERE name=\"$name\"");
            }
            if($this->dt == "YAMLFILE"){
                if($this->Money->get($name) > 999999999999){
                    $this->Money->set($name, 999999999999);
                    $this->Money->save();
                }
            }elseif($this->dt == "SQLITEFILE"){
                $now = $this->CheckForName($name);
                if($now > 999999999999){
                    $this->DB("UPDATE player SET money = 999999999999 WHERE name=\"$name\"");
                }
            }
            $result = $this->MissionCleardChecker();
            if($result){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
//          }
        }
    }

/**
    **@var minus money for target player
    **@internal min money have player is set money '0'
    **@return false
**/

    public function TakeMoney(Player $player, $amount, $type = "other.plugin", $issuer = "undefined issuer") : bool{
        $user = $player->getName();
        if($this->ExistsAccount($player)){
            if(!is_numeric($amount)){
                return false;
            }else{
                if(!is_int($amount) || !is_float($amount) || is_string($amount) && is_int($amount) || is_string($amount) && is_float($amount)){
                    $amount = intval($amount);
                }
            }
            $this->getServer()->getPluginManager()->callEvent(new MoneyTakedEvent($this, $player, $amount, $type, $issuer));
            $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, $user, $amount, $type, $issuer));
/**
            if($this->isCancelled["MoneyTakedEvent"][$user] == true){
                return true;
            }elseif($this->isCancelled["MoneyTakedEvent"][$user] == false){
**/
            $nowmoney = $this->CheckForName($user);
            $taked = $nowmoney - $amount;
            if($this->dt == "YAMLFILE"){
                $this->Money->set($user, $taked);
                $this->Money->save();
            }elseif($this->dt == "SQLITEFILE"){
                $this->DB("UPDATE player SET money = $taked WHERE name=\"$user\"");
            }
            if($this->dt == "YAMLFILE"){
                if($this->Money->get($user) > 999999999999){
                    $this->Money->set($user, 999999999999);
                    $this->Money->save();
                }
                if($this->Money->get($user) < 0){
                    $this->Money->set($user, 0);
                    $this->Money->save();
                }
            }elseif($this->dt == "SQLITEFILE"){
                $now = $this->CheckForName($user);
                if($now > 999999999999){
                    $this->DB("UPDATE player SET money = 999999999999 WHERE name=\"$user\"");
                }
                $now = $this->CheckForName($user);
                if($now < 0){
                    $this->DB("UPDATE player SET money = 0 WHERE name=\"$user\"");
                }
            }
            $result = $this->MissionCleardChecker();
            if($result){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
//          }
        }
    }

    public function TakeMoneyForName(string $user, $amount, $type = "other.plugin", $issuer = "undefined issuer") : bool{
        if($this->ExistsAccountForName($user)){
            if(!is_numeric($amount)){
                return false;
            }else{
                if(!is_int($amount) || !is_float($amount) || is_string($amount) && is_int($amount) || is_string($amount) && is_float($amount)){
                    $amount = intval($amount);
                }
            }
            $player = $this->getServer()->getPlayer($user);
            if(!$player){
                $player = $this->getServer()->getOfflinePlayer($user);
            }
            $this->getServer()->getPluginManager()->callEvent(new MoneyTakedEvent($this, $player, $amount, $type, $issuer));
            $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, $user, $amount, $type, $issuer));
/**
            if($this->isCancelled["MoneyTakedEvent"][$user] == true){
                return true;
            }elseif($this->isCancelled["MoneyTakedEvent"][$user] == false){
**/
            $nowmoney = $this->CheckForName($user);
            $taked = $nowmoney - $amount;
            if($this->dt == "YAMLFILE"){
                $this->Money->set($user, $taked);
                $this->Money->save();
            }elseif($this->dt == "SQLITEFILE"){
                $this->DB("UPDATE player SET money = $taked WHERE name=\"$user\"");
            }
            if($this->dt == "YAMLFILE"){
                if($this->Money->get($user) > 999999999999){
                    $this->Money->set($user, 999999999999);
                    $this->Money->save();
                }
                if($this->Money->get($user) < 0){
                    $this->Money->set($user, 0);
                    $this->Money->save();
                }
            }elseif($this->dt == "SQLITEFILE"){
                $now = $this->CheckForName($user);
                if($now > 999999999999){
                    $this->DB("UPDATE player SET money = 999999999999 WHERE name=\"$user\"");
                }
                $now = $this->CheckForName($user);
                if($now < 0){
                    $this->DB("UPDATE player SET money = 0 WHERE name=\"$user\"");
                }
            }
            $result = $this->MissionCleardChecker();
            if($result){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
//          }
        }
    }

/**
    **@return int
**/

    public function Check($player){
        if($player instanceof Player or $player instanceof OfflinePlayer){
            $name = $player->getName();
            if($this->dt == "YAMLFILE"){
                if(!$this->Money->exists($name)){
                    return false;
                }
                return (int) $this->Money->get($name);
            }elseif($this->dt == "SQLITEFILE"){
                $result = $this->DB("SELECT money FROM player WHERE name=\"$name\"", true);
                $result = $result[0];
                if(empty($result))
                    return false;
                return $result;
            }
        }
    }

    public function CheckForName(string $name){
        if($this->dt == "YAMLFILE"){
            if(!$this->Money->exists($name)){
                return false;
            }
            return (int) $this->Money->get($name);
        }elseif($this->dt == "SQLITEFILE"){
            $result = $this->DB("SELECT money FROM player WHERE name=\"$name\"", true);
            $result = $result[0];
            if(empty($result))
                return false;
            return $result;
        }
    }

/**
    **@var Set money for target player
    **@internal Max money have player is set money '999999999999'
    **@internal Min money have player is set money '0'
    **@return false
**/

    public function SetMoney(Player $player, $money, $type = "other.plugin", $issuer = "undefined issuer") : bool{
        $name = $player->getName();
        if($this->ExistsAccount($player)){
            if(!is_numeric($money)){
                return false;
            }else{
                if(!is_int($money) || !is_float($money) || is_string($money) && is_int($money) || is_string($money) && is_float($money)){
                    $money = intval($money);
                }
            }
            $this->getServer()->getPluginManager()->callEvent(new MoneySettedEvent($this, $player, $money, $type, $issuer));
            $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, $name, $money, $type, $issuer));
/**
            if($this->isCancelled["MoneySettedEvent"][$name] == true){
                return true;
            }elseif($this->isCancelled["MoneySettedEvent"][$name] == false){
**/
            if($this->dt == "YAMLFILE"){
                $this->Money->set($name, $money);
                $this->Money->save();
                if($this->Money->get($name) > 999999999999){
                    $this->Money->set($name, 999999999999);
                    $this->Money->save();
                }
            }elseif($this->dt == "SQLITEFILE"){
                $this->DB("UPDATE player SET money = $money WHERE name=\"$name\"");
                $now = $this->CheckForName($name);
                if($now > 999999999999){
                    $this->DB("UPDATE player SET money = 999999999999 WHERE name=\"$name\"");
                }
                $now = $this->CheckForName($name);
                if($now < 0){
                    $this->DB("UPDATE player SET money = 0 WHERE name=\"$name\"");
                }
            }
            $result = $this->MissionCleardChecker();
            if($result){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
//          }
        }
    }

    public function SetMoneyForName(string $name, $money, $type = "other.plugin", $issuer = "undefined issuer") : bool{
        if($this->ExistsAccountForName($name)){
            if(!is_numeric($money)){
                return false;
            }else{
                if(!is_int($money) || !is_float($money) || is_string($money) && is_int($money) || is_string($money) && is_float($money)){
                    $money = intval($money);
                }
            }
            $player = $this->getServer()->getPlayer($name);
            if(!$player){
                $player = $this->getServer()->getOfflinePlayer($name);
            }
            $this->getServer()->getPluginManager()->callEvent(new MoneySettedEvent($this, $player, $money, $type, $issuer));
            $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, $name, $money, $type, $issuer));
/**
            if($this->isCancelled["MoneySettedEvent"][$name] == true){
                return true;
            }elseif($this->isCancelled["MoneySettedEvent"][$name] == false){
**/
            if($this->dt == "YAMLFILE"){
                $this->Money->set($name, $money);
                $this->Money->save();
                if($this->Money->get($name) > 999999999999){
                    $this->Money->set($name, 999999999999);
                    $this->Money->save();
                }
            }elseif($this->dt == "SQLITEFILE"){
                $this->DB("UPDATE player SET money = $money WHERE name=\"$name\"");
                $now = $this->CheckForName($name);
                if($now > 999999999999){
                    $this->DB("UPDATE player SET money = 999999999999 WHERE name=\"$name\"");
                }
                $now = $this->CheckForName($name);
                if($now < 0){
                    $this->DB("UPDATE player SET money = 0 WHERE name=\"$name\"");
                }
            }
            $result = $this->MissionCleardChecker();
            if($result){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
//          }
        }
    }

/**
    **@var power off MoneySystem event
    **@internal offset all players support engine
    **@return bool
**/

    public function onDisable(){
        if($this->enable == true){
            if($this->dt == "YAMLFILE")
                $this->Money->save();
            elseif($this->dt == "SQLITEFILE")
                $this->db->close();
            $this->player->save();
            $this->enable = (boolean) false;
            $this->getLogger()->info(TextFormat::GREEN."MoneySystemをシャットダウンしました。");
        }else{
            return;
        }
    }

/**
    **@var create new account for player
    **@var we need playername for create account
    **@link MoneySystem using database file 'Money.yml'
**/

    public function CreateAccount(string $name, $money) : bool{
        if(!isset($money)){
            $money = 1000;
        }
        if(!is_numeric($money)){
            $money = 1000;
        }
        if($money > 999999999999){
            $money = 999999999999;
        }
        if($money < 0){
            $money = 0;
        }
        if($this->dt == "YAMLFILE"){
            $this->Money->set($name, $money);
            $this->Money->save();
        }elseif($this->dt == "SQLITEFILE"){
            $this->DB("INSERT OR REPLACE INTO player VALUES (\"$name\", \"$money\")");
        }
        $result = $this->MissionCleardChecker();
        if($result){
            return true;
        }else{
            return false;
        }
    }

/**
    **@var remove account for player
    **@var we need playername for remove account
    **@link MoneySystem using database file 'Money.yml'
**/

    public function RemoveAccount(string $name) : bool{
        if($this->dt == "YAMLFILE"){
            if(!$this->Money->exists($name)){
                return false;
            }
            $this->Money->remove($name);
            $this->Money->save();
        }elseif($this->dt == "SQLITEFILE"){
            $result = $this->DB("SELECT * FROM player WHERE name=\"$name\"", true);
            if(empty($result))
                return false;
            $this->DB("DELETE FROM player WHERE name=\"$name\"");
        }
        return true;
    }

/**
    **@var reset all players money for default money
    **@internal only calling for admin permission have player
    **@return bool
**/

    public function setPlayerDefaultMoney(Player $player) : bool{
        $name = $player->getName();
        if($this->dt == "YAMLFILE"){
            if(!$this->Money->exists($name)){
                return false;
            }else{
                $default = $this->config->get("DefaultMoney");
                $this->Money->set($name, $default);
                $this->Money->save();
            }
        }elseif($this->dt == "SQLITEFILE"){
            $default = $this->config->get("DefaultMoney");
            $this->DB("INSERT OR REPLACE INTO player VALUES(\"$name\", \"$default\"");
        }
        $default = $this->config->get("DefaultMoney");
        $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, $name, $default, "set.player.default", "other.plugin"));
        return true;
    }

     public function setPlayerDefaultMoneyForName(string $name) : bool{
        if($this->dt == "YAMLFILE"){
            if(!$this->Money->exists($name)){
                return false;
            }else{
                $default = $this->config->get("DefaultMoney");
                $this->Money->set($name, $default);
                $this->Money->save();
            }
        }elseif($this->dt == "SQLITEFILE"){
            $default = $this->config->get("DefaultMoney");
            $this->DB("INSERT OR REPLACE INTO player VALUES(\"$name\", \"$default\"");
        }
        $default = $this->config->get("DefaultMoney");
        $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, $name, $default, "set.player.default", "other.plugin"));
        return true;
    }

/**
    **@var we can setting target player's money
**/

    public function getDefaultMoney() : int{
        return $this->config->get("DefaultMoney");
    }

/**
    **@var we can setting terget player's configlation file
**/

    public function setAllDefaultMoney() : bool{
        if($this->dt == "YAMLFILE"){
            foreach($this->Money->getAll(true) as $all){
                $this->Money->set($all, $this->config->get("DefaultMoney"));
                $this->Money->save();
                $default = $this->config->get("DefaultMoney");
                $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, "allplayers", $default, "set.allplayer.default", "other.plugin"));
                return true;
            }
        }elseif($this->dt == "SQLITEFILE"){
            foreach($this->DB("SELECT * FROM player", true) as $all){
                $name = $all->getName();
                $default = $this->config->get("DefaultMoney");
                $this->DB("INSERT OR REPLACE INTO player VALUES(\"$name\", \"$default\"");
                $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, "allplayers", $default, "set.allplayer.default", "other.plugin"));
                return true;
            }
        }
        return false;
    }

/**
    **@var your need please given money informational is int or boolean
    **@internal this called block is int
    **@internal string or boolean or array is expension error debug strage
    **@throws \RuntimeException if not int given
**/

    public function setDefaultMoney($money) : bool{
        if(!is_numeric($money)){
            $money = 1000;
        }
        $this->config->set("DefaultMoney", $money);
        $this->config->save();
        $result = $this->MissionCleardChecker();
        if($result){
            return true;
        }else{
            return false;
        }
    }

/**
    **@var mission clear checker
**/

    private function MissionCleardChecker() : bool{
        return true;
    }

    public function saveData() : bool{
        if($this->enable == true){
            if($this->dt == "YAMLFILE")
                $this->Money->save();
            elseif($this->dt == "SQLITEFILE")
                $this->db->close();
            $this->player->save();
            return true && $this->enable === true;
        }else{
            return false;
        }
    }

    public function WhichOnlineAccessingDatabaseType() : string{
        return $this->dt;
    }

/**
    **@return bool
**/

    public function ExistsAccount(Player $player) : bool{
        $name = $player->getName();
        if($this->dt == "YAMLFILE"){
            if($this->Money->exists($name)){
                return true;
            }else{
                return false;
            }
        }elseif($this->dt == "SQLITEFILE"){
            $result = $this->DB("SELECT * FROM player WHERE name=\"$name\"", true);
            if(empty($result))
                return false;
            else
                return true;
        }
    }

    public function ExistsAccountForName(string $name) : bool{
        if($this->dt == "YAMLFILE"){
            if($this->Money->exists($name)){
                return true;
            }else{
                return false;
            }
        }elseif($this->dt == "SQLITEFILE"){
            $result = $this->DB("SELECT * FROM player WHERE name=\"$name\"", true);
            if(empty($result))
                return false;
            else
                return true;
        }
    }

/**
    **@var your need please given money informational is int or boolean
    **@internal this called block is int
    **@internal string or boolean or array is expension error debug strage
    **@throws \RuntimeException if not int given
**/

    public function setAllCustomMoney($money) : bool{
        if(!is_numeric($money)){
            $money = 1000;
        }
        if($this->dt == "YAMLFILE"){
            foreach($this->Money->getAll(true) as $all){
                $this->Money->set($all, $money);
                $this->Money->save();
                $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, "allplayers", $money, "set.allplayer.custom", "other.plugin"));
                return true;
            }
        }elseif($this->dt == "SQLITEFILE"){
            foreach($this->DB("SELECT * FROM player", true) as $all){
                $name = $all->getName();
                $this->DB("INSERT OR REPLACE INTO player VALUES(\"$name\", \"$money\"");
                $this->getServer()->getPluginManager()->callEvent(new MoneyChangedEvent($this, "allplayers", $money, "set.allplayer.custom", "other.plugin"));
                return true;
            }
        }
        return false;
    }

/**
    **@internal this plugin have only unit
    **@var MoneySystem using mark is '$'
    **@return MonitorUnit '$'
**/

    public function getMonitorUnit(){
        return $this->unit;
    }

/**
    **@internal only expencive return boolean true or false
    **@return true or false
    **@var mixed true or false is not integer boolean for only mark boolean
**/

    public function isEnable() : bool{
        return (boolean) $this->enable;
    }

/**
    **@return array
**/

    public function getAllMoneyData(){
        if($this->dt == "YAMLFILE"){
            return $this->Money->getAll();
        }elseif($this->dt == "SQLITEFILE"){
            return $this->DB("SELECT * FROM player", true);
        }
    }

/**
    **@internal backedup this plugin database files
    **@internal all files backupping
    **@return bool
    **@throws \RuntimeException is backup failed only
**/

    public function BackupFiles() : bool{
        $this->failedbk = false;
        $dir = $this->getDataFolder();
        if(!is_dir($dir)){return false;}
        if(!is_dir($this->getServer()->getDataPath()."MoneySystemBackupFiles")){
            @mkdir($this->getServer()->getDataPath()."MoneySystemBackupFiles");
        }
        @mkdir($this->getServer()->getDataPath()."MoneySystemBackupFiles/".date("D_M_j-H.i.s-T_Y", time()));
        $path = $this->getServer()->getDataPath()."MoneySystemBackupFiles/".date("D_M_j-H.i.s-T_Y", time());
        $file[0] = $path."\\PlayerPaing.yml";
        $file[1] = $path."\\Money.yml";
        $file[2] = $path."\\Config.yml";
        try{
            if(!copy($dir."PlayerPaing.yml", $file[0])){
                throw new \Exception("Backedup failed. >> backedup failed resource is PlayerPaing.yml", 1);
            }
            if(!copy($dir."Money.yml", $file[1])){
                throw new \Exception("Backedup failed. >> backedup failed resource is Money.yml", 1);
            }
            if(!copy($dir."Config.yml", $file[2])){
                throw new \Exception("Backedup failed. >> backedup failed resource is Config.yml", 1);
            }
        }catch(\Exception $error){
            $this->getLogger()->error(TextFormat::RED."Backup was failed. do not compleate mission of BackupFiles() func.");
            $this->failedbk = true;
        }finally{
            if($this->failedbk){return false; $this->getServer()->shutdown();}
                $this->getLogger()->info(TextFormat::GREEN."[Sucsess] Sucsess to backup files of MoneySystem data.");
            }
            return true;
        }

/**
    **@return checking all files of boolean
**/

    public function files_exists(){
        $dir = $this->getDataFolder();
        if(!file_exists($dir."PlayerPaing.yml")){
            return "PlayerPaing.yml was not found";
        }
        if(!file_exists($dir."Money.yml")){
            return "Money.yml was not found";
        }
        if(!file_exists($dir."Config.yml")){
            return "Config.yml was not found";
        }
        return null;
    }

/**
    **@return checking backedup files of boolean
**/

    public function BackedupFiles(){
        $dir = $this->getDataPath()."MoneySystemBackupFiles\\";
        if(!file_exists($dir."PlayerPaing.yml")){
            return "PlayerPaing.yml was not found";
        }
        if(!file_exists($dir."Money.yml")){
            return "Money.yml was not found";
        }
        if(!file_exists($dir."Config.yml")){
            return "Config.yml was not found";
        }
        return null;
    }

/**
    **@var MoneySystem have default commands list
**/

    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        $name = $sender->getName();
        if(!$sender instanceof Player){
            $name = "CONSOLE";
        }

        switch(strtolower($command->getName())){
            case "removesystem":
                if(!$sender->isOp()){
                    $sender->sendMessage(TextFormat::RED.$this->getMessage("not.permission"));
                    return true;
                }
                $this->getLogger()->notice("今までMoneySystemをご利用頂きまして、誠にありがとうございました。今後ともよろしくお願いいたします。それではごきげんよう... Powered by metowa1227");
                Delete::getInstance()->systemEND();
                return true;
                break;

            case "moneysystem":
                if($name !== "CONSOLE"){
                    $sender->sendMessage(TextFormat::RED."このコマンドを実行する権限がありません。");
                    return true;
                }
                $this->getLogger()->info("=== MoneySystemAPI ===");
                $this->getLogger()->info("- mymoney");
                $this->getLogger()->info("- pay");
                $this->getLogger()->info("- mystatus");
                $this->getLogger()->info("- see");
                $this->getLogger()->info("- addmoney");
                $this->getLogger()->info("- take");
                $this->getLogger()->info("- setmoney");
                $this->getLogger()->info("- moneyrank");
                $this->getLogger()->info("- setlang");
                $this->getLogger()->info("- moneysystem");
                $this->getLogger()->info(TextFormat::DARK_RED."- removesystem");
                $this->getLogger()->info("MoneySystemAPI and other MoneySystem POWERED BY metowa1227");
                return true;
                break;

                /** MoneySystemをご利用いただきまして誠にありがとうございました。 また会うその日まで... **/

            case "mymoney":
                if($sender instanceof Player){
                    $money = $this->Check($sender);
                    $sender->sendMessage(str_replace(array("%MONITORUNIT%", "%MONEY%"), array($this->unit, $money), $this->getMessage($name, "mymoney")));
                    return true;
                }else{
                        $this->getLogger()->info("[CONSOLE MONEY SYSTEM] $".$this->CheckForName($name));
                        return true;
                }
            break;

        case "see":
            if(!isset($args[0])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "see.tergetname.not.set"));
                return true;
            }
            if(!$this->ExistsAccountForName($args[0])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "see.tergetplayer.not.found"));
                return true;
            }
            $tergetmoney = $this->CheckForName($args[0]);
            $sender->sendMessage(str_replace(array("%TERGET%", "%MONITORUNIT%", "%MONEY%"), array($args[0], $this->unit, $tergetmoney), $this->getMessage($name, "see.terget.money")));
            return true;
        break;

        case "pay":
            if($sender instanceof Player){
                $tergetplayer = $args[0];
                $paymoney = $args[1];
                if(!isset($tergetplayer)){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "pay.tergetname.not.set"));
                    return true;
                }
                if(!$this->ExistsAccountForName($tergetplayer)){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "pay.tergetplayer.not.found"));
                    return true;
                }
                if(!isset($paymoney)){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "pay.amount.not.set"));
                    return true;
                }
                if(!is_numeric($paymoney)){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "pay.amount.not.numeric"));
                    return true;
                }
                if($name === $tergetplayer){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "pay.myself.error")); 
                    return true;
                }
                if($this->ExistsAccountForName($tergetplayer)){
                    $user = $this->getServer()->getPlayer($tergetplayer);
                    $money = $this->CheckForName($name);
                    if(0 > $paymoney){
                        $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "pay.decimal.error"));
                        return true;
                    }
                    if($money < $paymoney){
                        $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "pay.lack.money.error"));
                        return true;
                    }
                    if(0 >= $paymoney){
                        $sender->sendMessage(TextFormat::YELLOW.str_replace("%MONITORUNIT%", $this->unit, $this->getMessage($name, "pay.0.or.less")));
                        return true;
                    }
                    if(!isset($user)){
                        $this->player->set($tergetplayer, [
                            "price" => $paymoney,
                            "player" => $name,
                        ]);
                        $this->player->save();
                        try{
                            $result = $this->TakeMoney($sender, $paymoney, "pay.command");
                            if(!$result){
                                throw new \Exception("Pay : take money missing error, Event cancelled with other plugins.");
                            }
                        }catch(\Exception $error){
                            $sender->sendMessage(TextFormat::RED.TextFormat::BOLD.$error->getMessage());
                            return true;
                        }
                        $sender->sendMessage(str_replace(array("%TERGET%", "%MONITORUNIT%", "%AMOUNT%"), array($tergetplayer, $this->unit, $paymoney), TextFormat::GREEN.$this->getMessage($name, "pay.success.1")."\n".str_replace("%TERGET%", $tergetplayer, TextFormat::GREEN.$this->getMessage($name, "pay.success.2"))));
                        return true;
                    }
                    try{
                        try{
                            $result = $this->TakeMoney($sender, $paymoney, "pay.command");
                            if(!$result){
                                throw new \Exception("Pay : take money missing error, Event cancelled with other plugins.");
                            }
                        }catch(\Exception $error){
                            $sender->sendMessage(TextFormat::RED.TextFormat::BOLD."[ERROR] pay command is cancelled. ( by other plugins )");
                            return true;
                        }
                        $result = $this->AddMoneyForName($tergetplayer, $paymoney, "pay.command");
                        if(!$result){
                            throw new \Exception("Pay : add money missing error, Event cancelled with other plugins.");
                        }
                    }catch(\Exception $error){
                        $sender->sendMessage(TextFormat::RED.TextFormat::BOLD.$error->getMessage());
                        return true;
                    }      
                    $sender->sendMessage(str_replace(array("%USER%", "%TERGET%", "%MONITORUNIT%", "%AMOUNT%"), array($name, $tergetplayer, $this->unit, $paymoney), TextFormat::GREEN.$this->getMessage($name, "pay.success.3")));
                    if($user instanceof Player){
                        $user->sendMessage(TextFormat::GREEN.str_replace(array("%USER%", "%MONITORUNIT%", "%AMOUNT%"), array($name, $this->unit, $paymoney),TextFormat::GREEN.$this->getMessage("pay.success.4")));
                        return true;
                    }
                }
            }else{
                $tergetplayer = $args[0];
                $paymoney = $args[1];
                if(!isset($tergetplayer)){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage("CONSOLE", "pay.tergetname.not.set"));
                    return true;
                }
                if(!$this->ExistsAccountForName($tergetplayer)){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage("CONSOLE", "pay.tergetplayer.not.found"));
                    return true;
                }
                if(!isset($paymoney)){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage("CONSOLE", "pay.amount.not.set"));
                    return true;
                }
                if(!is_numeric($paymoney)){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage("CONSOLE", "pay.amount.not.numeric"));
                    return true;
                }
                if($name === $tergetplayer){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage("CONSOLE", "pay.myself.error")); 
                    return true;
                }
                if($this->ExistsAccountForName($tergetplayer)){
                    $user = $this->getServer()->getPlayer($payn);
                    $money = $this->CheckForName($name);
                    if(0 > $paymoney){
                        $sender->sendMessage(TextFormat::YELLOW.$this->getMessage("CONSOLE", "pay.decimal.error"));
                    }
                    if(!isset($user)){
                        $this->player->set($tergetplayer, [
                            "price" => $paymoney,
                            "player" => $name,
                        ]);
                        $this->player->save();
                        $sender->sendMessage(str_replace(array("%TERGET%", "%MONITORUNIT%", "%AMOUNT%"), array($tergetplayer, $this->unit, $paymoney), TextFormat::GREEN.$this->getMessage("CONSOLE", "pay.success.1")."\n".str_replace("%TERGET%", $tergetplayer, TextFormat::GREEN.$this->getMessage("CONSOLE", "pay.success.2"))));
                        return true;
                    }
                    try{
                        $result = $this->AddMoneyForName($tergetplayer, $paymoney, "pay.command");
                        if(!$result){
                            throw new \Exception("Pay : add money missing error, Event cancelled with other plugins.");
                        }
                    }catch(\Exception $error){
                        $sender->sendMessage(TextFormat::RED.TextFormat::BOLD.$error->getMessage());
                        return true;
                    }
                    $this->getLogger()->info(TextFormat::GREEN.str_replace(array("%TERGET%", "%MONITORUNIT%", "%AMOUNT%"), array($tergetplayer, $this->unit, $paymoney), $this->getMessage("CONSOLE", "pay.success.5")));
                    return true;
                    break;
                }
            }

        case "mystatus":
            if(!$sender instanceof Player){$this->getLogger()->info($this->getMessage("CONSOLE", "console.not.command"));return true;}
            $allMoney = 0;
            foreach($this->Money->getAll() as $key => $value){
                if($key === "CONSOLE"){
                    continue;
                }
            $allMoney += $value;
            }
            $output = ltrim($allMoney, "CONSOLE");
            $topMoney = 0;
            if($allMoney > 0){
                $topMoney = round((($this->Money->get($sender->getName()) / $allMoney) * 100), 2);
            }
            $sender->sendMessage(str_replace("%STATUS%", $topMoney, $this->getMessage($name, "status.view")));
            return true;
        break;

/**
    **@internal can use this commands is operator permission have players only
**/

        case "addmoney":
            if(!$sender->isOp()){
                $sender->sendMessage(TextFormat::RED.$this->getMessage($name, "not.permission"));
                return true;
            }
            if(!isset($args[0])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "addmoney.tergetname.not.set"));
                return true;
            }
            $tergetplayer = $args[0];
            if(!$this->ExistsAccountForName($tergetplayer)){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "addmoney.tergetplayer.not.found"));
                return true;
            }
            if(!isset($args[1])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "addmoney.amount.not.set"));
                return true;
            }
            $addmoney = $args[1];
            if(!is_numeric($addmoney)){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "addmoney.amount.not.numeric"));
                return true;
            }
            if($this->ExistsAccountForName($tergetplayer)){
                if(0 > $addmoney){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "addmoney.decimal.error"));
                    return true;
                }
                try{
                    $result = $this->AddMoneyForName($tergetplayer, $addmoney, "addmoney.command");
                    if(!$result){
                        throw new \Exception("Add : add money missing error, Event cancelled with other plugins.");
                    }
                }catch(\Exception $error){
                    $sender->sendMessage(TextFormat::RED.TextFormat::BOLD.$error->getMessage());
                    return true;
                }
                $aftermoney = $this->CheckForName($tergetplayer);
                $sender->sendMessage(TextFormat::GREEN.str_replace(array("%TERGET%", "%MONITORUNIT%", "%AMOUNT%", "%TERGET%", "%MONITORUNIT%", "%MONEY%"), array($tergetplayer, $this->unit, $addmoney, $tergetplayer, $this->unit, $aftermoney), $this->getMessage($name, "addmoney.success")));
                return true;
                break;
        }

          case "setmoney":
            if($sender->isOp()){
                $sender->sendMessage(TextFormat::RED.$this->getMessage($name, "not.permission"));
                return true;
            }
            if(!isset($args[0])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "setmoney.tergetname.not.set"));
                return true;
            }
            $tergetplayer = $args[0];
            if(!$this->ExistsAccountForName($tergetplayer)){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "setmoney.tergetplayer.not.found"));
                return true;
            }
            if(!isset($args[1])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "setmoney.amount.not.set"));
                return true;
            }
            $setmoney = $args[1];
            if(!is_numeric($setmoney)){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "setmoney.amount.not.numeric"));
                return true;
            }
            if($this->ExistsAccountForName($tergetplayer)){
                if(0 > $setmoney){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "setmoney.decimal.error"));
                    return true;
                }
                try{
                    $result = $this->SetMoneyForName($tergetplayer, $setmoney, "setmoney.command");
                    if(!$result){
                        throw new \Exception("Set : set money missing error, Event cancelled with other plugins.");
                    }
                }catch(\Exception $error){
                    $sender->sendMessage(TextFormat::RED.TextFormat::BOLD.$error->getMessage());
                    return true;
                }
                $sender->sendMessage(TextFormat::GREEN.str_replace(array("%TERGET%", "%MONITORUNIT%", "%MONEY%"), array($tergetplayer, $this->unit, $setmoney), $this->getMessage($name, "setmoney.success")));
                return true;
                break;
            }

        case "take":
            if($sender->isOp()){
                $sender->sendMessage(TextFormat::RED.$this->getMessage($name, "not.permission"));
                return true;
            }
            if(!isset($args[0])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "takemoney.tergetname.not.set"));
                return true;
            }
            $tergetplayer = $args[0];
            if(!$this->ExistsAccountForName($tergetplayer)){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "takemoney.tergetplayer.not.found"));
                return true;
            }
            if(!isset($args[1])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "takemoney.amount.not.set"));
                return true;
            }
            $takemoney = $args[1];
            if(!is_numeric($takemoney)){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "takemoney.amount.not.numeric"));
                return true;
            }
            if($this->ExistsAccountForName($tergetplayer)){
                if(0 > $takemoney){
                    $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "takemoney.decimal.error"));
                    return true;
                }
                try{
                    $result = $this->TakeMoneyForName($tergetplayer, $takemoney, "takemoney.command");
                    if(!$result){
                        throw new \Exception("Take : take money missing error, Event cancelled with other plugins.");
                    }
                }catch(\Exception $error){
                    $sender->sendMessage(TextFormat::RED.TextFormat::BOLD.$error->getMessage());
                    return true;
                }
                $aftermoney = $this->CheckForName($tergetplayer);
                $sender->sendMessage(TextFormat::GREEN.str_replace(array("%TERGET%", "%MONITORUNIT%", "%AMOUNT%", "%TERGET%", "%MONITORUNIT%", "%MONEY%"), array($tergetplayer, $this->unit, $takemoney, $tergetplayer, $this->unit, $aftermoney), $this->getMessage($name, "takemoney.success")));
                return true;
                break;
            }

        case "setlang":
            if(!isset($args[0])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "setlang.tergetlang.not.set"));
                return true;
            }
            $lang = $args[0];
            if($this->ExistsLang($lang) == false){
                $sender->sendMessage(TextFormat::YELLOW.str_replace("%LANGUAGE%", $lang, $this->getMessage($name, "setlang.language.not.found")));
                return true;
            }
            $result = $this->setLang($name, $lang);
            if($result){
                $sender->sendMessage(TextFormat::GREEN.$this->getMessage($name, "setlang.success"));
                return true;
            }else{
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "setlang.error"));
                return true;
            }
        break;

        case "moneyrank":
            if($this->dt == "SQLITEFILE"){
                $sender->sendMessage(TextFormat::AQUA.TextFormat::BOLD."SORRY. This command is building now. If you need to use this command. When please edit the config file for \"DataBaseType\" to \"YAMLFILE\".");
                return true;
            }
            if(!isset($args[0])){
                $args[0] = 1;
            }elseif(!is_numeric($args[0])){
                $sender->sendMessage(TextFormat::YELLOW.$this->getMessage($name, "moneyrank.not.numeric.error"));
                return true;
            }
            $all = $this->getAllMoneyData();
            $max = 0;
            foreach($all as $c){
                $max += count($c);
            }
            $max = ceil(($max / 5));
            $page = max(1, $args[0]);
            $page = min($max, $page);
            $page = (int) $page;
            $sender->sendMessage(TextFormat::AQUA."===MoneySystem MoneyRanking [".$page."/".$max."]===");
            arsort($all);
            $oprank = $this->config->get("AddRank.OP");
            $i = 0;
            foreach($all as $a => $b){
                if($a == "CONSOLE"){
                    continue;
                }
                if(isset($this->getServer()->getOps()->getAll()[$a]) and $oprank == "false"){
                    continue;
                }
                if(($page - 1) * 5 <= $i && $i <= ($page - 1) * 5 + 4){
                    $i1 = $i + 1;
                    $sender->sendMessage($i1."> ".$a." | ".$this->unit.$b);
                }
                $i++;
            }
            return true;
            break;
        }
    }

    private function LoadConfig(){
        $this->loadconfig = isset($this->loadconfig[$this]);
        return $this->loadconfig;
    }

    private function LoadData(){
        $this->loaddata = isset($this->loaddata[$this]);
        return $this->loaddata;
    }

    private function LoadDataBase(){
        $this->loaddatabase = isset($this->loaddatabase[$this]);
        return $this->loaddatabase;
    }

    private function AutoUpdateChecker(){
        $newversion = @file_get_contents('http://metowa1227.s1001.xrea.com/MoneySystemNewVersion');
        if(!$newversion){
            $this->getLogger()->error("[ERROR] インターネットへの未接続");
            $this->getServer()->shutdown();
        }
        if($newversion > self::PLUGIN_VERSION){
            $this->getLogger()->notice("新しいバージョンがリリースされています。 新しいバージョン: ".$newversion."  (NewVersion Released. Please update MoneySystem.)");
            $this->config->set("FirstStarted", false);
            $this->config->save();
        }elseif($newversion == self::PLUGIN_VERSION){
            $this->getLogger()->info("このMoneySystemは最新です。");
        }
            $this->getLogger()->info("Compleate.");
    }

    private function GetUpdateDescription(){
        if($this->config->exists("FirstStarted")){
            $result = $this->config->get("FirstStarted");
            if(!$result){
                $newversion = @file_get_contents('http://metowa1227.s1001.xrea.com/MoneySystemNewVersion');
                $newversiondes = @file_get_contents('http://metowa1227.s1001.xrea.com/MoneySystemNewVersionDescription');
                if(!$newversiondes){
                    $this->getLogger()->error("[ERROR] インターネットへの未接続");
                    $this->getServer()->shutdown();
                }
                $this->getLogger()->info(TextFormat::AQUA."新バージョンの更新内容: MoneySystem ver".self::PLUGIN_VERSION." -> ".$newversion." : ");
                $description = str_replace("???", "\n                                                  ", $newversiondes);
                $this->config->set("FirstStarted", true);
                $this->config->save();
                $this->getLogger()->info(TextFormat::GREEN.$description);
            }
        }
    }

    private function DB($sql, $return = false) {
        if ($return) {
            return $this->db->query($sql)->fetchArray();
        } else {
            $this->db->query($sql);
            return true;
        }
    }
}