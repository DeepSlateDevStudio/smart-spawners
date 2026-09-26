# SmartSpawners

Mobs on PocketMine-MP and Altay just stand there. SmartSpawners brings them to life: 72 vanilla mobs that walk, chase, shoot, fly, swim and flee, that you can breed, tame, ride and trade with, plus stackable spawners and working spawn eggs in the creative menu.

No mods and nothing for players to install: every mob uses the model and animations the Bedrock client already has.

## What it does

**72 mobs with their own behaviour**

- **Hostile mobs chase and fight.** Zombies burn in daylight, husks cause hunger, wither skeletons wither you, vindicators swing axes, ravagers and hoglins throw you in the air.
- **Ranged mobs keep their distance.** Skeletons, strays, bogged and pillagers shoot arrows (strays slow you down, bogged poison you), witches throw potions and drink healing ones, blazes charge up and fire bursts of 3 fireballs, ghasts shoot exploding fireballs, snow golems throw snowballs at monsters.
- **Creepers** swell up, then explode without breaking blocks.
- **Spiders** climb walls, leap at you and stay neutral in bright light. Cave spiders poison.
- **Endermen** get angry when you look at them, teleport around and dodge arrows.
- **Neutral mobs** only fight back. Wolves, bees, piglins and zombified piglins call their group for help.
- **Iron golems and snow golems** defend against monsters. Axolotls hunt too.
- **Flying mobs** fly: bats, parrots, allays, bees, vexes, phantoms, ghasts, blazes.
- **Aquatic mobs** swim in 3D. Fish and squids suffocate on land, drowned, dolphins, turtles and frogs are at home in both.
- **Slimes and magma cubes** hop at you in three sizes and split when they die.
- **Animals** wander, panic when hit and follow you when you hold their food. Chickens lay eggs and fall slowly, sheep can be sheared and eat grass to grow their wool back, cows and mooshrooms can be milked, mooshrooms give stew and turn into cows when sheared, rabbits and frogs hop.
- **Villagers and wandering traders** run away from zombies.

**Farming, taming, riding and trading**

- **Breeding:** feed two adults of the same species their food, they fall in love and a baby is born. Babies are smaller and grow up after 20 minutes (feeding them speeds it up).
- **Taming:** wolves with bones, cats with fish, parrots with seeds. Tamed pets follow you, teleport to you when left behind, sit or stand when you click them, and wolves defend you and attack what you attack.
- **Horses, donkeys and mules:** mount them a few times to tame them, then put a saddle on and ride. Steer with your movement keys, jump with the jump key. Camels only need a saddle. Llamas can be tamed by riding them but, like in vanilla, can't be steered.
- **Pigs and striders:** saddle them and steer with a carrot on a stick or a warped fungus on a stick. Saddles and both sticks are added to the creative menu.
- **Villagers** get a profession (with its outfit) and trade with you. The wandering trader has his own offers. Every trade is editable in `trades.yml`.
- Tamed, saddled, bred and egg-spawned animals are saved with the world.

**Spawners and eggs, like vanilla**

- A spawn egg for every mob in the creative menu, with the vanilla icon and name.
- Use an egg on the ground to spawn the mob, or on a spawner to set what it spawns.
- Spawners stack: right click a spawner with another spawner of the same mob (up to 64).
- Breaking a spawner gives back the whole stack (silk touch can be required).
- Right click a spawner with an empty hand to see its mob and stack size.
- Spawners only work while a player is nearby, and cap the number of mobs around them.

## Install

1. Drop the plugin in your `plugins` folder.
2. Restart the server.

## Commands

| Command | What it does | Permission |
|---|---|---|
| `/spawner give <mob> [player] [amount]` | Give spawners | `smartspawners.admin` (op) |
| `/spawner summon <mob> [amount]` | Spawn mobs next to you | `smartspawners.admin` (op) |
| `/spawner list` | List every mob | `smartspawners.admin` (op) |

## Config

`mobs.yml` holds every mob: name, behaviour (`hostile`, `neutral` or `passive`), health, damage, speed, XP and drops. Set `enabled: false` on a mob to remove it.

```yaml
zombie:
  name: "Zombie"
  mode: hostile
  health: 20
  damage: 3
  speed: 0.22
  xp: 5
  drops: ["rotten_flesh:0-2"]
```

Drops use `item:min-max`. New mobs added in an update are picked up automatically, your edits are kept.

`config.yml` controls the spawners (delay, activation range, spawn radius, max mobs nearby, max stack, drops on break), the variant data and every message.

`trades.yml` holds the offers for each villager profession and the wandering trader, as `item:amount`.

## Good to know

- Mobs from spawners don't save with the world and despawn after 5 minutes with no player around, so they never pile up.
- Cows, pigs and chickens get their climate variant data sent to the client. If a future client version shows them wrong, set `climate-variants: false` in `config.yml`.
- Villager trades use a menu instead of the vanilla trading screen, which the server software doesn't support.
- Mob kills work with any plugin that listens to entity deaths, jobs and stats included.

## Compatibility

PocketMine-MP API 5 and Altay.

## About

Made by DeepSlate Dev. Released under the MIT license, free to use on any server.
