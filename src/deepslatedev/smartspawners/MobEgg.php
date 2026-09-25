<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\world\World;

final class MobEgg extends SpawnEgg{
    public function __construct(ItemIdentifier $identifier, string $name, private string $mobKey){
        parent::__construct($identifier, $name);
    }

    public function getMobKey(): string{
        return $this->mobKey;
    }

    protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity{
        $class = MobRegistry::CLASSES[$this->mobKey];
        return new $class(Location::fromObject($pos, $world, $yaw, $pitch));
    }
}
