<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Salmon extends SmartMob{
    public static function mobKey(): string{
        return "salmon";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:salmon";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.5, 0.7);
    }

    public function getName(): string{
        return "Salmon";
    }

    protected function swims(): bool{
        return true;
    }

    protected function breathesAir(): bool{
        return false;
    }
}
