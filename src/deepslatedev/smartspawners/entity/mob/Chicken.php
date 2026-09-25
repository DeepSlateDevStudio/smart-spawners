<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\PopSound;

final class Chicken extends SmartMob{
    public static function mobKey(): string{
        return "chicken";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:chicken";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.7, 0.4);
    }

    public function getName(): string{
        return "Chicken";
    }

    protected function temptItems(): array{
        return Shots::itemIds("wheat_seeds", "melon_seeds", "pumpkin_seeds", "beetroot_seeds");
    }

    private int $eggTimer = 0;

    protected function extraTick(int $tickDiff): void{
        if(!$this->onGround && $this->motion->y < -0.1){
            $this->setMotion($this->motion->withComponents(null, -0.1, null));
        }
        if($this->eggTimer <= 0){
            $this->eggTimer = mt_rand(6000, 12000);
        }
        $this->eggTimer -= $tickDiff;
        if($this->eggTimer <= 0){
            $this->getWorld()->dropItem($this->location, VanillaItems::EGG());
            $this->getWorld()->addSound($this->location, new PopSound());
        }
    }

    protected function immuneTo(int $cause): bool{
        return $cause === EntityDamageEvent::CAUSE_FALL;
    }
}
