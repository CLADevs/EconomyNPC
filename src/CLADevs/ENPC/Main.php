<?php

declare(strict_types=1);

namespace CLADevs\ENPC;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\{
	IntTag, StringTag
};
use pocketmine\utils\TextFormat as C;
use pocketmine\command\{
	Command, CommandSender
};

class Main extends PluginBase{

	public function onEnable(): void{
		Entity::registerEntity(NPC::class, true);
		$this->getLogger()->info("Everything is ready!");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		if($sender->isOp() == false){
			$sender->sendMessage(C::RED . "You do not have pemission to use this command");
			return false;
		}
		if(count($args) < 1){
			$sender->sendMessage("Usage: /e <1:2:3>");
			return false;
		}

		switch($args[0]){
			case "1":
			case "2":
			case "3":
			$nbt = Entity::createBaseNBT($sender);
			$nbt->setTag($sender->namedtag->getTag("Skin"));
			$nbt->setTag(new IntTag("position", (int)$args[0]));
			$nbt->setTag(new StringTag("player", "0"));

			$npc = new NPC($sender->getLevel(), $nbt);
			$npc->setNameTag(C::RED . "Unknown");
			$npc->setNameTagAlwaysVisible(true);
			$npc->spawnToAll();
			break;
			default:
			$sender->sendMessage("Usage: /e <1:2:3>");
			break;
		}
		return true;
	}
}
