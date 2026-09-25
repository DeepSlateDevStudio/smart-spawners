<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;

final class IronGolem extends SmartMob{
    public static function mobKey(): string{
        return "iron_golem";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:iron_golem";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(2.7, 1.4);
    }

    public function getName(): string{
        return "Iron Golem";
    }

    protected function monsterTargets(): bool{
        return true;
    }

    protected function immuneTo(int $cause): bool{
        return $cause === EntityDamageEvent::CAUSE_FALL || $cause === EntityDamageEvent::CAUSE_DROWNING;
    }

    protected function onMeleeHit(Living $target): void{
        $target->setMotion($target->getMotion()->withComponents(null, 0.6, null));
    }
}
