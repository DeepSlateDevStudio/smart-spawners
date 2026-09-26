<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class SkeletonHorse extends SmartMob{
    public static function mobKey(): string{
        return "skeleton_horse";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:skeleton_horse";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.6, 1.4);
    }

    public function getName(): string{
        return "Skeleton Horse";
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
        return 0.5;
    }

    protected function jumpPower(): float{
        return 0.6;
    }
}
