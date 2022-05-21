<?php
declare(strict_types=1);

namespace IpDataLookup;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Psr7\Response;
use Respect\Validation\Validator as v;

class IpDataLookup
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function lookup(string $ipAddress)
    {
        try {
            $validIPv4 = v::ip('*', FILTER_FLAG_NO_PRIV_RANGE)->validate($ipAddress);
            $validIPv6 = v::ip('*', FILTER_FLAG_IPV6)->validate($ipAddress);
            if ($validIPv4 || $validIPv6 === true) {
                $apiKey = '0857fac6bb5994ea9e59c9be94723a161b14e38268358fea8b797a8b';
                $client = new Client();
                /** @var Response $response */
                $response = $client->request('GET', 'https://api.ipdata.co/'.$ipAddress.'?api-key='.$apiKey);
                if ($response->getStatusCode() === 200) {
                    /** @var array $json */
                    $json = json_decode($response->getBody()->getContents(), true);

                    return [
                        'city' => $json['city'] ?? null,
                        'region' => $json['region'] ?? null,
                        'country_code' => $json['country_code'] ?? null,
                        'latitude' => $json['latitude'] ?? null,
                        'longitude' => $json['longitude'] ?? null,
                        'postcode' => $json['postcode'] ?? null,
                        'is_tor' => $json['threat']['is_tor'] ?? null,
                        'is_proxy' => $json['threat']['is_proxy'] ?? null,
                        'is_anonymous' => $json['threat']['is_anonymous'] ?? null,
                        'is_known_attacker' => $json['threat']['is_known_attacker'] ?? null,
                        'is_known_abuser' => $json['threat']['is_known_abuser'] ?? null,
                    ];
                }
            }

        } catch (InvalidArgumentException $exception) {
            return $exception->getMessage();
        }
    }
}