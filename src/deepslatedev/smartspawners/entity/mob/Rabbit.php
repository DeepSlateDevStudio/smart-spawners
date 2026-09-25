<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Rabbit extends SmartMob{
    public static function mobKey(): string{
        return "rabbit";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:rabbit";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.5, 0.4);
    }

    public function getName(): string{
        return "Rabbit";
    }
}
