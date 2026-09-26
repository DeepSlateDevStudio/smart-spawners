<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageEvent;

final class MagmaCube extends Slime{
    public static function mobKey(): string{
        return "magma_cube";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:magma_cube";
    }

    public function getName(): string{
        return "Magma Cube";
    }

    public function isFireProof(): bool{
        return true;
    }

    protected function immuneTo(int $cause): bool{
        return in_array($cause, [EntityDamageEvent::CAUSE_FIRE, EntityDamageEvent::CAUSE_FIRE_TICK, EntityDamageEvent::CAUSE_LAVA, EntityDamageEvent::CAUSE_FALL], true);
    }

    protected function meleeDamage(array $def): float{
        return (float) ($this->slimeSize + 2);
    }
}
