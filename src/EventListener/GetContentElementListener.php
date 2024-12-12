<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\EventListener;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('getContentElement')]
class GetContentElementListener
{
    public function __invoke(ContentModel $contentModel, string $buffer, $element): string
    {
        if ($contentModel->type=="minkorrekt_thema") {
            $buffer = sprintf("<div>%s</div>%s", (string) $contentModel->minkorrekt_thema_art, $buffer);
        }

        return $buffer;
    }
}
