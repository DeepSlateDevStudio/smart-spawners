<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class ZombieVillager extends SmartMob{
    public static function mobKey(): string{
        return "zombie_villager";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:zombie_villager_v2";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Zombie Villager";
    }

    protected function burnsInDaylight(): bool{
        return true;
    }
}
