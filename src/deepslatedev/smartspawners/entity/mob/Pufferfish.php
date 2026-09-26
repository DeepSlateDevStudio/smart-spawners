<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Pufferfish extends SmartMob{
    public static function mobKey(): string{
        return "pufferfish";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:pufferfish";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.7, 0.7);
    }

    public function getName(): string{
        return "Pufferfish";
    }

    protected function swims(): bool{
        return true;
    }

    protected function breathesAir(): bool{
        return false;
    }
}
