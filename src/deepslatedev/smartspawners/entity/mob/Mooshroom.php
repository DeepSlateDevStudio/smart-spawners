<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Mooshroom extends SmartMob{
    public static function mobKey(): string{
        return "mooshroom";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:mooshroom";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.4, 0.9);
    }

    public function getName(): string{
        return "Mooshroom";
    }
}
