<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\math\Vector3;

class Spider extends SmartMob{
    public static function mobKey(): string{
        return "spider";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:spider";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.9, 1.4);
    }

    public function getName(): string{
        return "Spider";
    }

    protected function isHostileNow(): bool{
        $pos = $this->location->floor();
        return $this->getWorld()->getFullLightAt($pos->x, $pos->y, $pos->z) < 12;
    }

    protected function extraTick(int $tickDiff): void{
        if($this->isCollidedHorizontally && !$this->isUnderwater()){
            $this->setMotion($this->motion->withComponents(null, 0.2, null));
        }
    }

    protected function engage(Living $target, array $def): void{
        $distance = $target->getPosition()->distance($this->location);
        if($distance > 2.0 && $distance < 5.0 && $this->onGround && mt_rand(1, 12) === 1){
            $push = $target->getPosition()->subtractVector($this->location)->withComponents(null, 0, null)->normalize()->multiply(0.55);
            $this->setMotion(new Vector3($push->x, 0.4, $push->z));
            return;
        }
        parent::engage($target, $def);
    }
}
