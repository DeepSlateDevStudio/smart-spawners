<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity;

use deepslatedev\smartspawners\Items;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\particle\AngryVillagerParticle;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\particle\SmokeParticle;

trait Domestic{
    protected bool $tamed = false;
    protected ?string $owner = null;
    protected bool $sitting = false;
    protected bool $saddled = false;
    protected bool $baby = false;
    protected int $babyTicks = 0;
    protected bool $persistent = false;
    protected int $loveTicks = 0;
    protected int $breedCooldown = 0;
    protected int $temper = 0;
    protected float $riderForward = 0.0;
    protected float $riderStrafe = 0.0;
    protected float $riderYaw = 0.0;
    protected bool $riderJump = false;

    protected function breedItems(): array{
        return $this->temptItems();
    }

    protected function canBreed(): bool{
        return count($this->breedItems()) > 0 && $this->def()["mode"] !== "hostile";
    }

    protected function breedNeedsTame(): bool{
        return false;
    }

    protected function tameItems(): array{
        return [];
    }

    protected function tameChance(): int{
        return 3;
    }

    protected function tameByRiding(): bool{
        return false;
    }

    protected function canSit(): bool{
        return false;
    }

    protected function saddleable(): bool{
        return false;
    }

    protected function saddleNeedsTame(): bool{
        return true;
    }

    protected function controlItem(): ?string{
        return null;
    }

    protected function rideable(): bool{
        return false;
    }

    protected function riderSpeed(): float{
        return 0.35;
    }

    protected function jumpPower(): float{
        return 0.5;
    }

    public function climateVariant(): bool{
        return false;
    }

    public function isTamed(): bool{
        return $this->tamed;
    }

    public function isOwnedBy(Player $player): bool{
        return $this->tamed && $this->owner !== null && $this->owner === strtolower($player->getName());
    }

    public function isBaby(): bool{
        return $this->baby;
    }

    protected function loadDomestic(CompoundTag $nbt): void{
        $this->tamed = $nbt->getByte("SSTamed", 0) === 1;
        $owner = $nbt->getString("SSOwner", "");
        $this->owner = $owner === "" ? null : $owner;
        $this->sitting = $nbt->getByte("SSSitting", 0) === 1;
        $this->saddled = $nbt->getByte("SSSaddled", 0) === 1;
        $this->baby = $nbt->getByte("SSBaby", 0) === 1;
        $this->babyTicks = $nbt->getInt("SSBabyTicks", $this->baby ? 24000 : 0);
        $this->persistent = $nbt->getByte("SSPersist", 0) === 1;
        if($this->baby){
            $this->setScale(0.5);
        }
    }

    protected function saveDomestic(CompoundTag $nbt): void{
        $nbt->setByte("SSTamed", $this->tamed ? 1 : 0);
        $nbt->setString("SSOwner", $this->owner ?? "");
        $nbt->setByte("SSSitting", $this->sitting ? 1 : 0);
        $nbt->setByte("SSSaddled", $this->saddled ? 1 : 0);
        $nbt->setByte("SSBaby", $this->baby ? 1 : 0);
        $nbt->setInt("SSBabyTicks", $this->babyTicks);
        $nbt->setByte("SSPersist", $this->persistent ? 1 : 0);
    }

    public function keepsWithWorld(): bool{
        return $this->tamed || $this->saddled || $this->persistent;
    }

    public function setRiderInput(float $forward, float $strafe, float $yaw, bool $jump): void{
        $this->riderForward = $forward;
        $this->riderStrafe = $strafe;
        $this->riderYaw = $yaw;
        $this->riderJump = $jump;
    }

    protected function driver(): ?Player{
        foreach($this->getPassengers() as $passenger){
            if($passenger instanceof Player){
                return $passenger;
            }
        }
        return null;
    }

    public function getSeatPosition(?Entity $passenger = null): Vector3{
        return new Vector3(0, $this->size->getHeight() * 0.85, 0);
    }

    protected function markDirty(): void{
        $this->networkPropertiesDirty = true;
    }

