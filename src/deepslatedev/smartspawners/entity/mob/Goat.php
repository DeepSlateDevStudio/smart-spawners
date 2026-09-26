<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Goat extends SmartMob{
    public static function mobKey(): string{
        return "goat";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:goat";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.3, 0.9);
    }

    public function getName(): string{
        return "Goat";
    }

    protected function temptItems(): array{
        return Shots::itemIds("wheat");
    }
}
