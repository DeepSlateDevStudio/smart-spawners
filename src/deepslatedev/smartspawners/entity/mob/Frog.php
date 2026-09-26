<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Frog extends SmartMob{
    public static function mobKey(): string{
        return "frog";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:frog";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.55, 0.5);
    }

    public function getName(): string{
        return "Frog";
    }

    protected function swims(): bool{
        return true;
    }

    protected function hops(): bool{
        return true;
    }

    protected function temptItems(): array{
        return Shots::itemIds("slime_ball");
    }
}
