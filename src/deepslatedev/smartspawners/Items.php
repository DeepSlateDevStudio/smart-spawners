<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeCategory;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\world\format\io\GlobalItemDataHandlers;

final class Items{
    private const DEFINITIONS = [
        "saddle" => "Saddle",
        "carrot_on_a_stick" => "Carrot on a Stick",
        "warped_fungus_on_a_stick" => "Warped Fungus on a Stick",
    ];

    private static array $items = [];

    public static function register(): void{
        $serializer = GlobalItemDataHandlers::getSerializer();
        $deserializer = GlobalItemDataHandlers::getDeserializer();
        $parser = StringToItemParser::getInstance();
        $creative = CreativeInventory::getInstance();
        foreach(self::DEFINITIONS as $key => $name){
            $id = "minecraft:" . $key;
            $existing = $deserializer->getDeserializerForId($id);
            if($existing !== null){
                self::$items[$key] = ($existing)(new SavedItemData($id));
                continue;
            }
            $item = new SingleItem(new ItemIdentifier(ItemTypeIds::newId()), $name);
            $deserializer->map($id, static fn() => clone $item);
            $serializer->map($item, static fn() => new SavedItemData($id));
            $parser->override($key, static fn() => clone $item);
            $creative->add($item, CreativeCategory::EQUIPMENT);
            self::$items[$key] = $item;
        }
    }

    public static function saddle(): ?Item{
        return isset(self::$items["saddle"]) ? clone self::$items["saddle"] : null;
    }

    public static function isSaddle(Item $item): bool{
        return isset(self::$items["saddle"]) && $item->getTypeId() === self::$items["saddle"]->getTypeId();
    }

    public static function holdsControl(Player $player, string $key): bool{
        return isset(self::$items[$key]) && $player->getInventory()->getItemInHand()->getTypeId() === self::$items[$key]->getTypeId();
    }
}
