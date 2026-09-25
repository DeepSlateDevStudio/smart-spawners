<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Blaze extends SmartMob{
    public static function mobKey(): string{
        return "blaze";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:blaze";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.8, 0.6);
    }

    public function getName(): string{
        return "Blaze";
    }
}
