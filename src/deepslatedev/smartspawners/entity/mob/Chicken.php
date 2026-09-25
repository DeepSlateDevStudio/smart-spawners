<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Chicken extends SmartMob{
    public static function mobKey(): string{
        return "chicken";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:chicken";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.7, 0.4);
    }

    public function getName(): string{
        return "Chicken";
    }
}
