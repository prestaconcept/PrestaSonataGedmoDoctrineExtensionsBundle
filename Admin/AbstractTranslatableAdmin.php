<?php
/**
 * This file is part of the PrestaSonataGedmoDoctrineExtensionsBundle
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\SonataGedmoDoctrineExtensionsBundle\Admin;

use Presta\SonataAdminExtendedBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Gedmo\Translatable\TranslatableListener;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class AbstractTranslatableAdmin extends AbstractAdmin
{
    /**
     * Request parameter
     */
    const TRANSLATABLE_LOCALE_PARAMETER = 'tl';

    /**
     * @var TranslatableListener
     */
    protected $translatableListener;

    /**
     * @var string
     */
    protected $translatableLocale;

    /**
     * @return string
     */
    protected function getDefaultLocale()
    {
        return $this->getConfigurationPool()
            ->getContainer()
            ->getParameter('presta_sonata_admin_extended.default_locale');
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->getConfigurationPool()->getContainer();
    }

    /**
     * @return TranslatableListener
     */
    protected function getTranslatableListener()
    {
        if ($this->translatableListener == null) {
            $this->translatableListener = $this->getContainer()->get('stof_doctrine_extensions.listener.translatable');
        }

        return $this->translatableListener;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->getContainer()->getParameter('locales');
    }

    /**
     * Return current translatable locale
     * ie: the locale used to load object translations != current request locale
     *
     * @return string
     */
    public function getTranslatableLocale()
    {
        if ($this->translatableLocale == null) {
            if ($this->request) {
                $this->translatableLocale = $this->getRequest()->get(self::TRANSLATABLE_LOCALE_PARAMETER);
            }
            if ($this->translatableLocale == null) {
                $this->translatableLocale = $this->getDefaultLocale();
            }
        }

        return $this->translatableLocale;
    }

    /**
     * Check if $object is translatable
     *
     * @param  mixed $object
     * @return bool
     */
    public function isTranslatable($object)
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
     * {@inheritdoc}
     */
    public function generateUrl($name, array $parameters = array(), $absolute = false)
    {
        if ($name == 'create' || $name == 'edit') {
            $parameters = $parameters + array(self::TRANSLATABLE_LOCALE_PARAMETER => $this->getTranslatableLocale());
        }

        return parent::generateUrl($name, $parameters, $absolute);
    }

    /**
     * {@inheritdoc}
     */
    public function getObject($id)
    {
        $object = parent::getObject($id);

        if ($this->isTranslatable($object)) {
            $this->getTranslatableListener()->setTranslatableLocale($this->getTranslatableLocale());
            $this->getTranslatableListener()->setTranslationFallback('');

            $this->getContainer()->get('doctrine')->getManager()->refresh($object);
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     *
     * When object is persisted in a locale which is not the default one
     * We create the new translations and set the default values to ''
     */
    public function postPersist($object)
    {
        $locale = $this->getTranslatableLocale();
        if ($locale != $this->getDefaultLocale()) {
            $em = $this->getContainer()->get('doctrine')->getEntityManager();

            $gedmoConfiguration = $this->getTranslatableListener()->getConfiguration($em, get_class($object));
            $translationClass   = $gedmoConfiguration['translationClass'];
            $translatableFields = isset($gedmoConfiguration['fields']) ? $gedmoConfiguration['fields'] : array();

            foreach ($translatableFields as $fieldName) {
                //Rework field name to call getter / setter method
                $fieldFunction = str_replace('_', ' ', $fieldName);
                $fieldFunction = ucwords($fieldFunction);
                $fieldFunction = str_replace(' ', '', $fieldFunction);

                //Add new translation
                $object->addTranslation(
                    new $translationClass($locale, $fieldName, $object->{'get' . $fieldFunction}())
                );

                //reset base
                $object->{'set' . $fieldFunction}('');
            }
            $em->persist($object);
            $em->flush();
        }
    }

    /**
     * Add a translated field in the listing
     *
     * @param  ListMapper $listMapper
     * @param  string     $fieldName
     * @return ListMapper
     */
    protected function addTranslatedField(ListMapper $listMapper, $fieldName)
    {
        //On supprimer les autres champs
        foreach ($this->listFieldDescriptions as $fieldDescription) {
            if (strcmp($fieldDescription->getName(), '_action') === 0) {
                continue;
            }
            $listMapper->remove($fieldDescription->getName());
        }

        //On ajoute un identifier sur la langue par défaut
        $listMapper->addIdentifier($fieldName);

        //On ajoute les autres langues
        foreach ($this->getLocales() as $locale) {
            if (strcmp($locale, $this->getDefaultLocale()) === 0) {
                continue;
            }
            $fieldDescription = new TranslatedFieldDescription();
            $fieldDescription->setName($fieldName.'#'.$locale);
            $fieldDescription->setOptions(array('label' => 'list.label_translation_' . $locale));
            $listMapper->add($fieldDescription);
        }

        return $listMapper;
    }
}
