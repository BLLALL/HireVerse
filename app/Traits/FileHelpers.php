<?php

namespace App\Traits;

trait FileHelpers
{
    protected function generateUniqueName($file)
    {
        $fileName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $name = substr($fileName, 0, strrpos($fileName, '.'));

        return $name.'_'.uniqid().'.'.$extension;
    }
}
