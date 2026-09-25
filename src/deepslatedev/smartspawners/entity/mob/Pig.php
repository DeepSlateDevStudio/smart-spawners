<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Pig extends SmartMob{
    public static function mobKey(): string{
        return "pig";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:pig";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.9, 0.9);
    }

    public function getName(): string{
        return "Pig";
    }
}
