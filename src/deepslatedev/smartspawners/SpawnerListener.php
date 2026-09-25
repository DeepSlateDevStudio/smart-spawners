<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use pocketmine\block\MonsterSpawner;
use pocketmine\entity\Location;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;

final class SpawnerListener implements Listener{
    public function __construct(private Main $plugin, private Spawners $spawners){}

    public function onInteract(PlayerInteractEvent $event): void{
        if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK){
            return;
        }
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $item = $event->getItem();
        $eggMob = Eggs::mobOf($item);
        if($eggMob !== null && $block instanceof MonsterSpawner){
            $event->cancel();
            $this->spawners->setMob($block->getPosition(), $eggMob);
            if(!$player->isCreative()){
                $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
            }
            $player->sendTip($this->plugin->message("egg-set", ["mob" => MobRegistry::get($eggMob)["name"]]));
            return;
        }
        if($eggMob !== null && !$item instanceof MobEgg){
            $event->cancel();
            $class = MobRegistry::CLASSES[$eggMob];
            $spot = $block->getSide($event->getFace())->getPosition()->add(0.5, 0, 0.5);
            (new $class(Location::fromObject($spot, $player->getWorld(), $player->getLocation()->yaw + 180, 0.0)))->spawnToAll();
            if(!$player->isCreative()){
                $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
            }
            return;
        }
        if(!$block instanceof MonsterSpawner){
            return;
        }
        $entry = $this->spawners->get($block->getPosition());
        if($entry === null){
            return;
        }
        $mob = Spawners::mobOf($item);
        $name = MobRegistry::get($entry["mob"])["name"];
        if($mob === null){
            if($item->isNull()){
                $player->sendTip($this->plugin->message("info", ["mob" => $name, "stack" => $entry["stack"]]));
            }
            return;
        }
        $event->cancel();
        if($mob !== $entry["mob"]){
            return;
        }
        $max = (int) $this->plugin->getConfig()->getNested("spawners.max-stack", 64);
        if($entry["stack"] >= $max){
            $player->sendTip($this->plugin->message("stack-full", ["max" => $max]));
            return;
        }
        $stack = $this->spawners->stack($block->getPosition());
        if(!$player->isCreative()){
            $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
        }
        $player->sendTip($this->plugin->message("stacked", ["mob" => $name, "stack" => $stack]));
    }

    public function onPlace(BlockPlaceEvent $event): void{
        if($event->isCancelled()){
            return;
        }
        $mob = Spawners::mobOf($event->getItem());
        if($mob === null){
            return;
        }
        $world = $event->getPlayer()->getWorld();
        foreach($event->getTransaction()->getBlocks() as [$x, $y, $z, $block]){
            $position = new Position($x, $y, $z, $world);
            $this->plugin->getScheduler()->scheduleTask(new ClosureTask(function() use ($position, $mob): void{
                if($position->getWorld()->getBlock($position) instanceof MonsterSpawner){
                    $this->spawners->add($position, $mob);
                }
            }));
        }
    }

    public function onBreak(BlockBreakEvent $event): void{
        if($event->isCancelled() || !$event->getBlock() instanceof MonsterSpawner){
            return;
        }
        $entry = $this->spawners->remove($event->getBlock()->getPosition());
        if($entry === null){
            return;
        }
        $event->setXpDropAmount(0);
        $config = $this->plugin->getConfig();
        $silk = $event->getItem()->hasEnchantment(VanillaEnchantments::SILK_TOUCH());
        if((bool) $config->getNested("spawners.drop-on-break", true) && (!(bool) $config->getNested("spawners.require-silk-touch", false) || $silk)){
            $event->setDrops([$this->spawners->item($entry["mob"], $entry["stack"])]);
        }else{
            $event->setDrops([]);
        }
    }
}
