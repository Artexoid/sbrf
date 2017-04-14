<?php
/**
 * Created by PhpStorm.
 * User: Artexoid
 * Date: 10.08.16
 * Time: 22:13
 */

namespace QFive\Artexoid\SBRF;


class Statistics extends Action
{
    const CREATED = 1;
    const APPROVED = 2;
    const DEPOSITED = 4;
    const DECLINED = 8;
    const REVERSED = 16;
    const REFUNDED = 32;

    protected static $actionUrl = 'getLastOrdersForMerchants.do';

    /** @inherit */
    protected static $errors = [
        0 => 'Обработка запроса прошла без системных ошибок',
        5 => 'Ошибка значение параметра запроса',
        6 => 'Незарегистрированный OrderId',
        7 => 'Системная ошибка',
    ];

    protected static function getTransactionStates(int $transactionStates)
    {
        $result = [];

        if ($transactionStates & static::CREATED) {
            $result[] = 'CREATED';
        }
        if ($transactionStates & static::APPROVED) {
            $result[] = 'APPROVED';
        }
        if ($transactionStates & static::DEPOSITED) {
            $result[] = 'DEPOSITED';
        }
        if ($transactionStates & static::DECLINED) {
            $result[] = 'DECLINED';
        }
        if ($transactionStates & static::REVERSED) {
            $result[] = 'REVERSED';
        }
        if ($transactionStates & static::REFUNDED) {
            $result[] = 'REFUNDED';
        }

        return implode(',', $result);
    }

    private static function getPage(string $from, string $to, int $transactionStates, $page = 0)
    {
        $from = date('YmdHis', strtotime($from));
        $to = date('YmdHis', strtotime($to));

        $query = [
            'userName' => Config::gI()->userName,
            'password' => Config::gI()->password,
            'language' => Config::gI()->currency,
            'from' => $from,
            'to' => $to,
            'transactionStates' => static::getTransactionStates($transactionStates),
            'page' => $page,
            'size' => 200,
            'merchants' => ''
        ];

        $response = self::request($query);

        static::checkForErrors($response);

        if ($response['errorCode'] !== 0) {
            return $response;
        }

        $pages = ceil($response['totalCount'] / $response['pageSize']) - 1;
        if ($page < $pages) {
            $response['orderStatuses'] = array_merge(
                $response['orderStatuses'],
                static::getPage($from, $to, $transactionStates, $page + 1)['orderStatuses']
            );
        }

        return $response;
    }

    public static function getLastOrdersForMerchants(string $from, string $to, int $transactionStates)
    {
        return static::getPage($from, $to, $transactionStates);
    }
}