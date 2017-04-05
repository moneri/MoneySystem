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

namespace CustomName;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\level\level;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\tile\Sign;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Effect;
use pocketmine\event\Listener;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\particle\Particle;
use pocketmine\event\player\PlayerQuitEvent;
use MoneySystemAPI\main;

class main extends PluginBase implements Listener{
public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    if(!file_exists($this->getDataFolder())){
           mkdir($this->getDataFolder(),0774,true);
        }
	   $this->name = new Config($this->getDataFolder()."CustomName.yml",Config::YAML);
	   $this->config = new Config($this->getDataFolder()."Config.yml",Config::YAML, array(
        "Cost" => "30000",
        "GetMoney" => "5000",
	   	));
	   $this->getLogger()->notice("CustomNameを読み込みました。　二次配布は禁止です。　製作者: metowa1227");
	   $this->getLogger()->notice("MoneySystemAPIが必要です。");
	   $this->Money = $this->getServer()->getPluginManager()->getPlugin("MoneySystemAPI");
}
        public function onCommand (CommandSender $sender, Command $command, $label, array $args){
                                      if (!$sender instanceof Player) return $sender->sendMessage("このコマンドはゲーム内で使用してください");
                switch($command->getName()) {
                case "setname":
			if(!isset($args[0])){
				$sender->sendMessage("§e>>セットするネームタグを記入してください。");
				return true;
			}else{
				if(isset($args[0])){
                $name = $sender->getName();
                $cost = $this->config->get("Cost");
                $money = $this->Money->Check($sender);
                if($cost > $money){
                $sender->sendMessage("§e>>所持金が不足しています。");
                return true;
            }
                    $name = $sender->getName();
                    if(!isset($this->a[$sender->getName()])){
             	$tag = $args[0];
             	$minus = $this->config->get("Cost");
             	$this->Money->TakeMoney($name,$minus);
                unset($this->a[$name]);
                $sender->setNameTag("§b[§f".$tag."§b]§f".$name);
                $sender->setDisplayName("§b[§f".$tag."§b]§f".$name);
                $sender->sendMessage("ネームタグをカスタムしました。 タグ : ".$tag."");
                $this->name->set($name,$args[0]);
                $this->name->save();
                return true;
                break;
            }
        }
    }
                case "unsetname":
                                          if (!$sender instanceof Player) return $sender->sendMessage("このコマンドはゲーム内で使用してください");
                $name = $sender->getName();
                $amount = $this->config->get("GetMoney");
                $this->Money->AddMoney($name,$amount);
                $sender->setNameTag($name);
                $sender->setDisplayName($name);
                $sender->sendMessage("ネームタグをリセットしました。");
                $this->name->remove($name);
                $this->name->save();
                return true;
                break;
            }
        }
     public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $name = $player->getName();
        if($this->name->exists($name)){
        $player->setNameTag("§b[§f".$this->name->get($name)."§b]§f".$name);
        $player->setDisplayName("§b[§f".$this->name->get($name)."§b]§f".$name);
        return true;
        }else{
}
}
}