<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class Vex extends SmartMob{
    public static function mobKey(): string{
        return "vex";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:vex";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.8, 0.4);
    }

    public function getName(): string{
        return "Vex";
    }

    protected function canFly(): bool{
        return true;
    }

    public function heldItem(): ?Item{
        return VanillaItems::IRON_SWORD();
    }
}
