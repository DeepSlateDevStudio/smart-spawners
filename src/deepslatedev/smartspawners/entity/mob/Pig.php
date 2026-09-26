<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;

final class Pig extends SmartMob{
    public static function mobKey(): string{
        return "pig";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:pig";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.9, 0.9);
    }

    public function getName(): string{
        return "Pig";
    }

    protected function temptItems(): array{
        return Shots::itemIds("carrot", "potato", "beetroot");
    }

    public function climateVariant(): bool{
        return true;
    }

    protected function saddleable(): bool{
        return true;
    }

    protected function saddleNeedsTame(): bool{
        return false;
    }

    protected function rideable(): bool{
        return true;
    }

    protected function controlItem(): ?string{
        return "carrot_on_a_stick";
    }

    protected function riderSpeed(): float{
        return 0.25;
    }
}
