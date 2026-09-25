<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

class Zombie extends SmartMob{
    public static function mobKey(): string{
        return "zombie";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:zombie";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Zombie";
    }

    protected function burnsInDaylight(): bool{
        return true;
    }
}
