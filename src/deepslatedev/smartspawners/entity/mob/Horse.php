<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Horse extends SmartMob{
    public static function mobKey(): string{
        return "horse";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:horse";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.6, 1.4);
    }

    public function getName(): string{
        return "Horse";
    }

    protected function temptItems(): array{
        return Shots::itemIds("golden_apple", "golden_carrot", "apple", "wheat", "sugar");
    }
}
