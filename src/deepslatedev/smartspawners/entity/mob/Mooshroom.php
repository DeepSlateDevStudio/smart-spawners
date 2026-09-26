<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\Shears;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class Mooshroom extends Cow{
    public static function mobKey(): string{
        return "mooshroom";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:mooshroom";
    }

    public function getName(): string{
        return "Mooshroom";
    }

    public function climateVariant(): bool{
        return false;
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool{
        $hand = $player->getInventory()->getItemInHand();
        if($hand->getTypeId() === VanillaItems::BOWL()->getTypeId()){
            self::exchange($player, VanillaItems::MUSHROOM_STEW());
            return true;
        }
        if($hand instanceof Shears){
            $world = $this->getWorld();
            $world->dropItem($this->location->add(0, 1, 0), VanillaBlocks::RED_MUSHROOM()->asItem()->setCount(5));
            $cow = new Cow(Location::fromObject($this->location, $world, $this->location->yaw, 0.0));
            $cow->spawnToAll();
            if(!$player->isCreative()){
                $hand->applyDamage(1);
                $player->getInventory()->setItemInHand($hand);
            }
            $this->flagForDespawn();
            return true;
        }
        return parent::onInteract($player, $clickPos);
    }
}
