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
     */
    public function getCommutatorData(string $ip): array
    {
        $result = $this->swPageGetter->getSwPage($ip);
        $crawler = new Crawler($result);
        try {
            $crawler = $crawler->filter('body > div > div');
            $model = trim($crawler->eq(1)->html());
            $configUri = $crawler->eq(5)->filter('a')->attr('href');
            $log = trim($crawler->eq(4)->html());
            $mapImageUri = $crawler->eq(6)->filter('a')->attr('href');
            return [
                'model' => $model,
                'config_path' => "{$this->botPath}/{$configUri}",
                'log' => $log,
                'map_image_url' => "{$this->botPath}/{$mapImageUri}",
            ];
        } catch (\InvalidArgumentException $e) {
            throw new \DomainException("Data parsing failed: {$e->getMessage()}");
        }
    }
}
