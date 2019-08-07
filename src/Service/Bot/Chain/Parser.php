<?php
declare(strict_types = 1);


namespace App\Service\Bot\Chain;

use Symfony\Component\DomCrawler\Crawler;

class Parser
{
    /**
     * @var IdswPageGetterInterface
     */
    private $botHttpClient;

    /**
     * Parser constructor.
     * @param IdswPageGetterInterface $botHttpClient
     */
    public function __construct(IdswPageGetterInterface $botHttpClient)
    {
        $this->botHttpClient = $botHttpClient;
    }

    /**
     * @param int $id
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getChain(int $id)
    {
        $page = $this->botHttpClient->getIdswPage($id);
        $html = $this->getChainBlock($page);
        return $this->prepareText($html);
    }

    /**
     * @param string $page
     * @return string
     */
    private function getChainBlock(string $page): string
    {
        $crawler = new Crawler($page);
        $html = $crawler->filter('body > div > div > table > tr > td')
            ->last()->filter('p')->first()->html();

        return $html;
    }

    /**
     * @param string $html
     * @return string
     */
    public function prepareText(string $html): string
    {
        [$chain,] = explode('<br><br>', $html); // Во второй части строки ссылка на Ping
        $strings = explode("<br>", $chain); // Построчное разбиение блока
        array_shift($strings); //Удаление строки заголовка
        $chain =  implode("<br>", $strings);
        return $chain;
    }
}