<?php
/**
 * This file is part of the PrestaSonataGedmoDoctrineExtensionsBundle
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\SonataGedmoDoctrineExtensionsBundle\EventListener;

use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class LocaleSwitcherListener
{
    /**
     * @param  BlockEvent
     */
    public function onBlock(BlockEvent $event)
    {
        $block = new Block();
        $block->setSettings($event->getSettings());
        $block->setName('presta_sonata_gedmo.block.locale_switcher');
        $block->setType('presta_sonata_gedmo.block.locale_switcher');

        $event->addBlock($block);
    }
}
