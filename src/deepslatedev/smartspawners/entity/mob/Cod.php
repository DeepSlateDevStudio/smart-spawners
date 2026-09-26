<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Cod extends SmartMob{
    public static function mobKey(): string{
        return "cod";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:cod";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.3, 0.5);
    }

    public function getName(): string{
        return "Cod";
    }

    protected function swims(): bool{
        return true;
    }

    protected function breathesAir(): bool{
        return false;
    }
}
