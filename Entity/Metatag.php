<?php

namespace Copiaincolla\MetaTagsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Copiaincolla\MetaTagsBundle\Entity\Metatag
 *
 * @ORM\Table(name="ci_metatag")
 * @ORM\Entity(repositoryClass="Copiaincolla\MetaTagsBundle\Repository\MetatagRepository")
 */
class Metatag
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
     * @var string $route
     *
     * @ORM\Column(name="route", type="string", length=255)
     */
    private $route;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string $keywords
     *
     * @ORM\Column(name="keywords", type="text")
     */
    private $keywords;

    /**
     * @var string $author
     *
     * @ORM\Column(name="author", type="text")
     */
    private $author;

    /******************************************************************************************
     *  CUSTOM FUNCTIONS
     *****************************************************************************************/

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
     * Set route
     *
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set metaDescription
     *
     * @param text $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * Get metaDescription
     *
     * @return text
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set title
     *
     * @param text $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return text
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keywords
     *
     * @param text $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get keywords
     *
     * @return text
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set author
     *
     * @param text $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * Get author
     *
     * @return text
     */
    public function getAuthor()
    {
        return $this->author;
    }

}
