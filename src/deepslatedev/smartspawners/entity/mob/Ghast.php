<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;

final class Ghast extends SmartMob{
    public static function mobKey(): string{
        return "ghast";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:ghast";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(4.0, 4.0);
    }

    public function getName(): string{
        return "Ghast";
    }

    protected function canFly(): bool{
        return true;
    }

    public function isFireProof(): bool{
        return true;
    }

    protected function immuneTo(int $cause): bool{
        return in_array($cause, [EntityDamageEvent::CAUSE_FIRE, EntityDamageEvent::CAUSE_FIRE_TICK, EntityDamageEvent::CAUSE_LAVA], true);
    }

    protected function rangedRange(): float{
        return 32.0;
    }

    protected function preferredDistance(): float{
        return 12.0;
    }

    protected function shoot(Living $target): int{
        Shots::largeFireball($this, $target);
        return mt_rand(60, 90);
    }
}
