<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Fox extends SmartMob{
    public static function mobKey(): string{
        return "fox";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:fox";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.7, 0.6);
    }

    public function getName(): string{
        return "Fox";
    }

    protected function temptItems(): array{
        return Shots::itemIds("sweet_berries");
    }
}
