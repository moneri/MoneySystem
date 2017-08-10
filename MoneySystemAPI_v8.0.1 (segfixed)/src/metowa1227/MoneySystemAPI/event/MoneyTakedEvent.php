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

namespace metowa1227\MoneySystemAPI\event;

use pocketmine\Player;
use pocketmine\OfflinePlayer;

use metowa1227\MoneySystemAPI\MoneySystemAPI;
use metowa1227\MoneySystemAPI\event\MoneyTakedEvent;

class MoneyTakedEvent extends MoneySystemEvent{

	/**
	**Money Taked Event, if cancel to cancel take money.
	**/

	private $amount = 0;

	private $player = null;

	protected $type = null;

	public static $handlerList = null;

	public function __construct(MoneySystemAPI $main, $player, $amount, string $type, $issuer){
		parent::__construct($main, $issuer);
		$this->api = $main;
		$this->type = $type;
		$this->amount = $amount;
		if($player instanceof Player || $player instanceof OfflinePlayer){
			$this->player = $player;
		}
	}

	/**
	**@return Player object
	**/

	public function getPlayer(){
		return $this->player;
	}

	/**
	**@return string, taked type
	**/

	public function getType(){
		switch($this->type){
			case "takemoney.command":
				return "takemoney.command";
			case "pay.command":
				return "pay.command";
			case "other.plugin":
				return "other.plugin";
			default:
				throw new \Exception("Event type is undefined type");
			break;
		}
	}

	/**
	**@return int, amount
	**/

	public function getAmount(){
		return $this->amount;
	}

/*

	public function setCancelled($type = true){
		$this->api->setCancel("MoneyTakedEvent", $this->player->getName(), $type);
	}

	public function isCancelled(){
		$result = $this->api->isCancel("MoneyTakedEvent", $this->player->getName());
		return $result;
	}

*/

}