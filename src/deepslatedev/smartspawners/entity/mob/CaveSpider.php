<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class CaveSpider extends SmartMob{
    public static function mobKey(): string{
        return "cave_spider";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:cave_spider";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.5, 0.7);
    }

    public function getName(): string{
        return "Cave Spider";
    }
}
