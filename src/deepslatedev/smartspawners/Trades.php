<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use pocketmine\entity\Entity;
use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\world\particle\HappyVillagerParticle;
use pocketmine\world\sound\XpCollectSound;

final class Trades{
    private const VARIANTS = [
        "farmer" => 1, "fisherman" => 2, "shepherd" => 3, "fletcher" => 4, "librarian" => 5, "cartographer" => 6,
        "cleric" => 7, "armorer" => 8, "weaponsmith" => 9, "toolsmith" => 10, "butcher" => 11, "leatherworker" => 12, "mason" => 13,
    ];

    private static array $professions = [];
    private static array $wandering = [];
    private static ?Main $plugin = null;

    public static function load(Main $plugin, array $data): void{
        self::$plugin = $plugin;
        self::$professions = [];
        foreach((array) ($data["professions"] ?? []) as $name => $trades){
            $parsed = self::parse((array) $trades, (string) $name);
            if(count($parsed) > 0){
                self::$professions[strtolower((string) $name)] = $parsed;
            }
        }
        self::$wandering = self::parse((array) ($data["wandering-trader"] ?? []), "wandering-trader");
    }

    private static function parse(array $trades, string $context): array{
        $parsed = [];
        foreach($trades as $trade){
            if(!is_array($trade)){
                continue;
            }
            $give = [];
            foreach((array) ($trade["give"] ?? []) as $entry){
                $item = self::item((string) $entry);
                if($item === null){
                    self::$plugin?->getLogger()->warning("Unknown item in trades.yml ($context): $entry");
                    continue 2;
                }
                $give[] = $item;
            }
            $get = self::item((string) ($trade["get"] ?? ""));
            if($get === null || count($give) === 0){
                self::$plugin?->getLogger()->warning("Skipped a trade in trades.yml ($context)");
                continue;
            }
            $parsed[] = [$give, $get];
        }
        return $parsed;
    }

    private static function item(string $entry): ?Item{
        $parts = explode(":", $entry);
        $count = 1;
        if(count($parts) > 1 && ctype_digit(end($parts))){
            $count = (int) array_pop($parts);
        }
        $item = StringToItemParser::getInstance()->parse(implode(":", $parts));
        return $item?->setCount(max(1, $count));
    }

    public static function randomProfession(string $kind): string{
        if($kind === "wandering" || count(self::$professions) === 0){
            return "";
        }
        $names = array_keys(self::$professions);
        return $names[array_rand($names)];
    }

    public static function variant(string $profession): int{
        return self::VARIANTS[$profession] ?? 0;
    }

    public static function open(Player $player, Entity $trader, string $profession): void{
        $trades = $profession === "" ? self::$wandering : (self::$professions[$profession] ?? []);
        if(count($trades) === 0){
            return;
        }
        $title = $profession === "" ? "Wandering Trader" : ucfirst($profession);
        $player->sendForm(new class($title, $trades, $trader) implements Form{
            public function __construct(private string $title, private array $trades, private Entity $trader){}

            public function jsonSerialize(): array{
                $buttons = [];
                foreach($this->trades as [$give, $get]){
                    $cost = implode(" + ", array_map(static fn(Item $item) => $item->getCount() . " " . $item->getName(), $give));
                    $buttons[] = ["text" => $cost . "\n§8for §r" . $get->getCount() . " " . $get->getName()];
                }
                return ["type" => "form", "title" => $this->title, "content" => "Pick a trade.", "buttons" => $buttons];
            }

            public function handleResponse(Player $player, $data): void{
                if(!is_int($data) || !isset($this->trades[$data]) || $this->trader->isClosed()){
                    return;
                }
                [$give, $get] = $this->trades[$data];
                $inventory = $player->getInventory();
                foreach($give as $cost){
                    if(!$inventory->contains($cost)){
                        $player->sendTip("§cYou don't have " . $cost->getCount() . " " . $cost->getName());
                        return;
                    }
                }
                foreach($give as $cost){
                    $inventory->removeItem(clone $cost);
                }
                foreach($inventory->addItem(clone $get) as $left){
                    $player->getWorld()->dropItem($player->getPosition(), $left);
                }
                $world = $this->trader->getWorld();
                $position = $this->trader->getPosition();
                for($i = 0; $i < 5; $i++){
                    $world->addParticle($position->add(mt_rand(-5, 5) / 10, 1.8 + mt_rand(0, 5) / 10, mt_rand(-5, 5) / 10), new HappyVillagerParticle());
                }
                $world->addSound($player->getPosition(), new XpCollectSound(), [$player]);
            }
        });
    }
}
