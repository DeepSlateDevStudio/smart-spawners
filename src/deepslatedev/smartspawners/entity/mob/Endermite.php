<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Endermite extends SmartMob{
    public static function mobKey(): string{
        return "endermite";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:endermite";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.3, 0.4);
    }

    public function getName(): string{
        return "Endermite";
    }
}
