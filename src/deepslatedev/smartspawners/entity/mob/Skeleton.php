<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class Skeleton extends SmartMob{
    public static function mobKey(): string{
        return "skeleton";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:skeleton";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.99, 0.6);
    }

    public function getName(): string{
        return "Skeleton";
    }

    protected function burnsInDaylight(): bool{
        return true;
    }

    public function heldItem(): ?Item{
        return VanillaItems::BOW();
    }

    protected function rangedRange(): float{
        return 15.0;
    }

    protected function preferredDistance(): float{
        return 5.0;
    }

    protected function shoot(Living $target): int{
        Shots::arrow($this, $target);
        return mt_rand(30, 50);
    }
}
