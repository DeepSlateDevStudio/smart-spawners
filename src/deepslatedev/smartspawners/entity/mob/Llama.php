<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Llama extends SmartMob{
    public static function mobKey(): string{
        return "llama";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:llama";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.87, 0.9);
    }

    public function getName(): string{
        return "Llama";
    }

    protected function temptItems(): array{
        return Shots::itemIds("wheat", "hay_bale");
    }
}
