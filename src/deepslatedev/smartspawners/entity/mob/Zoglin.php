<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;

final class Zoglin extends SmartMob{
    public static function mobKey(): string{
        return "zoglin";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:zoglin";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.4, 1.4);
    }

    public function getName(): string{
        return "Zoglin";
    }

    protected function onMeleeHit(Living $target): void{
        $target->setMotion($target->getMotion()->withComponents(null, 0.5, null));
    }
}
