<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity;

use deepslatedev\smartspawners\MobRegistry;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\player\GameMode;
use pocketmine\player\Player;

abstract class SmartMob extends Living{
    protected ?Vector3 $anchor = null;
    protected ?Vector3 $wanderTarget = null;
    protected int $wanderCooldown = 0;
    protected int $attackCooldown = 0;
    protected int $rangedCooldown = 0;
    protected int $fleeTicks = 0;
    protected ?Vector3 $fleeFrom = null;
    protected ?int $angryAt = null;
    protected int $lonelyTicks = 0;
    protected int $age = 0;

    abstract public static function mobKey(): string;

    protected function def(): array{
        return MobRegistry::get(static::mobKey());
    }

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $def = $this->def();
        $this->setMaxHealth(max(1, (int) $def["health"]));
        $this->setHealth((float) $this->getMaxHealth());
        $this->setStepHeight(1.0);
        $this->anchor = $this->location->asVector3();
        $this->wanderCooldown = mt_rand(20, 80);
        $this->rangedCooldown = mt_rand(20, 40);
    }

    public function canSaveWithChunk(): bool{
        return false;
    }

    public function heldItem(): ?Item{
        return null;
    }

    protected function sendSpawnPacket(Player $player): void{
        parent::sendSpawnPacket($player);
        $item = $this->heldItem();
        if($item !== null){
            $session = $player->getNetworkSession();
            $stack = $session->getTypeConverter()->coreItemStackToNet($item);
            $session->sendDataPacket(MobEquipmentPacket::create($this->getId(), new ItemStackWrapper(0, $stack), 0, 0, ContainerIds::INVENTORY));
        }
    }

    public function getDrops(): array{
        $drops = [];
        foreach($this->def()["drops"] as [$id, $min, $max]){
            $item = StringToItemParser::getInstance()->parse($id);
            $count = mt_rand($min, $max);
            if($item instanceof Item && $count > 0){
                $drops[] = $item->setCount($count);
            }
        }
        return $drops;
    }

    public function getXpDropAmount(): int{
        return $this->lastDamageCause instanceof EntityDamageByEntityEvent && $this->lastDamageCause->getDamager() instanceof Player ? (int) $this->def()["xp"] : 0;
    }

    protected function immuneTo(int $cause): bool{
        return false;
    }

    public function attack(EntityDamageEvent $source): void{
        if($this->immuneTo($source->getCause())){
            $source->cancel();
        }
        parent::attack($source);
        if($source->isCancelled() || !$source instanceof EntityDamageByEntityEvent){
            return;
        }
        $damager = $source->getDamager();
        if($damager !== null){
            $this->onHurtBy($damager);
        }
    }

    protected function onHurtBy(Entity $damager): void{
        if($this->def()["mode"] === "passive"){
            $this->fleeTicks = 80;
            $this->fleeFrom = $damager->getPosition()->asVector3();
        }elseif($damager instanceof Living && !($damager instanceof Player && !$this->validPlayer($damager))){
            $this->angryAt = $damager->getId();
        }
    }

    protected function canFly(): bool{
        return false;
    }

    protected function burnsInDaylight(): bool{
        return false;
    }

    protected function isHostileNow(): bool{
        return $this->def()["mode"] === "hostile";
    }

    protected function rangedRange(): float{
        return 0.0;
    }

    protected function preferredDistance(): float{
        return 0.0;
    }

    protected function shoot(Living $target): int{
        return 40;
    }

    protected function onMeleeHit(Living $target): void{
    }

    protected function temptItems(): array{
        return [];
    }

    protected function extraTick(int $tickDiff): void{
    }

    protected function monsterTargets(): bool{
        return false;
    }

    protected function entityBaseTick(int $tickDiff = 1): bool{
        $update = parent::entityBaseTick($tickDiff);
        if(!$this->isAlive() || $this->isClosed() || $this->isFlaggedForDespawn()){
            return $update;
        }
        $this->age += $tickDiff;
        $this->think($tickDiff);
        if(!$this->isClosed() && !$this->isFlaggedForDespawn()){
            $this->extraTick($tickDiff);
        }
        return true;
    }

    protected function think(int $tickDiff): void{
        $def = $this->def();
        $this->attackCooldown = max(0, $this->attackCooldown - $tickDiff);
        $this->rangedCooldown = max(0, $this->rangedCooldown - $tickDiff);
        if($this->burnsInDaylight() && $this->age % 20 === 0 && $this->inSunlight()){
            $this->setOnFire(8);
        }
        $nearest = $this->nearestPlayer(64.0);
        if($nearest === null){
            $this->lonelyTicks += $tickDiff;
            if($def["despawn-seconds"] > 0 && $this->lonelyTicks > $def["despawn-seconds"] * 20){
                $this->flagForDespawn();
            }
            return;
        }
        $this->lonelyTicks = 0;

        if($this->fleeTicks > 0 && $this->fleeFrom !== null){
            $this->fleeTicks -= $tickDiff;
            $away = $this->location->subtractVector($this->fleeFrom)->withComponents(null, 0, null);
            if($away->lengthSquared() < 0.01){
                $away = new Vector3(mt_rand(-10, 10) / 10, 0, mt_rand(-10, 10) / 10);
            }
            $this->walkToward($this->location->addVector($away->normalize()->multiply(4)), (float) $def["speed"] * 1.8);
            return;
        }

        $target = $this->currentTarget($def);
        if($target !== null){
            $this->engage($target, $def);
            return;
        }
        if($this->followTempter($def)){
            return;
        }
        $this->idle($def);
    }

    protected function inSunlight(): bool{
        $world = $this->getWorld();
        $time = $world->getTime() % 24000;
        if($time > 12500 || $this->isUnderwater() || $this->isOnFire()){
            return false;
        }
        $pos = $this->location->floor();
        return $world->getPotentialBlockSkyLightAt($pos->x, $pos->y + 1, $pos->z) >= 15;
    }

    protected function currentTarget(array $def): ?Living{
        $range = (float) $def["follow-range"];
        if($this->angryAt !== null){
            $angry = $this->getWorld()->getEntity($this->angryAt);
            if($angry instanceof Living && $angry->isAlive() && !$angry->isClosed() && $angry->getWorld() === $this->getWorld()
                && $angry->getPosition()->distanceSquared($this->location) <= ($range * 2) ** 2
                && (!$angry instanceof Player || $this->validPlayer($angry))){
                return $angry;
            }
            $this->angryAt = null;
        }
        if($this->monsterTargets()){
            $monster = $this->nearestMonster($range);
            if($monster !== null){
                return $monster;
            }
        }
        if(!$this->isHostileNow()){
            return null;
        }
        $player = $this->nearestPlayer($range);
        return $player !== null && $this->validPlayer($player) ? $player : null;
    }

    protected function validPlayer(Player $player): bool{
        return $player->isAlive() && $player->isConnected() && $player->getWorld() === $this->getWorld()
            && ($player->getGamemode() === GameMode::SURVIVAL || $player->getGamemode() === GameMode::ADVENTURE);
    }

    protected function nearestPlayer(float $range): ?Player{
        $best = null;
        $bestDistance = $range * $range;
        foreach($this->getWorld()->getPlayers() as $player){
            $distance = $player->getPosition()->distanceSquared($this->location);
            if($distance <= $bestDistance){
                $best = $player;
                $bestDistance = $distance;
            }
        }
        return $best;
    }

    protected function nearestMonster(float $range): ?Living{
        $best = null;
        $bestDistance = $range * $range;
        $box = new AxisAlignedBB($this->location->x - $range, $this->location->y - 4, $this->location->z - $range, $this->location->x + $range, $this->location->y + 4, $this->location->z + $range);
        foreach($this->getWorld()->getNearbyEntities($box, $this) as $entity){
            if($entity instanceof SmartMob && $entity->def()["mode"] === "hostile" && $entity->isAlive()){
                $distance = $entity->getPosition()->distanceSquared($this->location);
                if($distance < $bestDistance){
                    $best = $entity;
                    $bestDistance = $distance;
                }
            }
        }
        return $best;
    }

    protected function engage(Living $target, array $def): void{
        $distance = $target->getPosition()->distance($this->location);
        $this->lookAt($target->getEyePos());
        $range = $this->rangedRange();
        if($range > 0){
            $keep = $this->preferredDistance();
            if($distance > $range * 0.8){
                $this->walkToward($target->getPosition(), (float) $def["speed"]);
            }elseif($distance < $keep){
                $away = $this->location->subtractVector($target->getPosition())->withComponents(null, 0, null);
                $this->walkToward($this->location->addVector($away->normalize()->multiply(3)), (float) $def["speed"]);
            }else{
                $this->strafe($target, (float) $def["speed"] * 0.5);
            }
            if($distance <= $range && $this->rangedCooldown === 0){
                $this->rangedCooldown = $this->shoot($target);
            }
            return;
        }
        $reach = max(1.6, $this->size->getWidth() / 2 + 1.2);
        if($distance > $reach - 0.3){
            $this->walkToward($target->getPosition(), (float) $def["speed"]);
        }else{
            $this->setMotion($this->motion->withComponents(0, null, 0));
        }
        if($distance <= $reach && $this->attackCooldown === 0){
            $this->attackCooldown = 20;
            $this->broadcastAnimation(new ArmSwingAnimation($this));
            $event = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, (float) $def["damage"]);
            $target->attack($event);
            if(!$event->isCancelled()){
                $this->onMeleeHit($target);
            }
        }
    }

    protected function strafe(Living $target, float $speed): void{
        $side = $this->location->subtractVector($target->getPosition())->withComponents(null, 0, null)->normalize();
        $dir = (int) (($this->age / 60) % 2) === 0 ? 1 : -1;
        $this->walkToward($this->location->add(-$side->z * 2 * $dir, 0, $side->x * 2 * $dir), $speed);
    }

    protected function followTempter(array $def): bool{
        $items = $this->temptItems();
        if(count($items) === 0){
            return false;
        }
        $player = $this->nearestPlayer(10.0);
        if($player === null || !in_array($player->getInventory()->getItemInHand()->getTypeId(), $items, true)){
            return false;
        }
        $this->lookAt($player->getEyePos());
        if($player->getPosition()->distance($this->location) > 2.2){
            $this->walkToward($player->getPosition(), (float) $def["speed"] * 1.1);
        }else{
            $this->setMotion($this->motion->withComponents(0, null, 0));
        }
        return true;
    }

    protected function idle(array $def): void{
        if($this->wanderTarget !== null){
            if($this->location->withComponents(null, $this->wanderTarget->y, null)->distanceSquared($this->wanderTarget) < 1.0 || --$this->wanderCooldown < -120){
                $this->wanderTarget = null;
                $this->wanderCooldown = mt_rand(60, 160);
            }else{
                $this->walkToward($this->wanderTarget, (float) $def["speed"] * 0.6);
                return;
            }
        }
        $this->setMotion($this->motion->withComponents($this->motion->x * 0.5, null, $this->motion->z * 0.5));
        if(--$this->wanderCooldown > 0){
            return;
        }
        $anchor = $this->anchor ?? $this->location->asVector3();
        $leash = (int) $def["leash"];
        $this->wanderTarget = $anchor->add(mt_rand(-$leash, $leash), 0, mt_rand(-$leash, $leash));
        $this->wanderCooldown = 0;
    }

    protected function walkToward(Vector3 $target, float $speed): void{
        $dx = $target->x - $this->location->x;
        $dz = $target->z - $this->location->z;
        $length = sqrt($dx * $dx + $dz * $dz);
        if($length < 0.05){
            return;
        }
        $this->setRotation(rad2deg(atan2(-$dx, $dz)), $this->location->pitch);
        $motionY = $this->motion->y;
        if($this->isCollidedHorizontally && $this->onGround){
            $motionY = 0.42;
        }
        if($this->isUnderwater()){
            $motionY = max($motionY, 0.12);
        }
        $this->setMotion(new Vector3($dx / $length * $speed, $motionY, $dz / $length * $speed));
    }
}
