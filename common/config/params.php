<?php

return [
    'adminEmail'                    => 'admin@example.com',
    'supportEmail'                  => 'support@example.com',
    'senderEmail'                   => 'noreply@example.com',
    'senderName'                    => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength'        => 8,
    //ключ для кук общий (frontend + backend)
    'cookieValidationKey'           => 'atLZmoeGMMh5tNSZtSpxIm6W903bNvHs',

    //домен для кук
    'cookieDomain'                  => '.yiitest.loc',
    'frontendUrl'                   => 'http://yiitest.loc',
    'backendUrl'                    => 'http://admin.yiitest.loc',
];
