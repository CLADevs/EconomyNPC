<?php

declare(strict_types=1);

namespace CLADevs\ENPC;

use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class NPC extends Human{

	/** @var null|Plugin $eco */
	protected $eco;

	public function __construct(Level $lvl, CompoundTag $nbt){
		$this->eco = $lvl->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		parent::__construct($lvl, $nbt);
	}

	public function getPos() : int{
		return $this->namedtag->getInt("position");
	}

	public function getPlayer() : string{
		return $this->namedtag->getString("player");
	}

	public function entityBaseTick(int $tick = 1) : bool{
		if(count($this->eco->getAllMoney()) > 0){
			$moneys = $this->eco->getAllMoney();
			$i = 1;
			arsort($moneys);
			foreach($moneys as $name => $money){
				if($i < 4){
					if($i == $this->getPos()) $this->namedtag->setTag(new StringTag("player", $name));
					$i++;
				}
			}
			$this->setNameTag(TextFormat::GRAY . $this->getPos() . ". " . TextFormat::WHITE . $this->getPlayer() . ": " . TextFormat::YELLOW . $this->eco->myMoney($this->getPlayer()));
			#I fudged up skin xd help me by pull request
			/*$nbt = $this->getLevel()->getServer()->getOfflinePlayerData($this->getPlayer());
			$s = $nbt->getTag("Skin");
			$skin = new Skin($s->getString("Name"), $s->getByteArray("Data"), $s->getByteArray("CapeData"), $s->getString("GeometryName"), $s->getByteArray("GeometryData"));
			$s = $nbt->getTag("Skin");
			$this->namedtag->setTag($s);
			$this->spawnToAll();*/
		}else{
			$this->setNameTag(TextFormat::RED . "Unknown");
		}
		return parent::entityBaseTick($tick);
	}
}