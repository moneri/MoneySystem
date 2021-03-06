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
use pocketmine\utils\TextFormat;

class MoneySystemAPI extends PluginBase implements Listener{

  /**
  **@author metowa1227
  **/

  /*  getMonitorUnit  */
  /**
  * @var array
  */

  private $unit = "$";

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

  const PLUGIN_VERSION = "6.0.0";

  const PACKAGE_VERSION = "6.7.0 alpha API";

  const API_VERSION = "5.0";

  /**
  * @var ahthor metowa1227
  */

  private $author = "metowa1227";

  const PLUGIN_AUTHOR = "metowa1227";

  const PLUGIN_REGISTED_DATE = "2016/09/03";

  private static $instance = null;

  public static function getInstance(){
    return self::$instance;
  }

  public function onLoad(){
    self::$instance = $this;
  }

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

  /**
  **@param \PluginBase
  **@param \MoneySystem enable event
  **/

  /**
  **@return bool
  **/

  private $enable = null;

	public function onEnable(){
    $this->getLogger()->notice(self::PLUGIN_NAME." is Loading... please wait...");
    $this->getLogger()->info("0% Loaded...");
    if(!file_exists($this->getDataFolder())){
    mkdir($this->getDataFolder(), 0755, true); 
    }   
    $c = $this->LoadConfig();
    $this->saveDefaultConfig();
    $this->saveResource("Config.yml", false);
    if(!isset($c)){
    $this->getServer()->getPluginManager()->disablePlugin($this);
    $this->getLogger()->warning("[MoneySystem ERROR] システムファイルの読み取りに失敗しました。　ファイルを確認してください。 ERROR-CODE 1001-cdf");
    }
    $a = mt_rand(1,2);
    $b = mt_rand(0,9);
    $this->getLogger()->info($a.$b."% Loaded...");
    $this->player = new Config($this->getDataFolder() . "PlayerPaing.yml", Config::YAML, []);
    $this->Money = new Config($this->getDataFolder() . "Money.yml", Config::YAML, []);
    $this->config = new Config($this->getDataFolder() . "Config.yml", Config::YAML, []);
    $d = $this->LoadData();
    if(!isset($d)){
    $this->getServer()->getPluginManager()->disablePlugin($this);
    $this->getLogger()->warning("[MoneySystem ERROR] プラグインのデータの読み取りに失敗しました。　データファイルを確認してください。 ERROR-CODE 1002-cdf");
    }
    $a = mt_rand(4,6);
    $b = mt_rand(0,9);
    $this->getLogger()->info($a.$b."% Loaded...");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->Money->set("CONSOLE", 999999999999);
    $this->Money->save();
    $a = mt_rand(7,8);
    $b = mt_rand(0,9);
    $this->getLogger()->info($a.$b."% Loaded...");
    $f = $this->LoadDataBase();
    if(!isset($f)){
    $this->getServer()->getPluginManager()->disablePlugin($this);
    $this->getLogger()->warning("[MoneySystem ERROR] プラグインのデータの読み取りに失敗しました。　データファイルを確認してください。 ERROR-CODE 1003-cdf");
    }
    $this->getLogger()->info("100% Loaded...");
    $this->getLogger()->notice("§a[Sucsess] MoneySystemを起動しました。 Plugin info: ver.".self::PLUGIN_VERSION." author.".self::PLUGIN_AUTHOR." PACK.".self::PACKAGE_VERSION." date.".self::PLUGIN_REGISTED_DATE." API.".self::API_VERSION);
    $this->enable = (boolean) true;
    }

  /**
  **@param \PlayerJoinEvent
  **@param \Setplayer default money for new player
  **@var Add money
  **/

