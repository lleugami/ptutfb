<?php

namespace Metinet\Bundle\FacebookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserWatch
 *
 * @ORM\Table(name="user_watch")
 * @ORM\Entity(repositoryClass="Metinet\Bundle\FacebookBundle\Repository\UserWatchRepository")
 */
class UserWatch
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
     * @var integer
     *
     * @ORM\Column(name="episode", type="integer")
     */
    private $episode;

    /**
     * @var integer
     *
     * @ORM\Column(name="season", type="integer")
     */
    private $season;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="watched_at", type="datetime")
     */
    private $watchedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userwatchs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Tvshow", inversedBy="userwatchs")
     * @ORM\JoinColumn(name="tvshow_id", referencedColumnName="id")
     */
    protected $tvshow;

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
     * Set episode
     *
     * @param integer $episode
     * @return UserWatch
     */
    public function setEpisode($episode)
    {
        $this->episode = $episode;

        return $this;
    }

    /**
     * Get episode
     *
     * @return integer
     */
    public function getEpisode()
    {
        return $this->episode;
    }

    /**
     * Set season
     *
     * @param integer $season
     * @return UserWatch
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return integer
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set watchedAt
     *
     * @param \DateTime $watchedAt
     * @return UserWatch
     */
    public function setWatchedAt($watchedAt)
    {
        $this->watchedAt = $watchedAt;

        return $this;
    }

    /**
     * Get watchedAt
     *
     * @return \DateTime
     */
    public function getWatchedAt()
    {
        return $this->watchedAt;
    }
}
