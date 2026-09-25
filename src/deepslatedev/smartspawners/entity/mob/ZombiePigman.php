<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\AxisAlignedBB;

final class ZombiePigman extends SmartMob{
    public static function mobKey(): string{
        return "zombie_pigman";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:zombie_pigman";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Zombified Piglin";
    }

    public function heldItem(): ?Item{
        return VanillaItems::GOLDEN_SWORD();
    }

    public function becomeAngry(int $targetId): void{
        $this->angryAt = $targetId;
    }

    protected function onHurtBy(Entity $damager): void{
        parent::onHurtBy($damager);
        if($this->angryAt === null){
            return;
        }
        $c = $this->location;
        foreach($this->getWorld()->getNearbyEntities(new AxisAlignedBB($c->x - 32, $c->y - 10, $c->z - 32, $c->x + 32, $c->y + 10, $c->z + 32), $this) as $entity){
            if($entity instanceof ZombiePigman){
                $entity->becomeAngry($this->angryAt);
            }
        }
    }
}
