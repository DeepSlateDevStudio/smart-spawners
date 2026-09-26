<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners\entity;

use deepslatedev\smartspawners\entity\projectile\LargeFireball;
use deepslatedev\smartspawners\entity\projectile\SmallFireball;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Arrow;
use pocketmine\entity\projectile\Snowball;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\item\PotionType;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\world\sound\BlazeShootSound;
use pocketmine\world\sound\BowShootSound;
use pocketmine\world\sound\ThrowSound;

final class Shots{
    private static array $ids = [];

    public static function itemIds(string ...$names): array{
        $key = implode(",", $names);
        if(!isset(self::$ids[$key])){
            $ids = [];
            foreach($names as $name){
                $item = StringToItemParser::getInstance()->parse($name);
                if($item !== null){
                    $ids[] = $item->getTypeId();
                }
            }
            self::$ids[$key] = $ids;
        }
        return self::$ids[$key];
    }

    private static function aim(Living $from, Living $target, float $speed, float $arc, float $spread): array{
        $origin = $from->getEyePos();
        $aimAt = $target->getPosition()->add(0, $target->getSize()->getHeight() * 0.6, 0);
        $dx = $aimAt->x - $origin->x;
        $dz = $aimAt->z - $origin->z;
        $flat = sqrt($dx * $dx + $dz * $dz);
        $dy = $aimAt->y - $origin->y + $flat * $arc;
        $direction = (new Vector3($dx, $dy, $dz))->normalize();
        $direction = $direction->add(mt_rand(-100, 100) / 100 * $spread, mt_rand(-100, 100) / 100 * $spread, mt_rand(-100, 100) / 100 * $spread)->normalize();
        $yaw = rad2deg(atan2(-$direction->x, $direction->z));
        $pitch = rad2deg(-asin(max(-1.0, min(1.0, $direction->y))));
        return [Location::fromObject($origin->addVector($direction->multiply(0.8)), $from->getWorld(), $yaw, $pitch), $direction->multiply($speed)];
    }

    public static function arrow(Living $from, Living $target, string $class = Arrow::class): void{
        [$location, $motion] = self::aim($from, $target, 1.6, 0.2, 0.05);
        $arrow = new $class($location, $from, false);
        if($arrow instanceof Arrow){
            $arrow->setPickupMode(Arrow::PICKUP_NONE);
        }
        $arrow->setMotion($motion);
        $arrow->spawnToAll();
        $from->getWorld()->addSound($from->getPosition(), new BowShootSound());
    }

    public static function fireball(Living $from, Living $target): void{
        [$location, $motion] = self::aim($from, $target, 1.1, 0.0, 0.08);
        $ball = new SmallFireball($location, $from);
        $ball->setMotion($motion);
        $ball->spawnToAll();
        $from->getWorld()->addSound($from->getPosition(), new BlazeShootSound());
    }

    public static function largeFireball(Living $from, Living $target): void{
        [$location, $motion] = self::aim($from, $target, 0.9, 0.0, 0.03);
        $ball = new LargeFireball($location, $from);
        $ball->setMotion($motion);
        $ball->spawnToAll();
        $from->getWorld()->addSound($from->getPosition(), new BlazeShootSound());
    }

    public static function snowball(Living $from, Living $target): void{
        [$location, $motion] = self::aim($from, $target, 1.4, 0.15, 0.04);
        $ball = new Snowball($location, $from);
        $ball->setMotion($motion);
        $ball->spawnToAll();
        $from->getWorld()->addSound($from->getPosition(), new ThrowSound());
    }

    public static function potion(Living $from, Living $target, PotionType $type): void{
        [$location, $motion] = self::aim($from, $target, 0.75, 0.35, 0.03);
        $potion = new SplashPotion($location, $from, $type);
        $potion->setMotion($motion);
        $potion->spawnToAll();
        $from->getWorld()->addSound($from->getPosition(), new ThrowSound());
    }
}
