<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Ocelot extends SmartMob{
    public static function mobKey(): string{
        return "ocelot";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:ocelot";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.7, 0.6);
    }

    public function getName(): string{
        return "Ocelot";
    }

    protected function temptItems(): array{
        return Shots::itemIds("raw_fish", "raw_salmon");
    }
}
