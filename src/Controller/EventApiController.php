<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventApiController extends AbstractController
{
    /**
     * @Route("/_api/get_events", name="get_events", methods={"GET"})
     */
    public function getEvents(): JsonResponse
    {
        // Diese Daten könnten aus Contao-Tabellen kommen
        $events = [
            ['id' => 1, 'name' => 'Event 1', 'description' => 'Beschreibung 1'],
            ['id' => 2, 'name' => 'Event 2', 'description' => 'Beschreibung 2'],
        ];

        return new JsonResponse($events);
    }
}
