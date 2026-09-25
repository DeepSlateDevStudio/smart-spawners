<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\projectile;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Arrow;
use pocketmine\math\RayTraceResult;

final class SlownessArrow extends Arrow{
    public function canSaveWithChunk(): bool{
        return false;
    }

    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
        parent::onHitEntity($entityHit, $hitResult);
        if($entityHit instanceof Living){
            $entityHit->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 600));
        }
    }
}
