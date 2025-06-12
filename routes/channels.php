<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('App.Models.Applicant.{applicantId}', function ($user, $applicantId) {
    // --- Temporary Debugging Logs ---
    Log::info('--- Broadcasting Auth Check ---');
    Log::info('Attempting to authorize for applicantId: ' . $applicantId);
    
    if ($user) {
        Log::info('Authenticated User ID: ' . $user->id);
        $is_authorized = (int) $user->id === (int) $applicantId;
        Log::info('Authorization result: ' . ($is_authorized ? 'SUCCESS' : 'FAILED'));
    } else {
        Log::warning('Authorization FAILED: No user is authenticated for this request.');
        $is_authorized = false;
    }
    
    Log::info('-----------------------------');
    // --- End of Debugging Logs ---

    return $is_authorized;
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