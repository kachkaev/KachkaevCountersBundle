<?php
namespace Kachkaev\CountersBundle\Listener;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * If /?counters_off or /?counters_on present in the request URL, kachkaev_counters cookie is set/unset
 *
 */
class CountersOnOffListener
{
    protected $onOffTrigger;
    protected $cookie;
    
    public function __construct($onOffTrigger, $cookie)
    {
        $this->onOffTrigger = $onOffTrigger;    
        $this->cookie = $cookie;    
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        // Setting counters cookie when there is counters_off in the URL
        if ($event->getRequest()->query->get($this->onOffTrigger.'_off') !== null) {

            $event->getRequest()->getSession()->setFlash($this->cookie, 'off');

            $cleanURI = str_replace(sprintf('?%s_off', $this->onOffTrigger), '', $event->getRequest()->server->get('REQUEST_URI'));
            $response = new RedirectResponse($cleanURI);
            $cookie = new Cookie($this->cookie, true, '2030-01-01', '/', null, null, false);
            $response->headers->setCookie($cookie);
            $event->setResponse($response);
        }

        // Deleting counters cookie when there is counters_on in the URL
        if ($event->getRequest()->query->get($this->onOffTrigger.'_on') !== null) {

            $event->getRequest()->getSession()->setFlash($this->cookie, 'on');

            $cleanURI = str_replace(sprintf('?%s_on', $this->onOffTrigger), '', $event->getRequest()->server->get('REQUEST_URI'));
            $response = new RedirectResponse($cleanURI);
            $cookie = new Cookie($this->cookie);
            $response->headers->setCookie($cookie);
            $event->setResponse($response);
        }

    }
}
