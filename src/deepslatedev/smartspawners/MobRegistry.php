<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use deepslatedev\smartspawners\entity\mob;
use deepslatedev\smartspawners\entity\projectile\SlownessArrow;
use deepslatedev\smartspawners\entity\projectile\SmallFireball;
use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

final class MobRegistry{
    public const CLASSES = [
        "zombie" => mob\Zombie::class,
        "husk" => mob\Husk::class,
        "skeleton" => mob\Skeleton::class,
        "stray" => mob\Stray::class,
        "spider" => mob\Spider::class,
        "cave_spider" => mob\CaveSpider::class,
        "creeper" => mob\Creeper::class,
        "witch" => mob\Witch::class,
        "blaze" => mob\Blaze::class,
        "enderman" => mob\Enderman::class,
        "zombie_pigman" => mob\ZombiePigman::class,
        "iron_golem" => mob\IronGolem::class,
        "cow" => mob\Cow::class,
        "pig" => mob\Pig::class,
        "sheep" => mob\Sheep::class,
        "chicken" => mob\Chicken::class,
        "rabbit" => mob\Rabbit::class,
        "mooshroom" => mob\Mooshroom::class,
    ];

    private static array $defs = [];

    public static function load(array $mobs, array $defaults): void{
        self::$defs = [];
        foreach(self::CLASSES as $key => $class){
            $data = is_array($mobs[$key] ?? null) ? $mobs[$key] : [];
            if(($data["enabled"] ?? true) === false){
                continue;
            }
            $drops = [];
            foreach((array) ($data["drops"] ?? []) as $drop){
                $parts = explode(":", (string) $drop);
                $range = explode("-", $parts[count($parts) - 1]);
                $id = count($parts) > 1 && is_numeric($range[0]) ? implode(":", array_slice($parts, 0, -1)) : (string) $drop;
                $min = count($parts) > 1 && is_numeric($range[0]) ? (int) $range[0] : 1;
                $max = isset($range[1]) && is_numeric($range[1]) ? (int) $range[1] : $min;
                $drops[] = [$id, max(0, $min), max($min, $max)];
            }
            self::$defs[$key] = [
                "name" => (string) ($data["name"] ?? ucwords(str_replace("_", " ", $key))),
                "mode" => in_array($data["mode"] ?? "", ["hostile", "neutral", "passive"], true) ? $data["mode"] : "passive",
                "health" => (int) ($data["health"] ?? 20),
                "damage" => (float) ($data["damage"] ?? 0),
                "speed" => (float) ($data["speed"] ?? $defaults["speed"] ?? 0.2),
                "follow-range" => (float) ($data["follow-range"] ?? $defaults["follow-range"] ?? 16),
                "leash" => (float) ($data["leash"] ?? $defaults["leash"] ?? 6),
                "xp" => (int) ($data["xp"] ?? 0),
                "explodes" => (bool) ($data["explodes"] ?? false),
                "despawn-seconds" => (int) ($defaults["despawn-seconds"] ?? 300),
                "drops" => $drops,
            ];
        }
    }

    public static function register(): void{
        $factory = EntityFactory::getInstance();
        foreach(self::CLASSES as $key => $class){
            $factory->register($class, static fn(World $world, CompoundTag $nbt): SmartMob => new $class(EntityDataHelper::parseLocation($nbt, $world), $nbt), ["SmartSpawners:" . $key]);
        }
        $factory->register(SmallFireball::class, static fn(World $world, CompoundTag $nbt): SmallFireball => new SmallFireball(EntityDataHelper::parseLocation($nbt, $world), null, $nbt), ["SmartSpawners:small_fireball"]);
        $factory->register(SlownessArrow::class, static fn(World $world, CompoundTag $nbt): SlownessArrow => new SlownessArrow(EntityDataHelper::parseLocation($nbt, $world), null, false, $nbt), ["SmartSpawners:slowness_arrow"]);
    }

    public static function exists(string $key): bool{
        return isset(self::$defs[$key]);
    }

    public static function get(string $key): array{
        return self::$defs[$key] ?? self::$defs[array_key_first(self::$defs)] ?? [
            "name" => $key, "mode" => "passive", "health" => 10, "damage" => 0, "speed" => 0.2, "follow-range" => 16,
            "leash" => 6, "xp" => 0, "explodes" => false, "despawn-seconds" => 300, "drops" => [],
        ];
    }

    public static function keys(): array{
        return array_keys(self::$defs);
    }
}
