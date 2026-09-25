<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Skeleton extends SmartMob{
    public static function mobKey(): string{
        return "skeleton";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:skeleton";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.99, 0.6);
    }

    public function getName(): string{
        return "Skeleton";
    }
}
