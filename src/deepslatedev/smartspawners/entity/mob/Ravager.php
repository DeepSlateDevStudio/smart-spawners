<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;

final class Ravager extends SmartMob{
    public static function mobKey(): string{
        return "ravager";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:ravager";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(2.2, 1.95);
    }

    public function getName(): string{
        return "Ravager";
    }

    protected function onMeleeHit(Living $target): void{
        $target->setMotion($target->getMotion()->withComponents(null, 0.6, null));
    }
}
