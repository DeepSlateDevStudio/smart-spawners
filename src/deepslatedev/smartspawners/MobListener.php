<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\SyncActorPropertyPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

final class MobListener implements Listener{
    private const CLIMATE_TYPES = ["minecraft:cow", "minecraft:pig", "minecraft:chicken"];

    public function __construct(private Main $plugin){}

    public function onReceive(DataPacketReceiveEvent $event): void{
        $packet = $event->getPacket();
        if(!$packet instanceof PlayerAuthInputPacket){
            return;
        }
        $player = $event->getOrigin()->getPlayer();
        $vehicle = $player?->getVehicle();
        if($vehicle instanceof SmartMob){
            $vehicle->setRiderInput($packet->getMoveVecZ(), $packet->getMoveVecX(), $packet->getYaw(), $packet->getInputFlags()->get(PlayerAuthInputFlags::JUMPING));
        }
    }

    public function onSend(DataPacketSendEvent $event): void{
        if(!MobRegistry::climateVariants()){
            return;
        }
        foreach($event->getPackets() as $packet){
            if($packet instanceof StartGamePacket){
                foreach($event->getTargets() as $session){
                    $this->plugin->getScheduler()->scheduleTask(new ClosureTask(fn() => $this->sendProperties($session)));
                }
                return;
            }
        }
    }

    private function sendProperties(NetworkSession $session): void{
        if(!$session->isConnected()){
            return;
        }
        foreach(self::CLIMATE_TYPES as $type){
            $property = CompoundTag::create()
                ->setString("name", "minecraft:climate_variant")
                ->setInt("type", 3)
                ->setTag("enum", new ListTag([new StringTag("temperate"), new StringTag("warm"), new StringTag("cold")]));
            $nbt = CompoundTag::create()
                ->setString("type", $type)
                ->setTag("properties", new ListTag([$property]));
            $session->sendDataPacket(SyncActorPropertyPacket::create(new CacheableNbt($nbt)));
        }
    }

    public function onDamage(EntityDamageByEntityEvent $event): void{
        if($event->isCancelled()){
            return;
        }
        $victim = $event->getEntity();
        $damager = $event->getDamager();
        if($damager instanceof Player && $victim instanceof Living && !($victim instanceof SmartMob && $victim->isOwnedBy($damager))){
            $this->rallyWolves($damager, $victim);
        }elseif($victim instanceof Player && $damager instanceof Living){
            $this->rallyWolves($victim, $damager);
        }
    }

    private function rallyWolves(Player $owner, Living $target): void{
        $c = $owner->getPosition();
        foreach($owner->getWorld()->getNearbyEntities(new AxisAlignedBB($c->x - 24, $c->y - 8, $c->z - 24, $c->x + 24, $c->y + 8, $c->z + 24)) as $entity){
            if($entity instanceof SmartMob && $entity !== $target && $entity::mobKey() === "wolf" && $entity->isOwnedBy($owner)){
                $entity->becomeAngry($target->getId());
            }
        }
    }
}
