<?php

namespace App\Entity;

class Author implements \JsonSerializable
{
    private $uuid;
    private $name;
    private $email;
    private $bio;
    private $urls;

    /**
     * @param string   $uuid
     * @param string   $name
     * @param string   $email
     * @param string   $bio
     * @param string[] $urls
     */
    public function __construct(string $uuid, string $name, string $email, string $bio, array $urls)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->email = $email;
        $this->bio = $bio;
        $this->urls = $urls;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return trim(substr($this->name, 0, strpos($this->name, ' ')));
    }

    public function getGravatarUrl(int $size = 200): string
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'?s='.$size;
    }

    public function getBio(): string
    {
        return file_get_contents($this->bio);
    }

    /**
     * @return string[]
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'urls' => $this->urls,
        ];
    }
}
