<?php

/*
 | How customers reach G&M by phone. Kept in one place: the header, shop pages,
 | confirmation emails and error messages had drifted onto two different
 | numbers. The policy pages are stored text, so a change of number needs
 | editing there in the admin as well.
 */
$phone = env('CONTACT_PHONE', '(07) 871-8575');

return [
    'phone' => $phone,

    // For tel: links: +64, then the number without its leading 0.
    'phone_tel' => '+64' . ltrim(preg_replace('/\D/', '', $phone), '0'),
];
