<?php

namespace App\Http;

use Log;
use ReCaptcha\ReCaptcha;
use App\Exceptions\ForbiddenException;

class Utilities {
    public static function checkRecaptcha($request) {
        // FIXME: Workaround due to some mocking issues in PHPUnit when unit testing
        if (config('app.env') == 'testing') {
            return;
        }
        $recaptcha = new ReCaptcha(config('ctvero.recaptchaSecret'));
        $response = $recaptcha->setScoreThreshold(config('ctvero.recaptchaScoreThreshold'))
            ->verify($request->input('g-recaptcha-response'), $request->ip());
        Log::info('Received reCAPTCHA response:', [ var_export($response, true) ]);
        if (! $response->isSuccess()) {
            throw new ForbiddenException();
        }
        return $response;
    }

    public static function contestL10n($contestName) {
        if (preg_match_all('|^(.*\S)\s+(\d+)\s*$|', $contestName, $output)) {
            return __($output[1][0]) . ' ' . $output[2][0];
        }
        return $contestName;
    }
}
