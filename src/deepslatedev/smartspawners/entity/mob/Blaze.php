<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

final class Blaze extends SmartMob{
    public static function mobKey(): string{
        return "blaze";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:blaze";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.8, 0.6);
    }

    public function getName(): string{
        return "Blaze";
    }

    private int $burst = 0;
    private bool $charging = false;

    protected function getInitialGravity(): float{
        return 0.0;
    }

    public function isFireProof(): bool{
        return true;
    }

    protected function immuneTo(int $cause): bool{
        return in_array($cause, [EntityDamageEvent::CAUSE_FIRE, EntityDamageEvent::CAUSE_FIRE_TICK, EntityDamageEvent::CAUSE_LAVA, EntityDamageEvent::CAUSE_FALL], true);
    }

    protected function syncNetworkData(EntityMetadataCollection $properties): void{
        parent::syncNetworkData($properties);
        $properties->setGenericFlag(EntityMetadataFlags::ONFIRE, $this->charging);
    }

    private function setCharging(bool $charging): void{
        if($this->charging !== $charging){
            $this->charging = $charging;
            $this->networkPropertiesDirty = true;
        }
    }

    protected function rangedRange(): float{
        return 24.0;
    }

    protected function shoot(Living $target): int{
        if($this->burst === 0){
            $this->burst = 3;
            $this->setCharging(true);
            return 20;
        }
        Shots::fireball($this, $target);
        $this->burst--;
        if($this->burst > 0){
            return 6;
        }
        $this->setCharging(false);
        return 100;
    }

    protected function engage(Living $target, array $def): void{
        $this->lookAt($target->getEyePos());
        $distance = $target->getPosition()->distance($this->location);
        $desiredY = $target->getPosition()->y + 2.0 + sin($this->age / 20) * 0.5;
        $vertical = max(-0.15, min(0.15, ($desiredY - $this->location->y) * 0.1));
        $flat = $target->getPosition()->subtractVector($this->location)->withComponents(null, 0, null);
        $horizontal = new Vector3(0, 0, 0);
        if($distance > 10){
            $horizontal = $flat->normalize()->multiply((float) $def["speed"]);
        }elseif($distance < 4){
            $horizontal = $flat->normalize()->multiply(-(float) $def["speed"]);
        }
        $this->setMotion(new Vector3($horizontal->x, $vertical, $horizontal->z));
        if($distance <= $this->rangedRange() && $this->rangedCooldown === 0){
            $this->rangedCooldown = $this->shoot($target);
        }
    }

    protected function idle(array $def): void{
        if($this->burst > 0 || $this->charging){
            $this->burst = 0;
            $this->setCharging(false);
        }
        parent::idle($def);
        $vertical = $this->onGround ? 0.0 : -0.04;
        if($this->age % 90 === 0){
            $vertical = 0.3;
        }
        $this->setMotion($this->motion->withComponents(null, $vertical, null));
    }

    protected function extraTick(int $tickDiff): void{
        if($this->isUnderwater() && $this->age % 20 === 0){
            $this->attack(new EntityDamageEvent($this, EntityDamageEvent::CAUSE_DROWNING, 1.0));
        }
    }
}
