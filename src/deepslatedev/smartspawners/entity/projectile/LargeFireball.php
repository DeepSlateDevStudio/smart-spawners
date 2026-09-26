<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\projectile;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\RayTraceResult;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\world\sound\ExplodeSound;

final class LargeFireball extends Projectile{
    public static function getNetworkTypeId(): string{
        return "minecraft:fireball";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.0, 1.0);
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
        if($this->ticksLived > 160){
            $this->flagForDespawn();
        }
        return parent::entityBaseTick($tickDiff);
    }

    private function burst(): void{
        $world = $this->getWorld();
        $center = $this->location->asVector3();
        $world->addParticle($center, new HugeExplodeSeedParticle());
        $world->addSound($center, new ExplodeSound());
        $owner = $this->getOwningEntity() ?? $this;
        $radius = 2.5;
        foreach($world->getNearbyEntities(new AxisAlignedBB($center->x - $radius, $center->y - $radius, $center->z - $radius, $center->x + $radius, $center->y + $radius, $center->z + $radius), $this) as $entity){
            if(!$entity instanceof Living || $entity === $owner){
                continue;
            }
            $distance = $entity->getPosition()->distance($center);
            $damage = 6.0 * (1 - $distance / ($radius + 1));
            if($damage > 0){
                $event = new EntityDamageByChildEntityEvent($owner, $this, $entity, EntityDamageEvent::CAUSE_ENTITY_EXPLOSION, $damage);
                $entity->attack($event);
                if(!$event->isCancelled()){
                    $entity->setOnFire(3);
                }
            }
        }
        $this->flagForDespawn();
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
        $this->burst();
    }

    protected function onHitBlock(Block $blockHit, RayTraceResult $hitResult): void{
        $this->burst();
    }
}
