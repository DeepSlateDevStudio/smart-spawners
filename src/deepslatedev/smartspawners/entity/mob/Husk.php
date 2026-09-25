<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Husk extends SmartMob{
    public static function mobKey(): string{
        return "husk";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:husk";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Husk";
    }
}
