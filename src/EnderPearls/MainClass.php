<?php

/*    ___                 
 *   / __\   _ _ __ _   _ 
 *  / _\| | | | '__| | | |
 * / /  | |_| | |  | |_| |
 * \/    \__,_|_|   \__, |
 *                  |___/
 *
 * No copyright 2016 blahblah
 * Plugin made by fury and is FREE SOFTWARE
 * Do not sell or i will sue you lol
 * but fr tho I will sue ur face
 * DO NOT SELL
 */

namespace EnderPearls;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\entity\EntityDespawnEvent;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\ByteTag;

use pocketmine\entity\Entity;

use pocketmine\Player;

use pocketmine\level\sound\EndermanTeleportSound;

use pocketmine\item\Item;

class MainClass extends PluginBase implements Listener{

	public $pearlLog = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new PearlDespawnTask($this),1);
		@mkdir($this->getDataFolder());
		if(!file_exists($this->getDataFolder() . "config.yml")){
			$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
				"enderpearl-sound" => true,
				"enderpearl-id" => 378,
				"change-item-name" => true,
				"change-name-to" => "&dEnderpearl",
				"use-damage" => 4
			]);
		}
		else{
			$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		}
	}

	public function onInteract(PlayerInteractEvent $e){
		$p = $e->getPlayer();
		$i = $e->getItem();
		if($i->getId() == $this->config->get("enderpearl-id")){
			$nbt = new CompoundTag ("",[ 
				"Pos" => new ListTag ("Pos",[ 
					new DoubleTag ("", $p->getX()),
					new DoubleTag ("", $p->getY() + $p->getEyeHeight ()),
					new DoubleTag ("", $p->getZ()) 
				] ),
				"Motion" => new ListTag ("Motion",[ 
					new DoubleTag("", - \sin($p->yaw / 180 * M_PI)*\cos($p->pitch / 180 * M_PI)),
					new DoubleTag("", - \sin ( $p->pitch / 180 * M_PI ) ),
					new DoubleTag("",\cos($p->yaw / 180 * M_PI)*\cos($p->pitch / 180 * M_PI )) 
				]),
				"Rotation" => new ListTag("Rotation",[ 
					new FloatTag("",$p->yaw),
					new FloatTag("",$p->pitch) 
				]),
				"Health" => new ShortTag("Health", 5),
				"Item" => new CompoundTag("Item", [
					"id" => new ShortTag("id", $i->getId()),
					"Damage" => new ShortTag("Damage", $i->getDamage()),
					"Count" => new ByteTag("Count", 1)
				]),
				"PickupDelay" => new ShortTag("PickupDelay", "1") 
			]);
			$f = 1;
			$ep = Entity::createEntity("Item",$p->chunk,$nbt,$p);
			$ep->setMotion($ep->getMotion()->multiply($f));
			$ep->spawnToAll();
			$p->getInventory()->removeItem(Item::get($i->getId(),$i->getDamage(),1));
			$this->pearlLog[$ep->getId()] = $p->getName();
		}
	}

	public function onDespawn(EntityDespawnEvent $e){
		$ent = $e->getEntity();
		if(isset($this->pearlLog[$ent->getId()])){
			$ea = $this->pearlLog[$ent->getId()];
			$player = $this->getServer()->getPlayer($ea);
			if($player instanceof Player){
				$player->teleport($ent);
				$player->setHealth($player->getHealth() - $this->config->get("use-damage"));
				if($this->config->get("enderpearl-sound") == true){
					$player->getLevel()->addSound(new EndermanTeleportSound($player));
				}
			}
			unset($this->pearlLog[$ent->getId()]);
		}
	}

	public function onHeld(PlayerItemHeldEvent $e){
		$p = $e->getPlayer();
		$i = $e->getItem();
		if($i->getId() == $this->config->get("enderpearl-id") && $this->config->get("change-item-name") == true){
			$customname = $this->translateColors($this->config->get("change-name-to"));
			$p->sendPopup($customname);
		}
	}

	public function translateColors($string){
		$msg = str_replace("&1",TextFormat::DARK_BLUE,$string);
		$msg = str_replace("&2",TextFormat::DARK_GREEN,$msg);
		$msg = str_replace("&3",TextFormat::DARK_AQUA,$msg);
		$msg = str_replace("&4",TextFormat::DARK_RED,$msg);
		$msg = str_replace("&5",TextFormat::DARK_PURPLE,$msg);
		$msg = str_replace("&6",TextFormat::GOLD,$msg);
		$msg = str_replace("&7",TextFormat::GRAY,$msg);
		$msg = str_replace("&8",TextFormat::DARK_GRAY,$msg);
		$msg = str_replace("&9",TextFormat::BLUE,$msg);
		$msg = str_replace("&0",TextFormat::BLACK,$msg);
		$msg = str_replace("&a",TextFormat::GREEN,$msg);
		$msg = str_replace("&b",TextFormat::AQUA,$msg);
		$msg = str_replace("&c",TextFormat::RED,$msg);
		$msg = str_replace("&d",TextFormat::LIGHT_PURPLE,$msg);
		$msg = str_replace("&e",TextFormat::YELLOW,$msg);
		$msg = str_replace("&f",TextFormat::WHITE,$msg);
		$msg = str_replace("&o",TextFormat::ITALIC,$msg);
		$msg = str_replace("&l",TextFormat::BOLD,$msg);
		$msg = str_replace("&r",TextFormat::RESET,$msg);
		return $msg;
	}

}