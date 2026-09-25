<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\item\PotionType;
use pocketmine\world\sound\PopSound;

final class Witch extends SmartMob{
    public static function mobKey(): string{
        return "witch";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:witch";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Witch";
    }

    private int $drinkCooldown = 0;

    protected function rangedRange(): float{
        return 10.0;
    }

    protected function preferredDistance(): float{
        return 4.0;
    }

    protected function shoot(Living $target): int{
        $distance = $target->getPosition()->distance($this->location);
        $effects = $target->getEffects();
        if($distance >= 8 && !$effects->has(VanillaEffects::SLOWNESS())){
            $type = PotionType::SLOWNESS;
        }elseif($target->getHealth() >= 8 && !$effects->has(VanillaEffects::POISON())){
            $type = PotionType::POISON;
        }elseif($distance <= 3 && !$effects->has(VanillaEffects::WEAKNESS()) && mt_rand(1, 4) === 1){
            $type = PotionType::WEAKNESS;
        }else{
            $type = PotionType::HARMING;
        }
        Shots::potion($this, $target, $type);
        return 60;
    }

    protected function extraTick(int $tickDiff): void{
        $this->drinkCooldown = max(0, $this->drinkCooldown - $tickDiff);
        if($this->drinkCooldown === 0 && $this->getHealth() < $this->getMaxHealth() * 0.5){
            $this->drinkCooldown = 120;
            $this->heal(new EntityRegainHealthEvent($this, 6.0, EntityRegainHealthEvent::CAUSE_CUSTOM));
            $this->getWorld()->addSound($this->location, new PopSound());
        }
    }
}
