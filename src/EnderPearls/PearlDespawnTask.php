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

use pocketmine\scheduler\PluginTask;
use pocketmine\entity\Item;

class PearlDespawnTask extends PluginTask{

	public function __construct(MainClass $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	/*public function onRun($currentTick){
		foreach($this->plugin->getServer()->getLevels() as $lvls){
			foreach($lvls->getEntities() as $ent){
				if($ent instanceof Item){
					if(isset($this->plugin->pearlLog[$ent->getId()])){
						if($ent->getLevel()->getBlockIdAt($ent->getX(),$ent->getY() - 1,$ent->getZ()) != 0){
							$ent->close();
						}
					}
				}
			}
		}
	}*/
	
	public function onRun($currentTick){
		foreach($this->plugin->getServer()->getLevels() as $lvls){
			foreach($lvls->getEntities() as $ent){
				if($ent instanceof Item){
					if(isset($this->plugin->pearlLog[$ent->getId()])){
						$b_below = $ent->getLevel()->getBlockIdAt($ent->getX(),$ent->getY() - 1,$ent->getZ());
						$b_side1 = $ent->getLevel()->getBlockIdAt($ent->getX() + 1,$ent->getY(),$ent->getZ());
						$b_side2 = $ent->getLevel()->getBlockIdAt($ent->getX() - 1,$ent->getY(),$ent->getZ());
						$b_side3 = $ent->getLevel()->getBlockIdAt($ent->getX(),$ent->getY(),$ent->getZ() + 1);
						$b_side4 = $ent->getLevel()->getBlockIdAt($ent->getX(),$ent->getY(),$ent->getZ() - 1);
						if($b_below != 0 || $b_side1 != 0 || $b_side2 != 0 || $b_side3 != 0 || $b_side4 != 0){
							$ent->close();
						}
					}
				}
			}
		}
	}
}
