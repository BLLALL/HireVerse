<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('applicant.{applicantId}', function ($user, $applicantId) {
    \Log::info('Channel auth debug', [
        'user_id' => $user->id,
        'applicant_id' => $applicantId,
        'user_id_type' => gettype($user->id),
        'applicant_id_type' => gettype($applicantId),
        'match' => (int) $user->id === (int) $applicantId
    ]);
    
    return (int) $user->id === (int) $applicantId;
});