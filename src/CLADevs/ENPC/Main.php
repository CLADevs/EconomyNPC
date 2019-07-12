<?php

declare(strict_types=1);

namespace CLADevs\ENPC;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector2;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener{

	public function onEnable() : void{
		Entity::registerEntity(EconomyNPCEntity::class, true);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();
		$from = $event->getFrom();
		$to = $event->getTo();
		if($from->distance($to) < 0.1) return;
		foreach($player->getLevel()->getNearbyEntities($player->getBoundingBox()->expandedCopy(10, 10, 10), $player) as $e){
			if($e instanceof EconomyNPCEntity){
				$pk = new MoveActorAbsolutePacket();
				$v = new Vector2($e->x, $e->z);
				$pk->entityRuntimeId = $e->getId();
				$pk->position = $e->asVector3()->add(0, 1.5, 0);
				$pk->zRot = ((atan2($player->z - $e->z, $player->x - $e->x) * 180) / M_PI) - 90;
				$pk->yRot = ((atan2($player->z - $e->z, $player->x - $e->x) * 180) / M_PI) - 90;
				$pk->xRot = ((atan2($v->distance($player->x, $player->z), $player->y - $e->y) * 180) / M_PI) - 90;
				$player->dataPacket($pk);
				$e->setRotation(((atan2($player->z - $e->z, $player->x - $e->x) * 180) / M_PI) - 90, ((atan2($v->distance($player->x, $player->z), $player->y - $e->y) * 180) / M_PI) - 90);
			}
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if(strtolower($command->getName()) === "enpc"){
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
					$nbt->setTag(new IntTag("position", intval($args[0])));
					$nbt->setTag(new StringTag("player", "0"));
					$npc = new EconomyNPCEntity($sender->getLevel(), $nbt);
					$npc->setNameTag(TextFormat::RED . "Unknown");
					$npc->setNameTagAlwaysVisible(true);
					$npc->spawnToAll();
					break;
				default:
					$sender->sendMessage(TextFormat::GRAY . "Usage: /enpc <1:2:3>");
					break;
			}
		}
		return true;
	}
}