  public function onJoin(PlayerJoinEvent $event){
    $p = "§b[MoneySystem]§f";
    $player = $event->getPlayer();
    $name = $player->getName();
    if($this->player->exists($name)){
      $pay = $this->player->get($name);
      $price = $pay["price"];
      $payner = $pay["player"];
      $now = $this->Money->get($name);
      $af = $now + $price;
      $this->Money->set($name, $af);
      $player->sendMessage($p.$payner."さんから$".$price."を受け取りました。");
      $this->player->remove($name);
      $this->player->save();
      return true;
    }
    if(!$this->Money->exists($name)){
      $this->Money->set($name,1000);
      $this->Money->save();
      $default = $this->config->get("DefaultMoney");
      if(!is_numeric($default)){
        $default = 1000;
      }
    $player->sendMessage($p."ようこそ。あなたに".$this->unit.$default."を付与しました。");
    }
  }

  /**
  **@var Add money for target player
  **@internal Max money have player is set money '999999999999'
  **@return false
  **/

  public function AddMoney(Player $player, $amount){
    $name = $player->getName();
    $fast = $this->Money->get($name);
    $give = $fast + $amount;
    $this->Money->set($name,$give);
    $this->Money->save();
    if($this->Money->get($name) > 999999999999){
      $this->Money->set($name,999999999999);
      $this->Money->save();
    }
  }

  /**
  **@var minus money for target player
  **@internal min money have player is set money '0'
  **@return false
  **/

  public function TakeMoney(Player $player, $minus){
    $user = $player->getName();
    $fast = $this->Money->get($user);
    $mynus = $fast - $minus;
    if($mynus < 0){
      $mynus = 0;
    }
    $this->Money->set($user, $mynus);
    $this->Money->save();
  }

  public function Check(Player $player){
    $name = $player->getName();
    if(!$this->Money->exists($name)){
    return false;
    }
    return $this->Money->get($name);
    }

  /**
  **@var Set money for target player
  **@internal Max money have player is set money '999999999999'
  **@internal Min money have player is set money '0'
  **@return false
  **/

  public function SetMoney(Player $player, $money){
    $name = $player->getName();
    $this->Money->get($name);
    $this->Money->set($name,$money);
    $this->Money->save();
    if($this->Money->get($name) > 999999999999){
      $this->Money->set($name,999999999999);
      $this->Money->save();
    }
  }

  /**
  **@var power off MoneySystem event
  **@internal offset all players support engine
  **@return bool
  **/

  public function onDisable(){
    $this->Money->save();
    $this->player->save();
    $this->enable = (boolean) false;
    $this->getLogger()->info(TextFormat::GREEN."MoneySystemをシャットダウンしました。");
  }

  /**
  **@var create new account for player
  **@var we need playername for create account
  **@link MoneySystem using database file 'Money.yml'
  **/

  public function CreateAccount(string $name, $money){
    if(!isset($money)){
      $money = 1000;
    }
    if(!is_numeric($money)){
      $money = 1000;
    }
    $this->Money->set($name, $money);
    $this->Money->save();
  }

  /**
  **@var remove account for player
  **@var we need playername for remove account
  **@link MoneySystem using database file 'Money.yml'
  **/

  public function RemoveAccount(string $name){
    if(!$this->Money->exists($name)){
      return false;
    }
    $this->Money->remove($name);
    $this->Money->save();
  }

  /**
  **@var reset all players money for default money
  **@internal only calling for admin permission have player
  **@return bool
  **/

  public function setPlayerDefaultMoney(Player $player, $money) : int{
    $name = $player->getName();
    if(!$this->Money->exists($name)){
      return false;
    }else{
      $default = $this->config->get("DefaultMoney");
      $this->Money->set($name, $default);
    }
  }

  /**
  **@var we can setting target player's money
  **/

  public function getDefaultMoney(){
    return $this->config->get("DefaultMoney");
  }

  /**
  **@var we can setting terget player's configlation file
  **/

  public function setAllDefaultMoney(){
    foreach($this->Money->getAll(true) as $all){
      $this->Money->set($all, $this->config->get("DefaultMoney"));
      $this->Money->save();
    }
  }

