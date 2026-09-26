<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Allay extends SmartMob{
    public static function mobKey(): string{
        return "allay";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:allay";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.6, 0.6);
    }

    public function getName(): string{
        return "Allay";
    }

    protected function canFly(): bool{
        return true;
    }
}
