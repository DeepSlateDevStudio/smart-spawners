<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Mule extends SmartMob{
    public static function mobKey(): string{
        return "mule";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:mule";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.6, 1.4);
    }

    public function getName(): string{
        return "Mule";
    }

    protected function temptItems(): array{
        return Shots::itemIds("golden_apple", "golden_carrot", "apple", "wheat", "sugar");
    }

    protected function breedItems(): array{
        return Shots::itemIds();
    }

    protected function tameByRiding(): bool{
        return true;
    }

    protected function saddleable(): bool{
        return true;
    }

    protected function rideable(): bool{
        return true;
    }

    protected function riderSpeed(): float{
        return 0.35;
    }

    protected function jumpPower(): float{
        return 0.55;
    }
}
