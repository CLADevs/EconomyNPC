<?php

declare(strict_types=1);

namespace CLADevs\ENPC;

use pocketmine\level\Level;
use pocketmine\utils\TextFormat as C;
use pocketmine\nbt\tag\{
	CompoundTag, StringTag
};
use pocketmine\entity\{
	Human, Skin
};

class NPC extends Human{

	protected $eco;

	public function __construct(Level $lvl, CompoundTag $nbt){
		$this->eco = $lvl->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		parent::__construct($lvl, $nbt);
	}

	public function getPos(): int{
		return $this->namedtag->getInt("position");
	}

	public function getPlayer(): string{
		return $this->namedtag->getString("player");
	}

	public function Data(): void{
		$moneys = $this->eco->getAllMoney();
		$i = 1;
		arsort($moneys);

		foreach($moneys as $name => $money){
			if($i < 4){
				if($i == $this->getPos()) $this->namedtag->setTag(new StringTag("player", $name));
				$i++;
			}
		}
	}

	public function entityBaseTick(int $tick = 1): bool{
		$base = parent::entityBaseTick($tick);

		if(count($this->eco->getAllMoney()) > 0){
			$this->Data();
			$this->setNameTag(C::GRAY . $this->getPos() . ". " . C::WHITE . $this->getPlayer() . ": " . C::YELLOW . $this->eco->myMoney($this->getPlayer()));

			#i fudged up skin xd help me by pull request
			// $nbt = $this->getLevel()->getServer()->getOfflinePlayerData($this->getPlayer());
			// $s = $nbt->getTag("Skin");
			// $skin = new Skin($s->getString("Name"), $s->getByteArray("Data"), $s->getByteArray("CapeData"), $s->getString("GeometryName"), $s->getByteArray("GeometryData"));
			// $s = $nbt->getTag("Skin");
			// $this->namedtag->setTag($s);
			// $this->spawnToAll();
		}else{
			$this->setNameTag(C::RED . "Unknown");
		}
		return $base;
	}
}