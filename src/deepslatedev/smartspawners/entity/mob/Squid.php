<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Squid extends SmartMob{
    public static function mobKey(): string{
        return "squid";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:squid";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.95, 0.95);
    }

    public function getName(): string{
        return "Squid";
    }

    protected function swims(): bool{
        return true;
    }

    protected function breathesAir(): bool{
        return false;
    }
}
