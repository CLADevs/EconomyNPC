<?php

declare(strict_types=1);

namespace CLADevs\ENPC;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\utils\TextFormat;

class Main extends PluginBase{

	public function onEnable() : void{
		Entity::registerEntity(NPC::class, true);
		$this->getLogger()->info("EconomyNPC has been enabled!");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(!$sender->isOp()){
			$sender->sendMessage(TextFormat::RED . "You do not have permission to use this command");
			return false;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "Use this command in-game");
			return false;
		}
		if(count($args) < 1){
			$sender->sendMessage(TextFormat::GRAY . "Usage: /enpc <1:2:3>");
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
				$npc->setNameTag(TextFormat::RED . "Unknown");
				$npc->setNameTagAlwaysVisible(true);
				$npc->spawnToAll();
				break;
			default:
				$sender->sendMessage(TextFormat::GRAY . "Usage: /enpc <1:2:3>");
				break;
		}
		return true;
	}
}
