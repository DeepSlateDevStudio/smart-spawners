<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Tadpole extends SmartMob{
    public static function mobKey(): string{
        return "tadpole";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:tadpole";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.3, 0.4);
    }

    public function getName(): string{
        return "Tadpole";
    }

    protected function swims(): bool{
        return true;
    }

    protected function breathesAir(): bool{
        return false;
    }
}
