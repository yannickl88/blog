<?php
namespace App\Entity;

class Author implements \JsonSerializable
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $bio;

    /**
     * @param string $uuid
     * @param string $name
     * @param string $email
     * @param string $bio
     */
    public function __construct($uuid, $name, $email, $bio)
    {
        $this->uuid  = $uuid;
        $this->name  = $name;
        $this->email = $email;
        $this->bio   = $bio;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return substr($this->name, 0, strpos($this->name, ' '));
    }

    /**
     * @param int $size
     * @return string
     */
    public function getGravatarUrl($size = 200)
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '?s=' . $size;
    }

    /**
     * @return string
     */
    public function getBio()
    {
        return file_get_contents($this->bio);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'uuid'  => $this->uuid,
            'name'  => $this->name,
            'email' => $this->email,
            'bio'   => $this->bio,
        ];
    }
}
