<?php
/**
 * This file is part of the PrestaSonataGedmoDoctrineExtensionsBundle
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\SonataGedmoDoctrineExtensionsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entitée de base pour factoriser les méthodes liées aux traductions Gedmo
 *
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 * @see http://www.gediminasm.org/article/translatable-behavior-extension-for-doctrine-2 : Personal translations
 */
abstract class AbstractTranslatable
{
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * @var string
     */
    protected $locale;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * Set locale for Gedmo translation behavior
     *
     * @param  string $locale
     * @return AbstractTranslatable
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param  AbstractPersonalTranslation $translation
     * @return AbstractTranslatable
     */
    public function addTranslation($translation)
    {
        if (!$this->translations->contains($translation)) {
            $translation->setObject($this);
            $this->translations->add($translation);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Retourne la traduction d'un champ pour une locale
     *
     * @param  $field
     * @param  $locale
     * @return string
     */
    public function getTranslation($field, $locale)
    {
        foreach ($this->getTranslations() as $translation) {
            if (strcmp($translation->getField(), $field) === 0 && strcmp($translation->getLocale(), $locale) === 0) {
                return $translation->getContent();
            }
        }

        return null;
    }

}
