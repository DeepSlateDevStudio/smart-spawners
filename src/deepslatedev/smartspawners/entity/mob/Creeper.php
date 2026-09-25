<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Creeper extends SmartMob{
    public static function mobKey(): string{
        return "creeper";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:creeper";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.7, 0.6);
    }

    public function getName(): string{
        return "Creeper";
    }
}
