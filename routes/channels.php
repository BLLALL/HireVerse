<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//public channels
Broadcast::channel('applicant', function ($user) {
    \Log::info('Channel auth debug', [
        'user_id' => $user->id,
        'user_id_type' => gettype($user->id),
    ]);
    
    return true; // Allow all authenticated users to join this channel
});

Broadcast::channel('applicant.{applicantId}', function ($user, $applicantId) {
    \Log::info('Channel auth debug', [
        'user_id' => $user->id,
        'applicant_id' => $applicantId,
        'user_id_type' => gettype($user->id),
        'applicant_id_type' => gettype($applicantId),
        'match' => (int) $user->id === (int) $applicantId
    ]);
    
    return true;
});