<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserEvent
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserEvent
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="event_title", type="string")
     * @Assert\NotBlank()
     */
    private $eventTitle;

    /**
     * @var string
     *
     * @ORM\Column(name="event_content", type="string")
     * @Assert\NotBlank()
     */
    private $eventContent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\NotBlank()
     * @Assert\Type("\DateTime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="userId", type="integer")
     */
    private $userId;

/**
     * @var isactive
     * @ORM\Column(name="isactive", type="boolean", options={"default":false})
     */
    protected $isactive;

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
     * Set date
     *
     * @param \DateTime $date
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Event
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set eventContent
     *
     * @param string $eventContent
     * @return UserEvent
     */
    public function setEventContent($eventContent)
    {
        $this->eventContent = $eventContent;

        return $this;
    }

    /**
     * Get eventContent
     *
     * @return string
     */
    public function getEventContent()
    {
        return $this->eventContent;
    }

    /**
     * Set eventTitle
     *
     * @param string $eventTitle
     * @return UserEvent
     */
    public function setEventTitle($eventTitle)
    {
        $this->eventTitle = $eventTitle;

        return $this;
    }

    /**
     * Get eventTitle
     *
     * @return string
     */
    public function getEventTitle()
    {
        return $this->eventTitle;
    }

    /**
     * Set isactive
     *
     * @param boolean $isactive
     * @return boolean
     */
    public function setIsactive($isactive)
    {
        $this->isactive = $isactive;

        return $this;
    }

    /**
     * Get isactive
     *
     * @return boolean
     */
    public function getIsactive()
    {
        return $this->isactive;
    }

}
