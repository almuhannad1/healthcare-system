<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Consultation Fee
    |--------------------------------------------------------------------------
    |
    | Charged for a visit when the doctor has no consultation_fee_cents of
    | their own. Stored in cents, like every other money value in the app.
    |
    */

    'consultation_fee_cents' => (int) env('BILLING_CONSULTATION_FEE_CENTS', 5000),

];
