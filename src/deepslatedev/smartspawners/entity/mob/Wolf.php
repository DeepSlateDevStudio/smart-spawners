<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Wolf extends SmartMob{
    public static function mobKey(): string{
        return "wolf";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:wolf";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.85, 0.6);
    }

    public function getName(): string{
        return "Wolf";
    }

    protected function groupAnger(): bool{
        return true;
    }
}
