<?php

namespace MoneySystemSell;

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
use MoneySystemAPI\main;
use pocketmine\utils\Config;

class main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->notice("MoneySystemSellを読み込みました。　二次配布は禁止です。 製作者:metowa1227");
    if(!file_exists($this->getDataFolder())){
           mkdir($this->getDataFolder(),0774,true);
        }
	   $this->shop = new Config($this->getDataFolder()."SellSign.data",Config::YAML);
	   $this->Money = $this->getServer()->getPluginManager()->getPlugin("MoneySystemAPI");
	}

	public function onSign(SignChangeEvent $event){
		$block = $event->getBlock();
		$player = $event->getPlayer();
		$result = $event->getLine(0);
		if($result == "sell"){
			if(!$player->isOp()){
				$player->sendMessage("§cあなたは買取センターを作成する権限がありません。");
				return;
                        }
		            if(!is_numeric($event->getLine(2))){
				$player->sendMessage("§c記入方法が違います。　正式な記入方法で記入してください。");
				return;
}
                        if(!is_numeric($event->getLine(3))){
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
			$player->sendMessage("§b買取センターを作成しました。");
			$event->setLine(0, "§b§l[SELL§b]");
			$event->setLine(1, "§b".$itemname."");
			$event->setLine(2, "§b$".$event->getLine(2)."");
			$event->setLine(3, "§b§lAmount ".$amount."");
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
        $name = $player->getName();
		$money = $this->Money->Check($name);
					$now = microtime(true);
					if(!isset($this->tap[$player->getName()]) or $now - $this->tap[$player->getName()][1] >= 1.5  or $this->tap[$player->getName()][0] !== $loc){
		$shop = $this->shop->get($var);
						$this->tap[$player->getName()] = [$loc, $now];
              $player->sendMessage("§b本当にアイテムを売却しますか?\n§b売却する場合は、もう一度タップしてください。　売価:$".$shop["money"]."");
                        return;
                                }else{
                                $item = Item::get($shop["Item"], $shop["Meta"], $shop["amount"]);
                            if($player->getInventory()->contains($item)){
						   unset($this->tap[$player->getName()]);
                                $name = $player->getName();
                                $shop = $this->shop->get($var);
                                unset($this->a[$name]);
                                $in = $player->getInventory()->removeItem($item);
                                $amount = $shop["amount"];
								$this->Money->AddMoney($name,$amount);
                                $player->sendMessage("§a売却しました。");
                            }else{
                            	$player->sendMessage("§eアイテムがありません。");
                            	return true;
                            }
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
                       $player->sendMessage("§a買取センターを撤去しました。");
                                }else{
                                   $name = $player->getName();
                                   $player->sendPopup("あなたは看板を撤去する権限がありません。");
                                   $event->setCancelled();
                               }
                           }
                       }
                   }