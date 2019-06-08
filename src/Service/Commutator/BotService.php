<?php

namespace App\Service\Commutator;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\Translation\TranslatorInterface;

class BotService
{
    /**
     * @var string
     */
    private $bot_url;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * BotService constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters, TranslatorInterface $translator)
    {
        $this->bot_url = $parameters['bot_path'];
        $this->translator = $translator;
    }

    /**
     * @param string $ip
     * @return array
     */
    public function getCommutatorData(string $ip): array
    {
        try{
        // http://[bot_url]/msw?sw=127.0.0.1
        $query_data=['sw' => $ip];
        $result = $this->execCurl('msw', http_build_query($query_data));
        $crawler = new Crawler($result);
        $crawler = $crawler->filter('body > div > div');
        $model = trim($crawler->eq(1)->html());
        $config_path = $crawler->eq(5)->filter('a')->attr('href');
        $map_image_path = $crawler->eq(6)->filter('a')->attr('href');
        $log = trim($crawler->eq(4)->html());
        return [
            'model' => $model,
            'config_path' => "{$this->bot_url}/{$config_path}",
            'map_image_url' => "{$this->bot_url}/{$map_image_path}",
            'log' => $log,
            ];
        } catch (\Exception $e) {
            throw new \DomainException($this->translator->trans("Bot data not found for switch %ip%", ['%ip%' => $ip]));
        }
    }

    /**
     * @param string $command
     * @param string $query_data
     * @return string
     */
    public function execCurl(string $command, string $query_data): string
    {
        if(false !== ($curl = curl_init())) {
            curl_setopt_array($curl, [
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1, //ждем результат запроса
                CURLOPT_URL => "{$this->bot_url}/{$command}?{$query_data}",
            ]);
            if(false !== ($result = curl_exec($curl))) {
                curl_close($curl);
                return $result;
            }
            throw new \DomainException($this->translator->trans("Curl exec error"));
        } else {
            throw new \DomainException($this->translator->trans("Curl initializate error"));
        }
    }
}
