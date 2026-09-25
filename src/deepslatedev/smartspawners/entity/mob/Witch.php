<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Witch extends SmartMob{
    public static function mobKey(): string{
        return "witch";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:witch";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Witch";
    }
}
