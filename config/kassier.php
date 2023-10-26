<?php

return [

    /**
     * The models used by Kassier.
     * You can override these models by extending them in your own models and updating the configuration here.
     */
    'models' => [
        'customer' => \App\Models\User::class,
        'subscription' => \Malico\Kassier\Subscription::class,
        'price' => \Malico\Kassier\Price::class,
        'price_item' => \Malico\Kassier\PriceItem::class,
    ],

    /**
     * If you disable this, you will need to run the migrations manually.
     */
    'run_migrations' => true,
];
