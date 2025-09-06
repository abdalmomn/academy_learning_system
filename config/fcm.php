<?php

return [
    // من .env
    'project_id' => env('FCM_PROJECT_ID'),

    'credentials_json' => env('GOOGLE_APPLICATION_CREDENTIALS', storage_path('app/json/academylearningsystem-firebase-adminsdk-fbsvc-1766f0fb86.json')),
];
