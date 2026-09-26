<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class TropicalFish extends SmartMob{
    public static function mobKey(): string{
        return "tropical_fish";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:tropicalfish";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.4, 0.4);
    }

    public function getName(): string{
        return "Tropical Fish";
    }

    protected function swims(): bool{
        return true;
    }

    protected function breathesAir(): bool{
        return false;
    }
}