    protected function hearts(): void{
        for($i = 0; $i < 4; $i++){
            $this->getWorld()->addParticle($this->location->add(mt_rand(-5, 5) / 10, $this->size->getHeight() + mt_rand(0, 5) / 10, mt_rand(-5, 5) / 10), new HeartParticle());
        }
    }

    protected function consumeHand(Player $player): void{
        if(!$player->isCreative()){
            $hand = $player->getInventory()->getItemInHand();
            $player->getInventory()->setItemInHand($hand->setCount($hand->getCount() - 1));
        }
    }

    protected function holds(Player $player, array $typeIds): bool{
        return in_array($player->getInventory()->getItemInHand()->getTypeId(), $typeIds, true);
    }

    protected function domesticInteract(Player $player): bool{
        $hand = $player->getInventory()->getItemInHand();

        if(!$this->tamed && count($this->tameItems()) > 0 && $this->holds($player, $this->tameItems())){
            $this->consumeHand($player);
            if(mt_rand(1, $this->tameChance()) === 1){
                $this->tame($player);
            }else{
                for($i = 0; $i < 4; $i++){
                    $this->getWorld()->addParticle($this->location->add(mt_rand(-5, 5) / 10, $this->size->getHeight(), mt_rand(-5, 5) / 10), new SmokeParticle());
                }
            }
            return true;
        }

        if($this->holds($player, $this->breedItems()) && (!$this->breedNeedsTame() || $this->tamed)){
            if($this->baby){
                $this->consumeHand($player);
                $this->babyTicks = (int) ($this->babyTicks * 0.9);
                $this->hearts();
                return true;
            }
            if($this->canBreed() && $this->loveTicks <= 0 && $this->breedCooldown <= 0){
                $this->consumeHand($player);
                $this->loveTicks = 600;
                $this->persistent = true;
                $this->hearts();
                return true;
            }
        }

        if($this->saddleable() && !$this->saddled && !$this->baby && Items::isSaddle($hand) && (!$this->saddleNeedsTame() || $this->tamed)){
            $this->consumeHand($player);
            $this->saddled = true;
            $this->persistent = true;
            $this->markDirty();
            return true;
        }

        if($this->canSit() && $this->isOwnedBy($player) && !$this->rideable()){
            $this->sitting = !$this->sitting;
            $this->setMotion(new Vector3(0, $this->motion->y, 0));
            $this->markDirty();
            return true;
        }

        if($this->rideable() && !$this->baby && $this->driver() === null && $player->getVehicle() === null){
            $allowed = $this->tameByRiding() ? true : $this->saddled;
            if($allowed){
                $this->addPassenger($player);
                return true;
            }
        }
        return false;
    }

    protected function tame(Player $player): void{
        $this->tamed = true;
        $this->owner = strtolower($player->getName());
        $this->persistent = true;
        $this->angryAt = null;
        $this->markDirty();
        $this->hearts();
    }

    protected function domesticTick(int $tickDiff, array $def): bool{
        if($this->breedCooldown > 0){
            $this->breedCooldown -= $tickDiff;
        }
        if($this->baby){
            $this->babyTicks -= $tickDiff;
            if($this->babyTicks <= 0){
                $this->baby = false;
                $this->setScale(1.0);
                $this->markDirty();
            }
        }

        $driver = $this->driver();
        if($driver !== null){
            return $this->ridden($driver, $def);
        }

        if($this->loveTicks > 0){
            $this->loveTicks -= $tickDiff;
            if($this->age % 10 === 0){
                $this->hearts();
            }
            $partner = $this->findPartner();
            if($partner !== null){
                if($partner->getPosition()->distance($this->location) < 1.6){
                    $this->breedWith($partner);
                }else{
                    $this->walkToward($partner->getPosition(), (float) $def["speed"]);
                }
                return true;
            }
        }

        if($this->sitting){
            $this->setMotion(new Vector3(0, $this->motion->y, 0));
            return true;
        }

        if($this->tamed && $this->owner !== null && $this->angryAt === null){
            $owner = Server::getInstance()->getPlayerExact($this->owner);
            if($owner !== null && $owner->getWorld() === $this->getWorld() && !$this->rideable()){
                $distance = $owner->getPosition()->distance($this->location);
                if($distance > 14){
                    $this->teleport($owner->getPosition()->add(mt_rand(-1, 1), 0, mt_rand(-1, 1)));
                    return true;
                }
                if($distance > 4){
                    $this->walkToward($owner->getPosition(), (float) $def["speed"] * 1.2);
                    return true;
                }
            }
        }
        return false;
    }

