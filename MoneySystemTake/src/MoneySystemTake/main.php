<?php

namespace MoneySystemTake;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent as P;
use MoneySystemAPI\main;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class main extends PluginBase implements Listener{

	public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $p = "MoneySystemTake";
        $d = "MoneySystemAPI";
        $this->getLogger()->notice("TestPluginの".$p."を読み込みました。");
        $this->Money = $this->getServer()->getPluginManager()->getPlugin("MoneySystemAPI");
            if(!file_exists($this->getDataFolder())){
    mkdir($this->getDataFolder(), 0755, true); 
    }   
    $this->config = new Config($this->getDataFolder() . "Config.yml", Config::YAML, [
    'Amount' => '100',
        ]);
        }
    public function onJoin(P $e){
    	$player = $e->getPlayer();
    	$name = $player->getName();
    	$minus = $this->config->get("Amount");
    	$this->Money->TakeMoney($name, $minus);
    }
}