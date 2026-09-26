<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeCategory;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\world\format\io\GlobalItemDataHandlers;

final class Eggs{
    private static array $eggs = [];

    public static function register(): void{
        $serializer = GlobalItemDataHandlers::getSerializer();
        $deserializer = GlobalItemDataHandlers::getDeserializer();
        $parser = StringToItemParser::getInstance();
        $creative = CreativeInventory::getInstance();
        $vanillaZombie = VanillaItems::ZOMBIE_SPAWN_EGG();
        $group = null;
        foreach($creative->getAllEntries() as $entry){
            if($entry->getItem()->equals($vanillaZombie, true, false)){
                $group = $entry->getGroup();
                break;
            }
        }
        foreach(MobRegistry::keys() as $key){
            $id = "minecraft:" . $key . "_spawn_egg";
            if($deserializer->getDeserializerForId($id) !== null){
                continue;
            }
            $egg = new MobEgg(new ItemIdentifier(ItemTypeIds::newId()), MobRegistry::get($key)["name"] . " Spawn Egg", $key);
            $deserializer->map($id, static fn() => clone $egg);
            $serializer->map($egg, static fn() => new SavedItemData($id));
            $parser->override($key . "_spawn_egg", static fn() => clone $egg);
            $creative->add($egg, CreativeCategory::NATURE, $group);
            self::$eggs[$key] = $egg;
        }
        $spawner = VanillaBlocks::MONSTER_SPAWNER()->asItem();
        if(!$creative->contains($spawner)){
            $creative->add($spawner, CreativeCategory::NATURE);
        }
    }

    public static function mobOf(Item $item): ?string{
        if($item instanceof MobEgg){
            return $item->getMobKey();
        }
        $native = [
            VanillaItems::ZOMBIE_SPAWN_EGG()->getTypeId() => "zombie",
            VanillaItems::VILLAGER_SPAWN_EGG()->getTypeId() => "villager",
            VanillaItems::SQUID_SPAWN_EGG()->getTypeId() => "squid",
        ];
        $key = $native[$item->getTypeId()] ?? null;
        return $key !== null && MobRegistry::exists($key) ? $key : null;
    }
}
