<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;

final class SnowGolem extends SmartMob{
    public static function mobKey(): string{
        return "snow_golem";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:snow_golem";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.8, 0.4);
    }

    public function getName(): string{
        return "Snow Golem";
    }

    protected function monsterTargets(): bool{
        return true;
    }

    protected function rangedRange(): float{
        return 10.0;
    }

    protected function preferredDistance(): float{
        return 3.0;
    }

    protected function shoot(Living $target): int{
        Shots::snowball($this, $target);
        return mt_rand(20, 30);
    }
}
