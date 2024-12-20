<?php

namespace Oksydan\Module\IsThemeCore\Core\ListingDisplay;

use Oksydan\Module\IsThemeCore\Form\Settings\GeneralConfiguration;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeListDisplay
{
    private $cookieName = 'listingDisplayType';
    private $displayList = [
        'grid',
        'list',
    ];

    protected function getRequest(): Request
    {
        Request::setFactory(static function ($query, $request, $attributes, $cookies, $files, $server, $content) {
            return new Request($query, $request, $attributes, $cookies, [], $server, $content);
        });

        return Request::createFromGlobals();
    }

    public function setDisplay($display): Response
    {
        if (!in_array($display, $this->displayList)) {
            $display = \Configuration::get(GeneralConfiguration::THEMECORE_DISPLAY_LIST);
        }

        $response = new Response();

        $response->headers->setCookie(new Cookie(
            $this->cookieName,
            $display,
            (new \DateTime('now'))->modify('+ 30 days')->getTimestamp(),
            '/'
        ));

        return $response->sendHeaders();
    }

    public function getDisplay()
    {
        $displayFromCookie = $this->getRequest()->cookies->get($this->cookieName);

        if ($displayFromCookie) {
            return $displayFromCookie;
        }

        return \Configuration::get(GeneralConfiguration::THEMECORE_DISPLAY_LIST);
    }

    public function getDisplayOptions()
    {
        return $this->displayList;
    }
}
