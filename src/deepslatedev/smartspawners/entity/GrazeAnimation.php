<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity;

use pocketmine\entity\animation\Animation;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;

final class GrazeAnimation implements Animation{
    public function __construct(private Entity $entity){}

    public function encode(): array{
        return [ActorEventPacket::create($this->entity->getId(), ActorEvent::EAT_GRASS_ANIMATION, 0, null)];
    }
}
