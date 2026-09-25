<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity\mob;

use deepslatedev\smartspawners\entity\GrazeAnimation;
use deepslatedev\smartspawners\entity\Shots;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\Item;
use pocketmine\item\Shears;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;

final class Sheep extends SmartMob{
    public static function mobKey(): string{
        return "sheep";
    }

    public static function getNetworkTypeId(): string{
        return "minecraft:sheep";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo(1.3, 0.9);
    }

    public function getName(): string{
        return "Sheep";
    }

    protected function temptItems(): array{
        return Shots::itemIds("wheat");
    }

    private bool $sheared = false;
    private int $grazeTimer = 0;

    protected function syncNetworkData(EntityMetadataCollection $properties): void{
        parent::syncNetworkData($properties);
        $properties->setGenericFlag(EntityMetadataFlags::SHEARED, $this->sheared);
    }

    private function setSheared(bool $sheared): void{
        $this->sheared = $sheared;
        $this->networkPropertiesDirty = true;
    }

    public function getDrops(): array{
        $drops = parent::getDrops();
        if($this->sheared){
            $wool = VanillaBlocks::WOOL()->asItem()->getTypeId();
            $drops = array_values(array_filter($drops, static fn(Item $item) => $item->getTypeId() !== $wool));
        }
        return $drops;
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool{
        $hand = $player->getInventory()->getItemInHand();
        if($hand instanceof Shears && !$this->sheared){
            $this->setSheared(true);
            $this->getWorld()->dropItem($this->location->add(0, 1, 0), VanillaBlocks::WOOL()->setColor(DyeColor::WHITE)->asItem()->setCount(mt_rand(1, 3)));
            if(!$player->isCreative()){
                $hand->applyDamage(1);
                $player->getInventory()->setItemInHand($hand);
            }
            return true;
        }
        return parent::onInteract($player, $clickPos);
    }

    protected function extraTick(int $tickDiff): void{
        if($this->grazeTimer <= 0){
            $this->grazeTimer = mt_rand(600, 1800);
        }
        $this->grazeTimer -= $tickDiff;
        if($this->grazeTimer > 0){
            return;
        }
        $below = $this->location->floor()->down();
        $world = $this->getWorld();
        if($world->getBlock($below)->getTypeId() === VanillaBlocks::GRASS()->getTypeId()){
            $world->setBlock($below, VanillaBlocks::DIRT());
            $this->broadcastAnimation(new GrazeAnimation($this));
            if($this->sheared){
                $this->setSheared(false);
            }
        }
    }
}
