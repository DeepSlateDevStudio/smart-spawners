<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Cat extends SmartMob{
    public static function mobKey(): string{
        return "cat";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:cat";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.7, 0.6);
    }

    public function getName(): string{
        return "Cat";
    }

    protected function temptItems(): array{
        return Shots::itemIds("raw_fish", "raw_salmon");
    }

    protected function tameItems(): array{
        return Shots::itemIds("raw_fish", "raw_salmon");
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
}
