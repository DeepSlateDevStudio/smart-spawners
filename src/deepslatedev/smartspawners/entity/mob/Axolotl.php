<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Axolotl extends SmartMob{
    public static function mobKey(): string{
        return "axolotl";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:axolotl";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.42, 0.75);
    }

    public function getName(): string{
        return "Axolotl";
    }

    protected function swims(): bool{
        return true;
    }

    protected function monsterTargets(): bool{
        return true;
    }
}