    private function ridden(Player $driver, array $def): bool{
        if(!$this->tamed && $this->tameByRiding()){
            if($this->age % 40 === 0){
                $this->temper += mt_rand(0, 5);
                if(mt_rand(0, 99) < $this->temper){
                    $this->tame($driver);
                }elseif(mt_rand(1, 3) === 1){
                    $this->removePassenger($driver);
                    for($i = 0; $i < 4; $i++){
                        $this->getWorld()->addParticle($this->location->add(mt_rand(-5, 5) / 10, $this->size->getHeight(), mt_rand(-5, 5) / 10), new AngryVillagerParticle());
                    }
                }
            }
            $this->setMotion(new Vector3(mt_rand(-10, 10) / 60, $this->onGround && mt_rand(1, 20) === 1 ? 0.35 : $this->motion->y, mt_rand(-10, 10) / 60));
            return true;
        }
        $control = $this->controlItem();
        if($control !== null){
            if(!Items::holdsControl($driver, $control)){
                return false;
            }
            $yaw = $driver->getLocation()->yaw;
            $forward = 1.0;
            $strafe = 0.0;
        }else{
            if(!$this->saddled){
                $this->setMotion(new Vector3(0, $this->motion->y, 0));
                return true;
            }
            $yaw = $this->riderYaw;
            $forward = $this->riderForward;
            $strafe = $this->riderStrafe;
        }
        $rad = deg2rad($yaw);
        $speed = $this->riderSpeed();
        $x = (-sin($rad) * $forward + cos($rad) * $strafe) * $speed;
        $z = (cos($rad) * $forward + sin($rad) * $strafe) * $speed;
        $y = $this->motion->y;
        if($this->onGround && ($this->riderJump || $this->isCollidedHorizontally) && ($forward !== 0.0 || $strafe !== 0.0)){
            $y = $this->riderJump ? $this->jumpPower() : 0.42;
        }
        $this->setRotation($yaw, 0.0);
        $this->setMotion(new Vector3($x, $y, $z));
        return true;
    }

    private function findPartner(): ?self{
        $c = $this->location;
        foreach($this->getWorld()->getNearbyEntities(new AxisAlignedBB($c->x - 8, $c->y - 4, $c->z - 8, $c->x + 8, $c->y + 4, $c->z + 8), $this) as $entity){
            if($entity instanceof self && $entity::mobKey() === static::mobKey() && !$entity->baby && $entity->loveTicks > 0 && $entity->isAlive()){
                return $entity;
            }
        }
        return null;
    }

    private function breedWith(self $partner): void{
        $this->loveTicks = 0;
        $partner->loveTicks = 0;
        $this->breedCooldown = 6000;
        $partner->breedCooldown = 6000;
        $world = $this->getWorld();
        $nbt = CompoundTag::create()->setByte("SSBaby", 1)->setInt("SSBabyTicks", 24000)->setByte("SSPersist", 1);
        if($this->tamed && $this->owner !== null){
            $nbt->setByte("SSTamed", 1)->setString("SSOwner", $this->owner);
        }
        $baby = new static(Location::fromObject($this->location, $world, $this->location->yaw, 0.0), $nbt);
        $baby->spawnToAll();
        $this->hearts();
        (new ExperienceOrb(Location::fromObject($this->location->add(0, 0.5, 0), $world, 0.0, 0.0), mt_rand(1, 7)))->spawnToAll();
    }

    protected function domesticDrops(array $drops): array{
        if($this->baby){
            return [];
        }
        if($this->saddled && ($saddle = Items::saddle()) instanceof Item){
            $drops[] = $saddle;
        }
        return $drops;
    }
}
