<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class WitherSkeleton extends SmartMob{
    public static function mobKey(): string{
        return "wither_skeleton";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:wither_skeleton";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(2.4, 0.7);
    }

    public function getName(): string{
        return "Wither Skeleton";
    }

    public function isFireProof(): bool{
        return true;
    }

    protected function immuneTo(int $cause): bool{
        return in_array($cause, [EntityDamageEvent::CAUSE_FIRE, EntityDamageEvent::CAUSE_FIRE_TICK, EntityDamageEvent::CAUSE_LAVA], true);
    }

    public function heldItem(): ?Item{
        return VanillaItems::STONE_SWORD();
    }

    protected function onMeleeHit(Living $target): void{
        $target->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 200));
    }
}
