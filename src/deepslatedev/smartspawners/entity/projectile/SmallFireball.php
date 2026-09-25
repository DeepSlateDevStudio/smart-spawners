<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\projectile;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\RayTraceResult;

final class SmallFireball extends Projectile{
    public static function getNetworkTypeId(): string{
        return "minecraft:small_fireball";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.3125, 0.3125);
    }

    protected function getInitialGravity(): float{
        return 0.0;
    }

    protected function getInitialDragMultiplier(): float{
        return 0.0;
    }

    public function canSaveWithChunk(): bool{
        return false;
    }

    protected function entityBaseTick(int $tickDiff = 1): bool{
        if($this->ticksLived > 100){
            $this->flagForDespawn();
        }
        return parent::entityBaseTick($tickDiff);
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
        $owner = $this->getOwningEntity() ?? $this;
        $event = new EntityDamageByChildEntityEvent($owner, $this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, 5.0);
        $entityHit->attack($event);
        if(!$event->isCancelled()){
            $entityHit->setOnFire(5);
        }
        $this->flagForDespawn();
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
        $this->flagForDespawn();
    }
}
