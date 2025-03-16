<?php

namespace App\Http\Controllers;

class CurrentUserController extends Controller
{
    public function __invoke()
    {
        $type = class_basename($user = auth()->user());

        $class = 'App\\Http\\Resources\\'.$type.'Resource';
        $resource = $class::make($user);

        return response()->json([
            'message' => 'Authenticated',
            'data' => [strtolower($type) => $resource],
            'status' => 200,
        ]);
    }
}