  /**
  **@var your need please given money informational is int or boolean
  **@internal this called block is int
  **@internal string or boolean or array is expension error debug strage
  **@throws \RuntimeException if not int given
  **/

  public function setDefaultMoney($money) : int{
    if(!is_numeric($money)){
      $money = 1000;
    }
    $this->config->set("DefaultMoney",$money);
    $this->config->save();
  }

  /**
  **@var your need please given money informational is int or boolean
  **@internal this called block is int
  **@internal string or boolean or array is expension error debug strage
  **@throws \RuntimeException if not int given
  **/

  public function setAllCustomMoney($money) : int{
    if(!is_numeric($money)){
      $money = 1000;
    }
    foreach($this->Money->getAll(true) as $all){
      $this->Money->set($all, $money);
      $this->Money->save();
    }
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

  public function isEnable(){
    return (boolean) $this->enable;
  }

  /**
  **@internal backedup this plugin database files
  **@internal all files backupping
  **@return bool
  **@throws \RuntimeException is backup failed only
  **/

  public function BackupFiles(){
    $dir = $this->getDataFolder();
    if(!is_dir($dir)){return false;}
    if(!file_exists($dir."PlayerPaing.yml") or !file_exists($dir."Money.yml") or !file_exists($dir."Config.yml")){return false;}
    @mkdir($this->getDataPath()."MoneySystemBackupFiles");
    $dir = $this->getDataPath()."MoneySystemBackupFiles\\";
    $date = date("Y/m/d");
    $file[0] = $dir."PlayerPaing (Backedup {$date}).yml";
    $file[1] = $dir."Money (Backedup {$date}).yml";
    $file[2] = $dir."Config (Backedup {$date}).yml";
    $load[0] = @fopen($file[0], "w");
    $load[1] = @fopen($file[1], "w");
    $load[2] = @fopen($file[2], "w");
    if(!is_resource($load[0]) or !is_resource($load[1]) or !is_resource($load[2])){
      throw new \RuntimeException("[Backup failed] Could not create files.");
    }
    $all[0] = $this->player->getAll();
    $all[1] = $this->Money->getAll();
    $all[2] = $this->config->getAll();
    @fwrite($load[0], $all[0].PHP_EOL);
    @fwrite($load[1], $all[1].PHP_EOL);
    @fwrite($load[2], $all[2].PHP_EOL);
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
    switch (strtolower($command->getName())){
      case "mymoney":
        if($sender instanceof Player){
          $money = $this->Money->get($name);
          $sender->sendMessage("現在の所持金は$".$money."です。");
          return true;
    }else{
          $this->getLogger()->info("[CONSOLE MONEY] $".$this->Money->get($name));
          return true;
    }
    break;

      case "see":
        if(!isset($args[0])){
          $sender->sendMessage(TextFormat::YELLOW.">>所持金を確認するプレイヤー名を入力してください。");
          return true;
    }else{         
          if(!$this->Money->exists($args[0])){
            $sender->sendMessage(TextFormat::YELLOW.">>指定したプレイヤーの情報が見つかりませんでした。");
            return true;
    }
          $name = $args[0];
          $seeplayer = $this->Money->get($name);
          $sender->sendMessage($args[0]."さんの所持金は、 ".$this->unit.$seeplayer."です。");
          return true;
          break;
    }

      case "pay":
        if($sender instanceof Player){
          if(!isset($args[0])){
            $sender->sendMessage(TextFormat::YELLOW.">>所持金を付与するプレイヤー名を入力してください。");
            return true;
    }else{
            if(!$this->Money->exists($args[0])){
              $sender->sendMessage(TextFormat::YELLOW.">>指定したプレイヤーの情報が見つかりませんでした。");
              return true;
    }else{
            if(!isset($args[1])){
              $sender->sendMessage(TextFormat::YELLOW.">>お金を付与する金額を指定してください。");
              return true;
    }else{
            if(!isset($args[1])){
              $sender->sendMessage(TextFormat::YELLOW.">>所持金を付与する金額を入力してください。");
              return true;
    }else{         
            $pay = $args[0];
            if($name === $pay){
              $sender->sendMessage(TextFormat::YELLOW.">>自分にお金を付与することはできません。"); 
              return true;
    }else{
            if($this->Money->exists($args[0])){
              $payn = $args[0];
              $user = $this->getServer()->getPlayer($payn);
              $paymoney = $args[1];
              $money = $this->Money->get($name);
              if(0 > $args[1]){
                $sender->sendMessage(TextFormat::YELLOW.">>支払う金額に、小数点以下は指定できません。");
    }else{
            if($money < $paymoney){
              $sender->sendMessage(TextFormat::YELLOW.">>所持金が不足しています。");
    }else{
            if(0 == $paymoney){
              $sender->sendMessage(TextFormat::YELLOW.">>付与する金額は、0円以上で、お願いします。");
    }else{
            $get = $this->getServer()->getPlayer($args[0]);
            if(!isset($get)){
              $this->player->set($args[0], [
                  "price" => $args[1],
                  "player" => $sender->getName(),
              ]);
              $this->player->save();
              $sender->sendMessage($args[0]."さんに".$this->unit.$args[1]."を付与しました。\n".$args[0]."は現在オフラインの為、ログインしたときに付与されます。");
              return true;
    }
            $mynus = $money - $paymoney;
            $this->Money->set($name,$mynus);
            $this->Money->save();
            $paying = $this->Money->get($args[0]);
            $afmoney = $paying + $args[1];
            $this->Money->set($args[0],$afmoney);
            $this->Money->save();
            $sender->sendMessage($name."=====>".$args[0]."へ ".$this->unit.$args[1]."を付与しました。");
            if($user instanceof Player){
            $user->sendMessage($name."から $".$args[1]."を受け取りました。");
            return true;
    }
    }
    }
    }
    }
    }
    }
    }
    }
    }
    }else{
            if(!isset($args[0])){
              $sender->sendMessage(TextFormat::YELLOW.">>所持金を付与するプレイヤー名を入力してください。");
              return true;
    }
            if(!$this->Money->exists($args[0])){
              $sender->sendMessage(TextFormat::YELLOW.">>指定したプレイヤーの情報が見つかりませんでした。");
              return true;
    }
            if(!isset($args[1])){
              $sender->sendMessage(TextFormat::YELLOW.">>お金を付与する金額を指定してください。");
              return true;
    }
            $pay = $args[0];
            if($name === $pay){
              $sender->sendMessage(TextFormat::YELLOW.">>自分にお金を付与することはできません。"); 
              return true;
    }else{
            if($this->Money->exists($args[0])){
              $payn = $args[0];
              $user = $this->getServer()->getPlayer($payn);
              $paymoney = $args[1];
              $money = $this->Money->get($name);
              if(0 > $args[1]){
                $sender->sendMessage(TextFormat::YELLOW.">>支払う金額に、小数点以下は指定できません。");
    }
              $get = $this->getServer()->getPlayer($args[0]);
              if(!isset($get)){
                $this->player->set($args[0], [
                    "price" => $args[1],
                    "player" => $sender->getName(),
              ]);
              $this->player->save();
              $sender->sendMessage($args[0]."さんに".$this->unit.$args[1]."を付与しました。\n".$args[0]."は現在オフラインの為、ログインしたときに付与されます。");
              return true;
    }
              $aa = $this->Money->get($args[0]);
              $af = $aa + $args[1];
              $this->Money->set($name, $af);
              $this->Money->save();
              $this->getLogger()->info("[CONSOLE PAY SYSTEM] ".$args[0]."へ".$this->unit.$args[1]."を付与しました。");
              return true;
              break;
    }
    }
    }

      case "mystatus":
      if(!$sender instanceof Player){$this->getLogger()->info("このコマンドはコンソールからは実行できません。");return true;}
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
        $sender->sendMessage("あなたのステータス : 全体の".$topMoney."%");
        return true;
        break;

    /**
    **@internal can use this commands is operator permission have players only
    **/

      case "addmoney":
        if(!isset($args[0])){
          $sender->sendMessage(TextFormat::YELLOW.">>所持金を付与するプレイヤー名を入力してください。");
          return true;
    }else{
        if(!$this->Money->exists($args[0])){
          $sender->sendMessage(TextFormat::YELLOW.">>指定したプレイヤーの情報が見つかりませんでした。");
          return true;
    }
        if(!isset($args[1])){
          $sender->sendMessage(TextFormat::YELLOW.">>所持金を付与する金額を入力してください。");
          return true;
    }else{
        if($this->Money->exists($args[0])){
          $payn = $args[0];
          $paymoney = $args[1];
          $money = $this->Money->get($name);
          if(0 > $args[1]){
            $sender->sendMessage(TextFormat::YELLOW.">>付与する金額に、小数点以下は指定できません。");
    }else{
          $give = $this->Money->get($args[0]);
          $afmoney = $give + $args[1];
          $this->Money->set($args[0],$afmoney);
          $this->Money->save();
          $sender->sendMessage($args[0]."の所持金を".$this->unit.$args[1]."増やしました。 ".$args[0]."の所持金が".$this->unit.$afmoney."になりました。");
          return true;
          break;
    }
    }
    }
    }

      case "setmoney":
        if(!isset($args[0])){
          $sender->sendMessage(TextFormat::YELLOW.">>所持金をセットするプレイヤー名を入力してください。");
          return true;
    }else{
        if(!$this->Money->exists($args[0])){
          $sender->sendMessage(TextFormat::YELLOW.">>指定したプレイヤーの情報が見つかりませんでした。");
          return true;
    }
        if(!isset($args[1])){
          $sender->sendMessage(TextFormat::YELLOW.">>所持金をセットする金額を入力してください。");
          return true;
    }else{
        if($this->Money->exists($args[0])){
          $payn = $args[0];
          $paymoney = $args[1];
          $money = $this->Money->get($name);
          if(0 > $args[1]){
            $sender->sendMessage(TextFormat::YELLOW.">>付与する金額に、小数点以下は指定できません。");
    }else{
        $give = $this->Money->get($args[0]);
        $afmoney = $args[1];
        $this->Money->set($args[0],$afmoney);
        $this->Money->save();
        $sender->sendMessage($args[0]."の所持金を".$this->unit.$args[1]."にセットしました");
        return true;
        break;
    }
    }
    }
    }

      case "take":
        if(!isset($args[0])){
          $sender->sendMessage(TextFormat::YELLOW.">>所持金を没収するプレイヤー名を入力してください。");
          return true;
    }else{
        if(!$this->Money->exists($args[0])){
          $sender->sendMessage(TextFormat::YELLOW.">>指定したプレイヤーの情報が見つかりませんでした。");
         return true;
    }
        if(!isset($args[1])){
          $sender->sendMessage(TextFormat::YELLOW.">>所持金を没収する金額を入力してください。");
          return true;
    }else{
        if($this->Money->exists($args[0])){
          $payn = $args[0];
          $lost = $args[1];
          $money = $this->Money->get($name);
          if(0 > $args[1]){
            $sender->sendMessage(TextFormat::YELLOW.">>没収する金額に、小数点以下は指定できません。");
    }else{
        $give = $this->Money->get($args[0]);
        $afmoney = $give - $lost;
        $this->Money->set($args[0],$afmoney);
        $this->Money->save();
        $sender->sendMessage($args[0]."の所持金から".$this->unit.$args[1]."を没収しました。".$args[0]."の所持金が".$this->unit.$afmoney."になりました。");
        return true;
    } 
    }  
    }
    }
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
}
