<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Cow extends SmartMob{
    public static function mobKey(): string{
        return "cow";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:cow";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.4, 0.9);
    }

    public function getName(): string{
        return "Cow";
    }

    protected function temptItems(): array{
        return Shots::itemIds("wheat");
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool{
        $hand = $player->getInventory()->getItemInHand();
        if($hand->getTypeId() === VanillaItems::BUCKET()->getTypeId()){
            self::exchange($player, VanillaItems::MILK_BUCKET());
            return true;
        }
        return parent::onInteract($player, $clickPos);
    }

    protected static function exchange(Player $player, Item $result): void{
        if($player->isCreative()){
            $player->getInventory()->addItem($result);
            return;
        }
        $hand = $player->getInventory()->getItemInHand();
        if($hand->getCount() === 1){
            $player->getInventory()->setItemInHand($result);
            return;
        }
        $player->getInventory()->setItemInHand($hand->setCount($hand->getCount() - 1));
        foreach($player->getInventory()->addItem($result) as $left){
            $player->getWorld()->dropItem($player->getPosition(), $left);
        }
    }

    public function climateVariant(): bool{
        return true;
    }
}
