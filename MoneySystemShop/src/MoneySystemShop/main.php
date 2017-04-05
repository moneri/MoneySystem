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

namespace MoneySystemShop;

use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use metowa1227\MoneySystemAPI\MoneySystemAPI;
use pocketmine\utils\Config;

class main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$Logger = $this->getLogger();
		$Logger->notice("MoneySystemShopを読み込みました。　二次配布は禁止です。　製作者: metowa1227");
		$Logger->info("MoneySystemAPIを読み込みます。");
		$this->Money = $this->getServer()->getPluginManager()->getPlugin("MoneySystemAPI");
    if(!file_exists($this->getDataFolder())){
           mkdir($this->getDataFolder(),0774,true);
        }
	   $this->shop = new Config($this->getDataFolder()."shop.yml",Config::YAML);
	}

	public function onSign(SignChangeEvent $event){
		$block = $event->getBlock();
		$player = $event->getPlayer();
		$result = $event->getLine(0);
		if($result == "shop"){
			if(!$player->isOp()){
				$player->sendMessage("§cあなたはSHOPを作成する権限がありません。");
				return;
                        }
		            if(!is_numeric($event->getLine(2))){
				$player->sendMessage("§c記入方法が違います。　正式な記入方法で記入してください。");
				return;
}
			$item = Item::fromString($event->getLine(1));
			$var = (Int)$event->getBlock()->getX().":".(Int)$event->getBlock()->getY().":".(Int)$event->getBlock()->getZ().":".$block->getLevel()->getFolderName();
                                $this->shop->set($var, [
				"x" => $block->getX(),
				"y" => $block->getY(),
				"z" => $block->getZ(),
				"level" => $block->getLevel()->getFolderName(),
				"Item" => (int) $item->getID(),
				"ItemName" => (int) $item->getName(),
				"Meta" => (int) $item->getDamage(),
				"money" => (int) $event->getLine(2),
				"amount" => (int) $event->getLine(3),
			]);
                        $this->shop->save();
            $id = $item->getID();
            $damage = $item->getDamage();
            $amount = $event->getLine(3);
            $itemname = $item->getName();
            $money = $event->getLine(2);
			$player->sendMessage("§bSHOPを作成しました。");
			$event->setLine(0, "§b§l[SHOP]");
			$event->setLine(1, "§b".$itemname."");
			$event->setLine(2, "§b$".$money.""); 
			$event->setLine(3, "§b§lAmount:".$amount."");
}else{
}
}
        public function onTouch(PlayerInteractEvent $event){
        $block = $event->getBlock();
		$loc = $block->getX().":".$block->getY().":".$block->getZ().":".$block->getLevel()->getFolderName();
        $player = $event->getPlayer();
		$var = $block->getX().":".$block->getY().":".$block->getZ().":".$block->getLevel()->getFolderName();
		if($this->shop->exists($var)){
    $shop = $this->shop->get($var);
      $aa = $this->Money->Check($player);
      if($aa < $shop["money"]){
        $player->sendMessage("§e所持金が足りません。");
        return true;
      }
        $name = $player->getName();
					$now = microtime(true);
					if(!isset($this->tap[$player->getName()]) or $now - $this->tap[$player->getName()][1] >= 1.5  or $this->tap[$player->getName()][0] !== $loc){
						$this->tap[$player->getName()] = [$loc, $now];
                        $player->sendMessage("§b本当に$".$shop["money"]."で購入しますか?");
                        $player->sendMessage("購入する場合は、もう一度タップしてください。");
                        return;
                                }else{
						   unset($this->tap[$player->getName()]);
                                $name = $player->getName();
                                $shop = $this->shop->get($var);
				$player->sendMessage("§b$".$shop["money"]."で購入しました。");
				$player->getInventory()->addItem(new Item($shop["Item"], $shop["Meta"], $shop["amount"]));
				$minus = $shop["money"];
				$this->Money->takeMoney($name,$minus);
}
}
}
       public function onBreak(BlockBreakEvent $event){
       $player = $event->getPlayer();
       $block = $event->getBlock();
       $x = $block->getX();
       $y = $block->getY();
       $z = $block->getZ();
       $world = $block->getLevel()->getName();
       $name = $player->getName(); 
       $var = (Int)$event->getBlock()->getX().":".(Int)$event->getBlock()->getY().":".(Int)$event->getBlock()->getZ().":".$world;
            if($this->shop->exists($var)){
                  if($player->isOp()){
                       $this->shop->remove($var);
                        $this->shop->save();
                       $player->sendMessage("§aSHOPを破壊しました。");
                                }else{
                                   }
                             }
                       }
        public function onCommand (CommandSender $sender, Command $command, $label, array $args){
                        switch($command->getName()) {
                case "id":
                    $item = $sender->getInventory()->getItemInHand();
                    $id = $item->getID();
                    $meta = $item->getDamage();
                    $sender->sendMessage("[ID] 手に持っているアイテムは、".$id.":".$meta."です。");
                    return true;
                break;
                }
        }
}
