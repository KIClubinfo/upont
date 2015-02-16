<?php

namespace KI\UpontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contient une ligne de log de connexion
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Nom d'utilisateur (username)
     * @ORM\Column(name="username", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $username;
    
    /**
     * Date (timestamp)
     * @ORM\Column(name="date", type="integer")
     * @Expose
     * @Assert\Type("integer")
     */
    protected $date;
    
    /**
     * Verbe HTTP
     * @ORM\Column(name="method", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $method;
    
    /**
     * Route demandée
     * @ORM\Column(name="url", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $url;
    
    /**
     * Paramètres
     * @ORM\Column(name="params", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $params;
    
    /**
     * Code HTTP de réponse
     * @ORM\Column(name="code", type="integer")
     * @Expose
     * @Assert\Type("integer")
     */
    protected $code;
    
    /**
     * Adresse IP
     * @ORM\Column(name="ip", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $ip;
    
    /**
     * Navigateur internet
     * @ORM\Column(name="browser", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $browser;
    
    /**
     * Système d'exploitation
     * @ORM\Column(name="system", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $system;
    
    /**
     * User Agent complet
     * @ORM\Column(name="agent", type="string")
     * @Expose
     * @Assert\Type("string")
     */
    protected $agent;
    
    
    
    
    
    
    
    
    //===== GENERATED AUTOMATICALLY =====//


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
     * Set username
     *
     * @param string $username
     * @return Log
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Log
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return Log
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Log
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set params
     *
     * @param string $params
     * @return Log
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return string 
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set code
     *
     * @param integer $code
     * @return Log
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Log
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set browser
     *
     * @param string $browser
     * @return Log
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;

        return $this;
    }

    /**
     * Get browser
     *
     * @return string 
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Set system
     *
     * @param string $system
     * @return Log
     */
    public function setSystem($system)
    {
        $this->system = $system;

        return $this;
    }

    /**
     * Get system
     *
     * @return string 
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * Set agent
     *
     * @param string $agent
     * @return Log
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return string 
     */
    public function getAgent()
    {
        return $this->agent;
    }
}
