<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;

final class Main extends PluginBase{
    private Spawners $spawners;

    protected function onEnable(): void{
        $this->saveDefaultConfig();
        $this->saveResource("mobs.yml");
        $mobs = (new Config($this->getDataFolder() . "mobs.yml", Config::YAML))->getAll();
        $bundled = $this->getResource("mobs.yml");
        if($bundled !== null){
            $defaults = yaml_parse((string) stream_get_contents($bundled));
            fclose($bundled);
            if(is_array($defaults)){
                $mobs += $defaults;
            }
        }
        MobRegistry::load($mobs, (array) $this->getConfig()->get("mobs", []));
        MobRegistry::register();
        Eggs::register();
        $this->spawners = new Spawners($this);
        $this->getServer()->getPluginManager()->registerEvents(new SpawnerListener($this, $this->spawners), $this);
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(fn() => $this->spawners->tick()), 20);
        $this->getLogger()->info(count(MobRegistry::keys()) . " mobs ready");
    }

    public function message(string $key, array $params = []): string{
        $text = (string) $this->getConfig()->getNested("messages." . $key, $key);
        foreach($params as $name => $value){
            $text = str_replace("{" . $name . "}", (string) $value, $text);
        }
        return $text;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        $sub = strtolower($args[0] ?? "");
        if($sub === "list"){
            $sender->sendMessage($this->message("list", ["mobs" => implode(", ", MobRegistry::keys())]));
            return true;
        }
        if($sub !== "give" && $sub !== "summon"){
            $sender->sendMessage($this->message("usage"));
            return true;
        }
        $mob = strtolower($args[1] ?? "");
        if(!MobRegistry::exists($mob)){
            $sender->sendMessage($this->message("unknown-mob", ["mobs" => implode(", ", MobRegistry::keys())]));
            return true;
        }
        $name = MobRegistry::get($mob)["name"];
        if($sub === "summon"){
            if(!$sender instanceof Player){
                return false;
            }
            $amount = max(1, min(20, (int) ($args[2] ?? 1)));
            $class = MobRegistry::CLASSES[$mob];
            for($i = 0; $i < $amount; $i++){
                $location = $sender->getLocation();
                (new $class(Location::fromObject($location->add(mt_rand(-2, 2), 0, mt_rand(-2, 2)), $location->getWorld(), $location->yaw, 0.0)))->spawnToAll();
            }
            $sender->sendMessage($this->message("summoned", ["amount" => $amount, "mob" => $name]));
            return true;
        }
        $target = isset($args[2]) ? $this->getServer()->getPlayerByPrefix($args[2]) : ($sender instanceof Player ? $sender : null);
        if($target === null){
            $sender->sendMessage($this->message("player-not-found"));
            return true;
        }
        $amount = max(1, min(64, (int) ($args[3] ?? 1)));
        foreach($target->getInventory()->addItem($this->spawners->item($mob, $amount)) as $left){
            $target->getWorld()->dropItem($target->getPosition(), $left);
        }
        $sender->sendMessage($this->message("given", ["amount" => $amount, "mob" => $name, "player" => $target->getName()]));
        if($target !== $sender){
            $target->sendMessage($this->message("received", ["amount" => $amount, "mob" => $name]));
        }
        return true;
    }
}
