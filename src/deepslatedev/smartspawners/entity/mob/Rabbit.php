<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\math\Vector3;

final class Rabbit extends SmartMob{
    public static function mobKey(): string{
        return "rabbit";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:rabbit";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.5, 0.4);
    }

    public function getName(): string{
        return "Rabbit";
    }

    protected function temptItems(): array{
        return Shots::itemIds("carrot", "golden_carrot", "dandelion");
    }

    protected function walkToward(Vector3 $target, float $speed): void{
        $dx = $target->x - $this->location->x;
        $dz = $target->z - $this->location->z;
        $length = sqrt($dx * $dx + $dz * $dz);
        if($length < 0.05){
            return;
        }
        $this->setRotation(rad2deg(atan2(-$dx, $dz)), $this->location->pitch);
        if($this->onGround){
            $this->setMotion(new Vector3($dx / $length * $speed * 1.8, 0.32, $dz / $length * $speed * 1.8));
        }
    }
}
