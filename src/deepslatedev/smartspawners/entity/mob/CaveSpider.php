<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;

final class CaveSpider extends Spider{
    public static function mobKey(): string{
        return "cave_spider";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:cave_spider";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.5, 0.7);
    }

    public function getName(): string{
        return "Cave Spider";
    }

    protected function onMeleeHit(Living $target): void{
        $target->getEffects()->add(new EffectInstance(VanillaEffects::POISON(), 140));
    }
}
