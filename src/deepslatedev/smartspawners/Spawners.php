<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use deepslatedev\smartspawners\entity\SmartMob;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\world\particle\FlameParticle;
use pocketmine\world\Position;
use pocketmine\world\World;

final class Spawners{
    public const TAG = "SmartSpawnerMob";

    private Config $store;
    private array $entries = [];
    private array $timers = [];

    public function __construct(private Main $plugin){
        $this->store = new Config($plugin->getDataFolder() . "spawners.json", Config::JSON);
        foreach($this->store->getAll() as $key => $data){
            if(is_array($data) && MobRegistry::exists((string) ($data["mob"] ?? ""))){
                $this->entries[(string) $key] = ["mob" => (string) $data["mob"], "stack" => max(1, (int) ($data["stack"] ?? 1))];
            }
        }
    }

    public static function key(Position $pos): string{
        return $pos->getWorld()->getFolderName() . ";" . $pos->getFloorX() . ";" . $pos->getFloorY() . ";" . $pos->getFloorZ();
    }

    public function item(string $mob, int $amount = 1): Item{
        $item = VanillaBlocks::MONSTER_SPAWNER()->asItem()->setCount(max(1, $amount));
        $item->getNamedTag()->setString(self::TAG, $mob);
        $item->setCustomName("§r§e" . MobRegistry::get($mob)["name"] . " Spawner");
        $item->setLore(["§r§7Place it, then right click it with", "§r§7another one of the same mob to stack."]);
        return $item;
    }

    public static function mobOf(Item $item): ?string{
        $mob = $item->getNamedTag()->getString(self::TAG, "");
        return $mob !== "" && MobRegistry::exists($mob) ? $mob : null;
    }

    public function get(Position $pos): ?array{
        return $this->entries[self::key($pos)] ?? null;
    }

    public function add(Position $pos, string $mob): void{
        $this->entries[self::key($pos)] = ["mob" => $mob, "stack" => 1];
        $this->save();
    }

    public function setMob(Position $pos, string $mob): void{
        $key = self::key($pos);
        $this->entries[$key] = ["mob" => $mob, "stack" => $this->entries[$key]["stack"] ?? 1];
        unset($this->timers[$key]);
        $this->save();
    }

    public function stack(Position $pos): int{
        $key = self::key($pos);
        $this->entries[$key]["stack"]++;
        $this->save();
        return $this->entries[$key]["stack"];
    }

    public function remove(Position $pos): ?array{
        $key = self::key($pos);
        $entry = $this->entries[$key] ?? null;
        unset($this->entries[$key], $this->timers[$key]);
        if($entry !== null){
            $this->save();
        }
        return $entry;
    }

    private function save(): void{
        $this->store->setAll($this->entries);
        $this->store->save();
    }

    private function setting(string $key, float $default): float{
        return (float) $this->plugin->getConfig()->getNested("spawners." . $key, $default);
    }

    public function tick(): void{
        $server = $this->plugin->getServer();
        $activation = $this->setting("activation-range", 16);
        foreach($this->entries as $key => $entry){
            [$worldName, $x, $y, $z] = explode(";", $key);
            $world = $server->getWorldManager()->getWorldByName($worldName);
            if($world === null || !$world->isChunkLoaded(((int) $x) >> 4, ((int) $z) >> 4)){
                continue;
            }
            $center = new Vector3((int) $x + 0.5, (int) $y, (int) $z + 0.5);
            $near = false;
            foreach($world->getPlayers() as $player){
                if($player->getPosition()->distanceSquared($center) <= $activation * $activation){
                    $near = true;
                    break;
                }
            }
            if(!$near){
                continue;
            }
            $this->timers[$key] = ($this->timers[$key] ?? $this->delay()) - 1;
            if($this->timers[$key] > 0){
                continue;
            }
            $this->timers[$key] = $this->delay();
            $this->spawn($world, $center, $entry["mob"], $entry["stack"]);
        }
    }

    private function delay(): int{
        $min = (int) $this->setting("delay-min-seconds", 15);
        return mt_rand(max(1, $min), max($min, (int) $this->setting("delay-max-seconds", 30)));
    }

    private function spawn(World $world, Vector3 $center, string $mob, int $stack): void{
        $class = MobRegistry::CLASSES[$mob] ?? null;
        if($class === null){
            return;
        }
        $radius = max(1, (int) $this->setting("spawn-radius", 4));
        $nearby = 0;
        foreach($world->getNearbyEntities(new AxisAlignedBB($center->x - 10, $center->y - 5, $center->z - 10, $center->x + 10, $center->y + 5, $center->z + 10)) as $entity){
            if($entity instanceof SmartMob && $entity::mobKey() === $mob){
                $nearby++;
            }
        }
        $wanted = min($stack * max(1, (int) $this->setting("mobs-per-stack", 1)), (int) $this->setting("max-nearby", 12) - $nearby);
        for($i = 0; $i < $wanted; $i++){
            for($attempt = 0; $attempt < 8; $attempt++){
                $pos = $center->add(mt_rand(-$radius, $radius), mt_rand(-1, 1), mt_rand(-$radius, $radius))->floor();
                if(!$world->getBlock($pos->down())->isSolid() || $world->getBlock($pos)->isSolid() || $world->getBlock($pos->up())->isSolid()){
                    continue;
                }
                $spot = new Location($pos->x + 0.5, $pos->y, $pos->z + 0.5, $world, (float) mt_rand(0, 359), 0.0);
                (new $class($spot))->spawnToAll();
                $world->addParticle($spot->add(0, 0.5, 0), new FlameParticle());
                break;
            }
        }
    }
}
