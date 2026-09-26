<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use deepslatedev\smartspawners\Trades;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;

final class Villager extends SmartMob{
    public static function mobKey(): string{
        return "villager";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:villager_v2";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.95, 0.6);
    }

    public function getName(): string{
        return "Villager";
    }

    private string $profession = "";

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->profession = $nbt->getString("SSProfession", Trades::randomProfession("villager"));
    }

    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setString("SSProfession", $this->profession);
        return $nbt;
    }

    protected function syncNetworkData(EntityMetadataCollection $properties): void{
        parent::syncNetworkData($properties);
        $properties->setInt(EntityMetadataProperties::VARIANT, Trades::variant($this->profession));
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool{
        if($this->baby){
            return parent::onInteract($player, $clickPos);
        }
        Trades::open($player, $this, $this->profession);
        return true;
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
