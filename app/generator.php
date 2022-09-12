<?php

require_once(__DIR__ . '/vendor/autoload.php');

require_once(__DIR__ . '/log.php');

$logger = getLogger('app', '/var/log/app-log.log');

function getUserUuid()
{
    $uuids = [
        'abc123',
        'def456',
        'ghi789',
        'jkl012',
        'mno345'
    ];

    $idx = rand(0, count($uuids) - 1);

    return $uuids[$idx];
}

function getItems(int $count)
{
    $items = [
        ['abc123', 5],
        ['def456', 2],
        ['ghi789', 3],
        ['jkl012', 7],
        ['mno345', 1]
    ];

    $basket = [];

    for ($i = 0; $i < $count; $i++) {
        $idx = rand(0, count($items) - 1);
        $basket[] = [
            'uuid' => $items[$idx][0],
            'cost' => $items[$idx][1] 
        ];
    }

    return $basket;
}

function getItem()
{
    $items = getItems(1);
    $item = current($items);

    return $item;
}

function checkout()
{
    $userUuid = getUserUuid();
    
    $numItems = rand(1, 10);
    $items = getItems($numItems);
    $total = 0;

    foreach ($items as $item) {
        $total += $item['cost'];
    }

    return [
        'level' => \Monolog\Logger::NOTICE,
        'message' => 'Checkout',
        'context' => [
            'code' => 'checkout',
            'userUuid' => $userUuid,
            'total' => $total,
            'itemUuids' => array_map(function($item) { return $item['uuid']; }, $items),
        ]
    ];
}

function addToBasket()
{
    $userUuid = getUserUuid();
    $item = getItem(); 
    $remaining = rand(0, 10);

    return [
        'level' => \Monolog\Logger::INFO,
        'message' => 'Product added to basket',
        'context' => [
            'code' => 'basket.add',
            'userUuid' => $userUuid,
            'productUuid' => $item['uuid'],
            'cost' => $item['cost'],
            'remainingStock' => $remaining
        ]
    ];
}

function removeFromBasket()
{
    $userUuid = getUserUuid();
    $item = getItem();
    $remaining = rand(1, 10);

    return [
        'level' => \Monolog\Logger::INFO,
        'message' => 'Product removed from basket',
        'context' => [
            'code' => 'basket.remove',
            'userUuid' => $userUuid,
            'productUuid' => $item['uuid'],
            'cost' => $item['cost'],
            'remainingStock' => $remaining
        ]
    ];
}

function viewProduct()
{
    $userUuid = getUserUuid();
    $item = getItem();
    $remaining = rand(1, 10);

    return [
        'level' => \Monolog\Logger::DEBUG,
        'message' => 'Product viewed',
        'context' => [
            'code' => 'product.view',
            'userUuid' => $userUuid,
            'productUuid' => $item['uuid'],
            'cost' => $item['cost'],
        ]
    ];
}

function error()
{
    $userUuid = getUserUuid();
    
    return [
        'level' => \Monolog\Logger::ERROR,
        'message' => 'Server error',
        'context' => [
            'code' => 'server_error',
            'error_message' => 'Something went wrong',
            'userUuid' => $userUuid,
        ]
    ];
}

function getEvent()
{
    $rand = rand(0, 100);

    if ($rand < 5) { return checkout(); }
    if ($rand < 15) { return addToBasket(); }
    if ($rand < 20) { return removeFromBasket(); }
    if ($rand < 99) { return viewProduct(); }

    return error();
}

while (true) {
    $event = getEvent();
    $logger->log($event['level'], $event['message'], $event['context']);
    sleep(1);
}
