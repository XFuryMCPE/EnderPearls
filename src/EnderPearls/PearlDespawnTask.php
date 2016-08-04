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

	public function onRun($currentTick){
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
	}
}