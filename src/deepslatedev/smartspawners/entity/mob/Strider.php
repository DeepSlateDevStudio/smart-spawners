<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageEvent;

final class Strider extends SmartMob{
    public static function mobKey(): string{
        return "strider";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:strider";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.7, 0.9);
    }

    public function getName(): string{
        return "Strider";
    }

    public function isFireProof(): bool{
        return true;
    }

    protected function immuneTo(int $cause): bool{
        return in_array($cause, [EntityDamageEvent::CAUSE_FIRE, EntityDamageEvent::CAUSE_FIRE_TICK, EntityDamageEvent::CAUSE_LAVA], true);
    }

    protected function temptItems(): array{
        return Shots::itemIds("warped_fungus");
    }
}
