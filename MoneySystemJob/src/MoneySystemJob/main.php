<?php

namespace MoneySystemJob;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\Player;
use MoneySystemAPI\main;
use pocketmine\utils\TextFormat;

class main extends PluginBase implements Listener{
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
    if(!file_exists($this->getDataFolder())){
    mkdir($this->getDataFolder(), 0755, true); 
    }   
		@mkdir($this->getDataFolder());
		if(!is_file($this->getDataFolder()."jobs.yml")){
			$this->jobs = new Config($this->getDataFolder()."jobs.yml", Config::YAML, yaml_parse($this->readResource("jobs.yml")));
		}else{
    $this->jobs = new Config($this->getDataFolder() . "jobs.yml", Config::YAML, []);
}
    $this->player = new Config($this->getDataFolder() . "playerjobs.yml", Config::YAML, []);
    $this->getLogger()->notice("MoneySystemJobを読み込みました。　二次配布は禁止です。　製作者: metowa1227");
    $this->Money = $this->getServer()->getPluginManager()->getPlugin("MoneySystemAPI");
	}

	private function readResource($res){
		$path = $this->getFile()."resources/".$res;
		$resource = $this->getResource($res);
		if(!is_resource($resource)){
			$this->getLogger()->debug("Tried to load unknown resource ".TextFormat::AQUA.$res.TextFormat::RESET);
			return false;
		}
		$content = stream_get_contents($resource);
		@fclose($content);
		return $content;
	}

	public function onBlockBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
        $name = $player->getName();
		$job = $this->jobs->get($this->player->get($player->getName()));
		if($job !== false){
			if(isset($job[$block->getID().":".$block->getDamage().":break"])){
				$money = $job[$block->getID().":".$block->getDamage().":break"];
				if($money > 0){
					$this->Money->AddMoney($name,$money);
				}else{
					$this->Money->TakeMoney($name,$money);
				}
			}
		}
	}

	public function getJobs(){
		return $this->jobs->getAll();
	}

	public function getPlayers(){
		return $this->player->getAll();
	}
        public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        if (!$sender instanceof Player) return $sender->sendMessage("このコマンドはゲーム内で使用してください");
          $name = $sender->getName();
          $player = $sender->getPlayer();
		switch(array_shift($args)){
                       case "join":
				if($this->player->exists($sender->getName())){
					$sender->sendMessage(">>貴方はすでに職に就いています。");
					return true;
				}else{
              if(!isset($args[0])){
          $sender->sendMessage("§e>>仕事名を入力してください。");
          return true;
      }
  					$job = array_shift($args);
					if($this->jobs->exists($job)){
						$this->player->set($sender->getName(), $job);
						$sender->sendMessage(">>[INFO]>>".$job."に就きました。");
						return true;
					}else{
						$sender->sendMessage(">>".$job."という仕事は存在しません。");
						return true;
					}
				}
				break;
				case "me":
				if($this->player->exists($sender->getName())){
					$sender->sendMessage("貴方の仕事はこちらです。 : ".$this->player->get($sender->getName()));
					return true;
				}else{
					$sender->sendMessage("§c貴方は仕事に就いていません。");
					return true;
				}
				break;

			case "out":
				if($this->player->exists($sender->getName())){
					$job = $this->player->get($sender->getName());
					$this->player->remove($sender->getName());
					$sender->sendMessage("§a>>貴方は".$job."を辞職しました。");
					return true;
				}else{
					$sender->sendMessage("§c貴方は仕事に就いていません。");
					return true;
				}
				break;

			case "list":
			if(!isset($args[0])){
			$sender->sendMessage("§a[INFO]>>仕事一覧表<< 現在のページ page 1 / 3");
			$sender->sendMessage(">>仕事: 木こり");
			$sender->sendMessage("お金獲得ブロック:");
			$sender->sendMessage("17:0 オークの原木   17:1 ダークオークの原木");
			$sender->sendMessage("17:2 白樺の原木   17:3 ジャングルの原木");
			$sender->sendMessage("18:0 オークの葉　　　　18:1 ダークオークの葉");
			$sender->sendMessage("18:2 白樺の葉     18:3 ジャングルの葉");
			$sender->sendMessage("161:0 アカシアの葉  162:0 アカシアの原木");
			return true;
		}else{
		switch($args[0]){
			case "2":
			$sender->sendMessage("§a[INFO]>>仕事一覧表<< 現在のページ page 2 / 3");
			$sender->sendMessage(">>仕事: 石堀り");
			$sender->sendMessage("お金獲得ブロック:");
			$sender->sendMessage("1:0 石   4:0 丸石");
			return true;
			break;

			case "3":
			$sender->sendMessage("§a[INFO]>>仕事一覧表<< 現在のページ page 3 / 3");
			$sender->sendMessage(">>仕事: 鉱夫");
			$sender->sendMessage("お金獲得ブロック:");
			$sender->sendMessage("14:0 金の鉱石     15:0 鉄の鉱石");
			$sender->sendMessage("16:0 石炭の鉱石　　　21:0 ラピスラズリの鉱石");
			$sender->sendMessage("73:0 レッドストーンの鉱石   129:0 エメラルドの鉱石");
			return true;
			break;

			default:
				$sender->sendMessage("/list 1/2/3しか存在しません。");
				return true;
}
}
}
}
}