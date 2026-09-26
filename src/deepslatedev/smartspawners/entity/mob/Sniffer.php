<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Sniffer extends SmartMob{
    public static function mobKey(): string{
        return "sniffer";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:sniffer";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.75, 1.9);
    }

    public function getName(): string{
        return "Sniffer";
    }

    protected function temptItems(): array{
        return Shots::itemIds("torchflower_seeds");
    }
}
