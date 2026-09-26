<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class Vindicator extends SmartMob{
    public static function mobKey(): string{
        return "vindicator";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:vindicator";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Vindicator";
    }

    public function heldItem(): ?Item{
        return VanillaItems::IRON_AXE();
    }
}
