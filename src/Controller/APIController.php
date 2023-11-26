<?php

declare(strict_types=1);

/*
 * minkorrekt-history
 *  from lindesbs
 */

namespace lindesbs\minkorrekt\Controller;

use Contao\CoreBundle\Framework\ContaoFramework;
use lindesbs\minkorrekt\Models\MinkorrektFolgenModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{

    public function __construct(private readonly ContaoFramework $framework)
    {
    }

    #[Route('/api/folgen', name: 'api_folgen_list', defaults: ['_scope' => 'frontend'])]
    public function folgenListe(): JsonResponse
    {
        $this->framework->initialize();

        $objFolgen = MinkorrektFolgenModel::findBy('published', true);

        // Convert products to an array or format as needed
        $data = [];

        foreach ($objFolgen as $folge) {
            $data[] = [
                'folge' => $folge->row()
            ];
        }

        return new JsonResponse($data);
    }
}