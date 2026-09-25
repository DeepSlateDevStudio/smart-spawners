<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Spider extends SmartMob{
    public static function mobKey(): string{
        return "spider";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:spider";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.9, 1.4);
    }

    public function getName(): string{
        return "Spider";
    }
}
