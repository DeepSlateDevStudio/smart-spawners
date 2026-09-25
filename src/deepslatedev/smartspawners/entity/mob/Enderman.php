<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\PortalParticle;
use pocketmine\world\sound\EndermanTeleportSound;

final class Enderman extends SmartMob{
    public static function mobKey(): string{
        return "enderman";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:enderman";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(2.9, 0.6);
    }

    public function getName(): string{
        return "Enderman";
    }

    public function attack(EntityDamageEvent $source): void{
        if($source->getCause() === EntityDamageEvent::CAUSE_PROJECTILE){
            $source->cancel();
            $this->teleportRandomly(null);
            return;
        }
        parent::attack($source);
    }

    protected function onHurtBy(Entity $damager): void{
        parent::onHurtBy($damager);
        if(mt_rand(1, 3) === 1){
            $this->teleportRandomly(null);
        }
    }

    protected function extraTick(int $tickDiff): void{
        if($this->isUnderwater()){
            if($this->age % 20 === 0){
                $this->attack(new EntityDamageEvent($this, EntityDamageEvent::CAUSE_DROWNING, 1.0));
                $this->teleportRandomly(null);
            }
            return;
        }
        if($this->age % 5 !== 0){
            return;
        }
        if($this->angryAt === null){
            foreach($this->getWorld()->getPlayers() as $player){
                if($this->validPlayer($player) && $this->isStaredAtBy($player)){
                    $this->angryAt = $player->getId();
                    break;
                }
            }
            return;
        }
        $target = $this->getWorld()->getEntity($this->angryAt);
        if($target instanceof Living && $target->getPosition()->distance($this->location) > 12 && mt_rand(1, 8) === 1){
            $this->teleportRandomly($target->getPosition());
        }
    }

    private function isStaredAtBy(Player $player): bool{
        $eye = $player->getEyePos();
        $head = $this->location->add(0, $this->size->getHeight() - 0.3, 0);
        $toHead = $head->subtractVector($eye);
        $distance = $toHead->length();
        if($distance > 64 || $distance < 0.1){
            return false;
        }
        return $player->getDirectionVector()->dot($toHead->divide($distance)) > 1.0 - 0.025 / $distance;
    }

    private function teleportRandomly(?Vector3 $near): void{
        $world = $this->getWorld();
        $origin = $near ?? $this->location->asVector3();
        $spread = $near === null ? 16 : 4;
        for($attempt = 0; $attempt < 16; $attempt++){
            $x = (int) floor($origin->x) + mt_rand(-$spread, $spread);
            $z = (int) floor($origin->z) + mt_rand(-$spread, $spread);
            for($y = (int) floor($origin->y) + 8; $y > (int) floor($origin->y) - 8; $y--){
                if($world->getBlockAt($x, $y - 1, $z)->isSolid() && !$world->getBlockAt($x, $y, $z)->isSolid() && !$world->getBlockAt($x, $y + 1, $z)->isSolid() && !$world->getBlockAt($x, $y + 2, $z)->isSolid()){
                    $from = $this->location->asVector3();
                    $to = new Vector3($x + 0.5, $y, $z + 0.5);
                    $world->addSound($from, new EndermanTeleportSound());
                    for($i = 0; $i < 8; $i++){
                        $world->addParticle($from->add(mt_rand(-5, 5) / 10, mt_rand(0, 25) / 10, mt_rand(-5, 5) / 10), new PortalParticle());
                    }
                    $this->teleport($to);
                    $world->addSound($to, new EndermanTeleportSound());
                    return;
                }
            }
        }
    }
}
