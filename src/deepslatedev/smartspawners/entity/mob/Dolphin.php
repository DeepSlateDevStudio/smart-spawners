<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Dolphin extends SmartMob{
    public static function mobKey(): string{
        return "dolphin";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:dolphin";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.6, 0.9);
    }

    public function getName(): string{
        return "Dolphin";
    }

    protected function swims(): bool{
        return true;
    }
}
