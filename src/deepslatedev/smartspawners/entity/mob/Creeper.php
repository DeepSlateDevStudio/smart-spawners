<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\world\sound\ExplodeSound;

final class Creeper extends SmartMob{
    public static function mobKey(): string{
        return "creeper";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:creeper";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.7, 0.6);
    }

    public function getName(): string{
        return "Creeper";
    }

    private const FUSE = 30;

    private int $fuse = -1;

    protected function syncNetworkData(EntityMetadataCollection $properties): void{
        parent::syncNetworkData($properties);
        $properties->setGenericFlag(EntityMetadataFlags::IGNITED, $this->fuse >= 0);
        $properties->setInt(EntityMetadataProperties::FUSE_LENGTH, self::FUSE);
    }

    private function setFuse(int $fuse): void{
        $was = $this->fuse >= 0;
        $this->fuse = $fuse;
        if($was !== ($fuse >= 0)){
            $this->networkPropertiesDirty = true;
        }
    }

    protected function engage(Living $target, array $def): void{
        $distance = $target->getPosition()->distance($this->location);
        $this->lookAt($target->getEyePos());
        if($distance < 3.0 || ($this->fuse >= 0 && $distance < 7.0)){
            if($this->fuse < 0){
                $this->setFuse(0);
            }
            $this->setMotion($this->motion->withComponents(0, null, 0));
            $this->fuse++;
            if($this->fuse >= self::FUSE){
                $this->explode((float) $def["damage"]);
            }
            return;
        }
        $this->setFuse(-1);
        $this->walkToward($target->getPosition(), (float) $def["speed"]);
    }

    protected function idle(array $def): void{
        $this->setFuse(-1);
        parent::idle($def);
    }

    private function explode(float $power): void{
        $world = $this->getWorld();
        $center = $this->location->asVector3();
        $world->addParticle($center, new HugeExplodeSeedParticle());
        $world->addSound($center, new ExplodeSound());
        $radius = 4.0;
        foreach($world->getNearbyEntities(new AxisAlignedBB($center->x - $radius, $center->y - $radius, $center->z - $radius, $center->x + $radius, $center->y + $radius, $center->z + $radius), $this) as $entity){
            if(!$entity instanceof Living){
                continue;
            }
            $distance = $entity->getPosition()->distance($center);
            $damage = $power * (1 - $distance / $radius);
            if($damage > 0){
                $entity->attack(new EntityDamageByEntityEvent($this, $entity, EntityDamageEvent::CAUSE_ENTITY_EXPLOSION, $damage));
                $push = $entity->getPosition()->subtractVector($center)->normalize()->multiply(0.6);
                $entity->setMotion($entity->getMotion()->addVector($push->withComponents(null, 0.4, null)));
            }
        }
        $this->flagForDespawn();
    }
}
