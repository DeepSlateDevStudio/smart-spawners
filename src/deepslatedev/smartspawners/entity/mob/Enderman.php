<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Enderman extends SmartMob{
    public static function mobKey(): string{
        return "enderman";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:enderman";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(2.9, 0.6);
    }

    public function getName(): string{
        return "Enderman";
    }
}
