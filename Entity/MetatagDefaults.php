<?php

namespace Copiaincolla\MetaTagsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Copiaincolla\MetaTagsBundle\Entity\Metatag;

/**
 * Copiaincolla\MetaTagsBundle\Entity\MetatagDefaults
 *
 * @ORM\Table(name="ci_metatag_defaults")
 * @ORM\Entity(repositoryClass="Copiaincolla\MetaTagsBundle\Repository\MetatagDefaultsRepository")
 */
class MetatagDefaults
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $pathRegex
     *
     * @ORM\Column(name="path_regex", type="string", length=255, nullable=true)
     */
    private $pathRegex;

    /**
     * @var string $importance
     *
     * @ORM\Column(name="importance", type="integer", nullable=true)
     */
    private $importance;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private $title;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string $keywords
     *
     * @ORM\Column(name="keywords", type="text", nullable=true)
     */
    private $keywords;

    /**
     * @var string $robots
     *
     * @ORM\Column(name="robots", type="text", nullable=true)
     */
    private $robots;

    /**
     * @var string $googlebot
     *
     * @ORM\Column(name="googlebot", type="text", nullable=true)
     */
    private $googlebot;

    /**
     * @var string $author
     *
     * @ORM\Column(name="author", type="text", nullable=true)
     */
    private $author;

    /**
     * @var string $language
     *
     * @ORM\Column(name="language", type="text", nullable=true)
     */
    private $language;

    /**
     * @var string $ogTitle
     *
     * @ORM\Column(name="og_title", type="text", nullable=true)
     */
    private $ogTitle;

    /**
     * @var string $ogDescription
     *
     * @ORM\Column(name="og_description", type="text", nullable=true)
     */
    private $ogDescription;

    /**
     * @var string $ogImage
     *
     * @ORM\Column(name="og_image", type="text", nullable=true)
     */
    private $ogImage;

    /******************************************************************************************
     *  CUSTOM FUNCTIONS
     *****************************************************************************************/

    /**
     * return an array representation
     */
    public function toArray()
    {
        $metaTags = array();
        $metatagsAssociation = Metatag::getMetatagsAssociation();

        foreach ($metatagsAssociation as $metaTagName => $metaTagValue) {
            try {
                $metaTags[$metaTagName] = call_user_func(array($this, $metaTagValue));
            } catch (\Exception $e) {
                throw new Exception("Undefined method supported metatag \"{$metaTagName}\" in " . get_class() . " at line " . $e->getLine(), '500');
            }
        }

        return $metaTags;
    }

    /******************************************************************************************
     *  GETTER AND SETTER
     *****************************************************************************************/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pathRegex
     *
     * @param string $pathRegex
     * @return string
     */
    public function setPathRegex($pathRegex)
    {
        $this->pathRegex = $pathRegex;

        return $this;
    }

    /**
     * Get pathRegex
     *
     * @return string
     */
    public function getPathRegex()
    {
        return $this->pathRegex;
    }

    /**
     * Set importance
     *
     * @param string $importance
     * @return string
     */
    public function setImportance($importance)
    {
        $this->importance = $importance;

        return $this;
    }

    /**
     * Get importance
     *
     * @return string
     */
    public function getImportance()
    {
        return $this->importance;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return MetatagDefaults
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return MetatagDefaults
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return MetatagDefaults
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set robots
     *
     * @param string $robots
     * @return MetatagDefaults
     */
    public function setRobots($robots)
    {
        $this->robots = $robots;

        return $this;
    }

    /**
     * Get robots
     *
     * @return string
     */
    public function getRobots()
    {
        return $this->robots;
    }

    /**
     * Set googlebot
     *
     * @param string $googlebot
     * @return MetatagDefaults
     */
    public function setGooglebot($googlebot)
    {
        $this->googlebot = $googlebot;

        return $this;
    }

    /**
     * Get googlebot
     *
     * @return string
     */
    public function getGooglebot()
    {
        return $this->googlebot;
    }

    /**
     * Set author
     *
     * @param string $author
     * @return MetatagDefaults
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return MetatagDefaults
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set ogTitle
     *
     * @param string $ogTitle
     * @return MetatagDefaults
     */
    public function setOgTitle($ogTitle)
    {
        $this->ogTitle = $ogTitle;

        return $this;
    }

    /**
     * Get ogTitle
     *
     * @return string
     */
    public function getOgTitle()
    {
        return $this->ogTitle;
    }

    /**
     * Set ogDescription
     *
     * @param string $ogDescription
     * @return MetatagDefaults
     */
    public function setOgDescription($ogDescription)
    {
        $this->ogDescription = $ogDescription;

        return $this;
    }

    /**
     * Get ogDescription
     *
     * @return string
     */
    public function getOgDescription()
    {
        return $this->ogDescription;
    }

    /**
     * Set ogImage
     *
     * @param string $ogImage
     * @return MetatagDefaults
     */
    public function setOgImage($ogImage)
    {
        $this->ogImage = $ogImage;

        return $this;
    }

    /**
     * Get ogImage
     *
     * @return string
     */
    public function getOgImage()
    {
        return $this->ogImage;
    }
}