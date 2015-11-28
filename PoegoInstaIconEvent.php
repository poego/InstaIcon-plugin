<?php
namespace Plugin\PoegoInstaIcon;
use Eccube\Event\RenderEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DomCrawler\Crawler;

class PoegoInstaIconEvent
{
    private $app;
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     *
     *
     * @param FilterResponseEvent $event
     */
    public function onRenderProductsDetailBefore(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $html = $this->getModifiedHtml($request, $response);
        $response->setContent($html);
        $event->setResponse($response);
    }

    /**
     *
     * @param Request $request
     * @param Response $response
     * @return string
     */
    private function getModifiedHtml(Request $request, Response $response)
    {
        $crawler = new Crawler($response->getContent());
        $html = $this->getHtml($crawler);
        $parts = $this->app->renderView('PoegoInstaIcon/Resource/template/icon.twig', array());
        try {
            $oldHtml = $crawler->filter('.dl_table')->last()->html();
            $newHtml = $oldHtml . $parts;
            $html = str_replace($oldHtml, $newHtml, $html);
        } catch (\InvalidArgumentException $e) {
        }
        return $html;
    }

    /**
     * 解析用HTMLを取得
     *
     * @param Crawler $crawler
     * @return string
     */
    private function getHtml(Crawler $crawler)
    {
        $html = '';
        foreach ($crawler as $domElement) {
            $domElement->ownerDocument->formatOutput = true;
            $html .= $domElement->ownerDocument->saveHTML();
        }
        return html_entity_decode($html, ENT_NOQUOTES, 'UTF-8');
    }
}