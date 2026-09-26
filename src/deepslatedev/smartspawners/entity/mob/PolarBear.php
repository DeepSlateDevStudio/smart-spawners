<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class PolarBear extends SmartMob{
    public static function mobKey(): string{
        return "polar_bear";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:polar_bear";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.4, 1.3);
    }

    public function getName(): string{
        return "Polar Bear";
    }
}
