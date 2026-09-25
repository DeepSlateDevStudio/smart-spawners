<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Sheep extends SmartMob{
    public static function mobKey(): string{
        return "sheep";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:sheep";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.3, 0.9);
    }

    public function getName(): string{
        return "Sheep";
    }
}
