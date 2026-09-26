<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\math\AxisAlignedBB;

final class WanderingTrader extends SmartMob{
    public static function mobKey(): string{
        return "wandering_trader";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:wandering_trader";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Wandering Trader";
    }

    protected function extraTick(int $tickDiff): void{
        if($this->age % 10 !== 0 || $this->fleeTicks > 0){
            return;
        }
        $c = $this->location;
        foreach($this->getWorld()->getNearbyEntities(new AxisAlignedBB($c->x - 8, $c->y - 3, $c->z - 8, $c->x + 8, $c->y + 3, $c->z + 8), $this) as $entity){
            if($entity instanceof SmartMob && in_array($entity::mobKey(), ["zombie", "husk", "drowned", "zombie_villager"], true)){
                $this->fleeTicks = 40;
                $this->fleeFrom = $entity->getPosition()->asVector3();
                return;
            }
        }
    }
}
