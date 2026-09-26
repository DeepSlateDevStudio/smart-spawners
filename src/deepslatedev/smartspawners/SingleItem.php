<?php

declare(strict_types=1);

namespace deepslatedev\smartspawners;

use pocketmine\item\Item;

final class SingleItem extends Item{
    public function getMaxStackSize(): int{
        return 1;
    }
}
