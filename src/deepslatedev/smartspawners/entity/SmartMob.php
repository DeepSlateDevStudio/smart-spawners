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
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\world\sound\ExplodeSound;

abstract class SmartMob extends Living{
    private const FUSE = 30;

    private ?Vector3 $anchor = null;
    private ?Vector3 $wanderTarget = null;
    private int $wanderCooldown = 0;
    private int $attackCooldown = 0;
    private int $fleeTicks = 0;
    private ?Vector3 $fleeFrom = null;
    private ?int $angryAt = null;
    private int $lonelyTicks = 0;
    private int $fuse = -1;

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
    }

    public function canSaveWithChunk(): bool{
        return false;
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

    public function attack(EntityDamageEvent $source): void{
        parent::attack($source);
        if($source->isCancelled() || !$source instanceof EntityDamageByEntityEvent){
            return;
        }
        $damager = $source->getDamager();
        if($damager === null){
            return;
        }
        $mode = $this->def()["mode"];
        if($mode === "passive"){
            $this->fleeTicks = 60;
            $this->fleeFrom = $damager->getPosition()->asVector3();
        }elseif($damager instanceof Player){
            $this->angryAt = $damager->getId();
        }
    }

    protected function entityBaseTick(int $tickDiff = 1): bool{
        $update = parent::entityBaseTick($tickDiff);
        if(!$this->isAlive() || $this->isClosed() || $this->isFlaggedForDespawn()){
            return $update;
        }
        $this->think($tickDiff);
        return true;
    }

    private function think(int $tickDiff): void{
        $def = $this->def();
        $this->attackCooldown = max(0, $this->attackCooldown - $tickDiff);
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
            $away = $this->location->subtractVector($this->fleeFrom);
            $this->walkToward($this->location->addVector($away->withComponents(null, 0, null)->normalize()->multiply(4)), $def["speed"] * 1.6);
            return;
        }

        $target = $this->currentTarget($def);
        if($target !== null){
            $this->chase($target, $def);
            return;
        }
        $this->resetFuse();
        $this->wander($def);
    }

    private function currentTarget(array $def): ?Player{
        $range = (float) $def["follow-range"];
        if($this->angryAt !== null){
            $angry = $this->getWorld()->getEntity($this->angryAt);
            if($angry instanceof Player && $this->validTarget($angry, $range * 1.5)){
                return $angry;
            }
            $this->angryAt = null;
        }
        if($def["mode"] !== "hostile"){
            return null;
        }
        $player = $this->nearestPlayer($range);
        return $player !== null && $this->validTarget($player, $range) ? $player : null;
    }

    private function validTarget(Player $player, float $range): bool{
        return $player->isAlive() && $player->isConnected() && $player->getWorld() === $this->getWorld()
            && ($player->getGamemode() === GameMode::SURVIVAL || $player->getGamemode() === GameMode::ADVENTURE)
            && $player->getPosition()->distanceSquared($this->location) <= $range * $range;
    }

    private function nearestPlayer(float $range): ?Player{
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

    private function chase(Player $target, array $def): void{
        $distance = $target->getPosition()->distance($this->location);
        $this->lookAt($target->getEyePos());
        if($def["explodes"]){
            $this->creeperLogic($target, $distance, $def);
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
            $target->attack(new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, (float) $def["damage"]));
        }
    }

    private function creeperLogic(Player $target, float $distance, array $def): void{
        if($distance < 3.0 || ($this->fuse >= 0 && $distance < 7.0)){
            if($this->fuse < 0){
                $this->fuse = 0;
                $this->getNetworkProperties()->setInt(EntityMetadataProperties::FUSE_LENGTH, self::FUSE);
                $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::IGNITED, true);
            }
            $this->setMotion($this->motion->withComponents(0, null, 0));
            if(++$this->fuse >= self::FUSE){
                $this->explode((float) $def["damage"]);
            }
            return;
        }
        $this->resetFuse();
        $this->walkToward($target->getPosition(), (float) $def["speed"]);
    }

    private function resetFuse(): void{
        if($this->fuse >= 0){
            $this->fuse = -1;
            $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::IGNITED, false);
        }
    }

    private function explode(float $power): void{
        $world = $this->getWorld();
        $center = $this->location->asVector3();
        $world->addParticle($center, new HugeExplodeSeedParticle());
        $world->addSound($center, new ExplodeSound());
        $radius = 4.0;
        foreach($world->getNearbyEntities(new AxisAlignedBB($center->x - $radius, $center->y - $radius, $center->z - $radius, $center->x + $radius, $center->y + $radius, $center->z + $radius), $this) as $entity){
            if(!$entity instanceof Living){
                continue;
            }
            $distance = $entity->getPosition()->distance($center);
            if($distance > $radius){
                continue;
            }
            $damage = $power * (1 - $distance / $radius);
            if($damage > 0){
                $entity->attack(new EntityDamageByEntityEvent($this, $entity, EntityDamageEvent::CAUSE_ENTITY_EXPLOSION, $damage));
            }
        }
        $this->flagForDespawn();
    }

    private function wander(array $def): void{
        if($this->wanderTarget !== null){
            if($this->location->distanceSquared($this->wanderTarget) < 1.0 || --$this->wanderCooldown < -120){
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
        $leash = (float) $def["leash"];
        $this->wanderTarget = $anchor->add(mt_rand((int) -$leash, (int) $leash), 0, mt_rand((int) -$leash, (int) $leash));
        $this->wanderCooldown = 0;
    }

    private function walkToward(Vector3 $target, float $speed): void{
        $dx = $target->x - $this->location->x;
        $dz = $target->z - $this->location->z;
        $length = sqrt($dx * $dx + $dz * $dz);
        if($length < 0.05){
            return;
        }
        $yaw = rad2deg(atan2(-$dx, $dz));
        $this->setRotation($yaw, $this->location->pitch);
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
