<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Wolf extends SmartMob{
    public static function mobKey(): string{
        return "wolf";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:wolf";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.85, 0.6);
    }

    public function getName(): string{
        return "Wolf";
    }

    protected function groupAnger(): bool{
        return true;
    }

    protected function tameItems(): array{
        return Shots::itemIds("bone");
    }

    protected function tameChance(): int{
        return 3;
    }

    protected function canSit(): bool{
        return true;
    }

    protected function breedNeedsTame(): bool{
        return true;
    }

    protected function breedItems(): array{
        return Shots::itemIds("raw_beef", "raw_porkchop", "raw_chicken", "raw_mutton", "raw_rabbit", "steak", "cooked_porkchop", "cooked_chicken", "cooked_mutton", "cooked_rabbit");
    }
}
