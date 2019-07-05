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
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener{

	public function onEnable() : void{
		Entity::registerEntity(NPC::class, true);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("EconomyNPC has been enabled!");
	}

	public function onMove(PlayerMoveEvent $e): void{
		$p = $e->getPlayer();
		$from = $e->getFrom();
		$to = $e->getTo();
		$maxDistance = 30;

		if($from->distance($to) < 0.1) return;
		foreach($p->getLevel()->getNearbyEntities($p->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $p) as $en){
		    if($en instanceof Player) continue;

			$xdiff = $p->x - $en->x;
			$zdiff = $p->z - $en->z;
			$angle = atan2($zdiff, $xdiff);
			$yaw = (($angle * 180) / M_PI) - 90;
			$ydiff = $p->y - $en->y;
			$v = new Vector2($en->x, $en->z);
			$dist = $v->distance($p->x, $p->z);
			$angle = atan2($dist, $ydiff);
			$pitch = (($angle * 180) / M_PI) - 90;
			if($en instanceof NPC){
                $pk = new MovePlayerPacket();
                $pk->entityRuntimeId = $en->getId();
                $pk->position = $en->asVector3()->add(0, $en->getEyeHeight(), 0);
                $pk->yaw = $yaw;
                $pk->pitch = $pitch;
                $pk->headYaw = $yaw;
                $pk->onGround = $en->onGround;
                $p->dataPacket($pk);
            }
		}
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
