<?php
/**
 * This file is part of the PrestaSonataGedmoDoctrineExtensionsBundle.
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\SonataGedmoDoctrineExtensionsBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class LocaleSwitcherBlockService extends BaseBlockService
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'admin'     => null,
                'object'    => null,
                'template'  => 'PrestaSonataGedmoDoctrineExtensionsBundle:Block:block_locale_switcher.html.twig',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
    }
}
