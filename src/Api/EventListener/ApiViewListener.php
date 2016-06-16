<?php


namespace Api\EventListener;


use JMS\Serializer\Serializer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ApiViewListener implements EventSubscriberInterface
{
    /** @var  Serializer */
    private $serializer;

    /**
     * ApiViewListener constructor.
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.view' => array(
                array('onKernelView'),
            ),
        );
    }

    /**
     * Serialize data to json.
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $data = $event->getControllerResult();
        $event->setResponse(new JsonResponse($this->serializer->toArray($data)));
    }
}