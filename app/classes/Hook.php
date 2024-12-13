<?php
namespace App\classes;
class Hook {
    public int $price {
        get {
            return $this->price;
        }

        set(int $price) {
            if($price < 0)
            {
                throw InvalidArgumentException('Price must be integer');
            }
            $this->price = $price;
        }
    }
}