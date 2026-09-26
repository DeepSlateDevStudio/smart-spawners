<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Panda extends SmartMob{
    public static function mobKey(): string{
        return "panda";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:panda";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.25, 1.3);
    }

    public function getName(): string{
        return "Panda";
    }

    protected function temptItems(): array{
        return Shots::itemIds("bamboo");
    }
}
