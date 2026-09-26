<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Armadillo extends SmartMob{
    public static function mobKey(): string{
        return "armadillo";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:armadillo";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.65, 0.7);
    }

    public function getName(): string{
        return "Armadillo";
    }

    protected function temptItems(): array{
        return Shots::itemIds("spider_eye");
    }
}
