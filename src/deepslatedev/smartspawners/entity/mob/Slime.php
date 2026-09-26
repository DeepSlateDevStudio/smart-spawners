<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;

class Slime extends SmartMob{
    public static function mobKey(): string{
        return "slime";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:slime";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(0.52, 0.52);
    }

    public function getName(): string{
        return "Slime";
    }

    protected int $slimeSize = 1;

    protected function initEntity(CompoundTag $nbt): void{
        $this->slimeSize = $nbt->getInt("SlimeSize", [1, 2, 4][mt_rand(0, 2)]);
        parent::initEntity($nbt);
        $this->setScale((float) $this->slimeSize);
        $this->setMaxHealth(max(1, $this->slimeSize * $this->slimeSize));
        $this->setHealth((float) $this->getMaxHealth());
    }

    public function getSlimeSize(): int{
        return $this->slimeSize;
    }

    protected function hops(): bool{
        return true;
    }

    protected function meleeDamage(array $def): float{
        return $this->slimeSize <= 1 ? 0.0 : (float) $this->slimeSize;
    }

    public function getDrops(): array{
        return $this->slimeSize === 1 ? parent::getDrops() : [];
    }

    protected function onDeath(): void{
        parent::onDeath();
        if($this->slimeSize <= 1){
            return;
        }
        $world = $this->getWorld();
        for($i = mt_rand(2, 4); $i > 0; $i--){
            $spot = Location::fromObject($this->location->add(mt_rand(-5, 5) / 10, 0.2, mt_rand(-5, 5) / 10), $world, (float) mt_rand(0, 359), 0.0);
            (new static($spot, CompoundTag::create()->setInt("SlimeSize", intdiv($this->slimeSize, 2))))->spawnToAll();
        }
    }
}
