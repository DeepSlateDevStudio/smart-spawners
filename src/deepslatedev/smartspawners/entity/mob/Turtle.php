<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Turtle extends SmartMob{
    public static function mobKey(): string{
        return "turtle";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:turtle";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.4, 1.2);
    }

    public function getName(): string{
        return "Turtle";
    }

    protected function swims(): bool{
        return true;
    }

    protected function temptItems(): array{
        return Shots::itemIds("seagrass");
    }
}
