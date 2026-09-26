# SmartSpawners

Mobs on PocketMine-MP and Altay just stand there. SmartSpawners brings them to life: 72 vanilla mobs that walk, chase, shoot, fly, swim and flee, plus stackable spawners and working spawn eggs in the creative menu.

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

`config.yml` controls the spawners (delay, activation range, spawn radius, max mobs nearby, max stack, drops on break) and every message.

## Good to know

- Mobs spawned by the plugin don't save with the world and despawn after 5 minutes with no player around, so they never pile up.
- Breeding, taming, riding and villager trading aren't included.
- Some newer variants (cow, pig, chicken, wolf) may show their default texture, because the server software doesn't send variant data yet.
- Mob kills work with any plugin that listens to entity deaths, jobs and stats included.

## Compatibility

PocketMine-MP API 5 and Altay.

## About

Made by DeepSlate Dev. Released under the MIT license, free to use on any server.
