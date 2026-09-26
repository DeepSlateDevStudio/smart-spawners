<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class Pillager extends SmartMob{
    public static function mobKey(): string{
        return "pillager";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:pillager";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Pillager";
    }

    public function heldItem(): ?Item{
        return VanillaItems::BOW();
    }

    protected function rangedRange(): float{
        return 12.0;
    }

    protected function preferredDistance(): float{
        return 6.0;
    }

    protected function shoot(Living $target): int{
        Shots::arrow($this, $target);
        return mt_rand(35, 55);
    }
}
