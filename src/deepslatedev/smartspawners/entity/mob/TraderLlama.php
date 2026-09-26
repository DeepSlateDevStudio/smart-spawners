<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class TraderLlama extends SmartMob{
    public static function mobKey(): string{
        return "trader_llama";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:trader_llama";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.87, 0.9);
    }

    public function getName(): string{
        return "Trader Llama";
    }

    protected function temptItems(): array{
        return Shots::itemIds("wheat", "hay_bale");
    }

    protected function tameByRiding(): bool{
        return true;
    }

    protected function rideable(): bool{
        return true;
    }

    protected function riderSpeed(): float{
        return 0.35;
    }
}
