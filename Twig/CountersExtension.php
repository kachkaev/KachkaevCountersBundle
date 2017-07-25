<?php
namespace Kachkaev\CountersBundle\Twig;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CountersExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        $functions = array();

        $mappings = array('getCounterId' => 'getCounterId', 'countersDisabled' => 'countersDisabled',);

        foreach ($mappings as $twigFunction => $method) {
            $functions[$twigFunction] = new \Twig_SimpleFunction($method, array($this, $method));
        }

        $safeMappings = array();

        foreach ($safeMappings as $twigFunction => $method) {
            $functions[$twigFunction] = new \Twig_SimpleFunction($method, array($this, $method), array('is_safe' => array('html')));
        }

        return $functions;
    }

    public function countersDisabled()
    {
        return $this->container->getParameter('kachkaev_counters.disabled') || $this->container->get('request_stack')->getMasterRequest()->cookies->get($this->container->getParameter('kachkaev_counters.cookie'));
    }

    public function getCounterId($name)
    {
        return $this->container->getParameter("kachkaev_counters.$name.id");
    }

    public function getName()
    {
        return 'KachkaevCountersExtension';
    }
}
