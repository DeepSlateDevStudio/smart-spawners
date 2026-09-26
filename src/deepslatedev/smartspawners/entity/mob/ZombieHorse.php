<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class ZombieHorse extends SmartMob{
    public static function mobKey(): string{
        return "zombie_horse";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:zombie_horse";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.6, 1.4);
    }

    public function getName(): string{
        return "Zombie Horse";
    }
}
