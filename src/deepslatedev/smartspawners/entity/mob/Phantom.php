<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Phantom extends SmartMob{
    public static function mobKey(): string{
        return "phantom";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:phantom";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.5, 0.9);
    }

    public function getName(): string{
        return "Phantom";
    }

    protected function canFly(): bool{
        return true;
    }

    protected function burnsInDaylight(): bool{
        return true;
    }
}
