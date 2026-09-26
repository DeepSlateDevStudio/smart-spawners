<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Bat extends SmartMob{
    public static function mobKey(): string{
        return "bat";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:bat";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.9, 0.5);
    }

    public function getName(): string{
        return "Bat";
    }

    protected function canFly(): bool{
        return true;
    }
}
