<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Camel extends SmartMob{
    public static function mobKey(): string{
        return "camel";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:camel";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(2.375, 1.7);
    }

    public function getName(): string{
        return "Camel";
    }

    protected function temptItems(): array{
        return Shots::itemIds("cactus");
    }
}
