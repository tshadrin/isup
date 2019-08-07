<?php
declare(strict_types=1);

namespace App\Service\Bot\Commutator;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Parser
 * @package App\Service\Bot\Commutator
 */
class Parser
{
    /**
     * @var SwPageGetterInterface
     */
    private $swPageGetter;
    /**
     * @var string
     */
    private $botPath;

    /**
     * Parser constructor.
     * @param SwPageGetterInterface $swPageGetter
     */
    public function __construct(string $botPath, SwPageGetterInterface $swPageGetter)
    {
        $this->swPageGetter = $swPageGetter;
        $this->botPath = $botPath;
    }

    /**
     * @param string $ip
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getCommutatorData(string $ip): array
    {
        $result = $this->swPageGetter->getSwPage($ip);
        $crawler = new Crawler($result);
        $crawler = $crawler->filter('body > div > div');
        $model = trim($crawler->eq(1)->html());
        $config_path = $crawler->eq(5)->filter('a')->attr('href');
        $map_image_path = $crawler->eq(6)->filter('a')->attr('href');
        $log = trim($crawler->eq(4)->html());
        return [
            'model' => $model,
            'config_path' => "{$this->botPath}/{$config_path}",
            'map_image_url' => "{$this->botPath}/{$map_image_path}",
            'log' => $log,
        ];
    }
}
