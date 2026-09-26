<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;

final class Bee extends SmartMob{
    public static function mobKey(): string{
        return "bee";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:bee";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.6, 0.7);
    }

    public function getName(): string{
        return "Bee";
    }

    protected function canFly(): bool{
        return true;
    }

    protected function groupAnger(): bool{
        return true;
    }

    protected function temptItems(): array{
        return Shots::itemIds("dandelion", "poppy");
    }

    protected function onMeleeHit(Living $target): void{
        $target->getEffects()->add(new EffectInstance(VanillaEffects::POISON(), 200));
    }
}
