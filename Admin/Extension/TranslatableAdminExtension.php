<?php
/**
 * This file is part of the PrestaSonataGedmoDoctrineExtensionsBundle
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SonataGedmoDoctrineExtensionsBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class TranslatableAdminExtension extends AbstractTranslatableAdminExtension
{
    /**
     * @var TranslatableListener
     */
    protected $translatableListener;

    /**
     * @var string
     */
    protected $translatableLocale;

    /**
     * Check if $object is translatable
     *
     * @param  mixed $object
     * @return bool
     */
    protected function isTranslatable($object)
    {
        if (is_null($object)) {
            return false;
        }

        return (
            in_array(
                'Presta\SonataGedmoDoctrineExtensionsBundle\Entity\TranslatableInterface',
                class_implements($object)
            )
        );
    }

    /**
     * @return TranslatableListener
     */
    protected function getTranslatableListener(AdminInterface $admin)
    {
        if ($this->translatableListener == null) {
            $this->translatableListener = $this->getContainer($admin)->get('stof_doctrine_extensions.listener.translatable');
        }

        return $this->translatableListener;
    }

    /**
     * {@inheritdoc}
     */
    public function alterObject(AdminInterface $admin, $object)
    {
        if ($this->isTranslatable($object)) {
            $this->getTranslatableListener($admin)->setTranslatableLocale($this->getTranslatableLocale($admin));
            $this->getTranslatableListener($admin)->setTranslationFallback('');

            $this->getContainer($admin)->get('doctrine')->getManager()->refresh($object);
            $object->setLocale($this->getTranslatableLocale($admin));
        }
    }
} 