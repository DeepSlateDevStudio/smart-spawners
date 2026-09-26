<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Parrot extends SmartMob{
    public static function mobKey(): string{
        return "parrot";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:parrot";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.9, 0.5);
    }

    public function getName(): string{
        return "Parrot";
    }

    protected function canFly(): bool{
        return true;
    }

    protected function temptItems(): array{
        return Shots::itemIds("wheat_seeds", "melon_seeds", "pumpkin_seeds", "beetroot_seeds");
    }
}
