<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class Piglin extends SmartMob{
    public static function mobKey(): string{
        return "piglin";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:piglin";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Piglin";
    }

    protected function groupAnger(): bool{
        return true;
    }

    public function heldItem(): ?Item{
        return VanillaItems::GOLDEN_SWORD();
    }
}
