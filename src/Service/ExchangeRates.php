<?php

declare(strict_types=1);

namespace CommissionGenerator\Service;

use CommissionGenerator\App\Config\Configuration;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Exchange\FixedExchange;
use Money\Exchange\ReversedCurrenciesExchange;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class ExchangeRates.
 */
class ExchangeRates
{
    /**
     * @return Converter
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function getRatesConverter()
    {
        $url = Configuration::getInstance()->get('EXCHANGE_RATES_API_URL');
        $arUrl = explode("|-|", $url);
        if(count($arUrl) > 1) {
            $url = $arUrl[0].$arUrl[1];
        }

        $cache = new FilesystemAdapter();
        $exchangeRates = $cache->get(
            'exchange_rates_api_cache',
            function (ItemInterface $item) {
                $item->expiresAfter(86400);
                $endpoint = 'latest';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $json = curl_exec($ch);
                curl_close($ch);

                $exchangeRates = json_decode($json, true);
                if (isset($exchangeRates['error'])) {
                    throw new \Exception('ExchangeRatesAPI Error: '.$exchangeRates['error']['code']);
                }

                return $exchangeRates['rates'];
            }
        );

        $exchange = new ReversedCurrenciesExchange(
            new FixedExchange(
                [
                    'EUR' => $exchangeRates,
                ]
            )
        );

        $converter = new Converter(new ISOCurrencies(), $exchange);
 
        return $converter;
    }
}
