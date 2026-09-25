<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\projectile\SlownessArrow;
use deepslatedev\smartspawners\entity\Shots;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;

final class Stray extends Skeleton{
    public static function mobKey(): string{
        return "stray";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:stray";
    }

    public function getName(): string{
        return "Stray";
    }

    protected function shoot(Living $target): int{
        Shots::arrow($this, $target, SlownessArrow::class);
        return mt_rand(30, 50);
    }
}
