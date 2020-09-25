<?php

namespace App\Avatar\Type;

use App\Avatar\AbstractAvatar;
use Cake\Http\Client;

final class Gravatar extends AbstractAvatar
{
    /**
     * Gravatar default options.
     *
     * @var array
     */
    private $options = [
        'email' => '',
        'size' => 160,
        'default' => '404',
        'rating' => 'g',
    ];

    /**
     * {@inheritDoc}
     *
     * @param mixed[] $options
     */
    public function __construct(array $options)
    {
        $this->options = array_merge($this->options, $options);

        $this->options['email'] = md5(strtolower(trim($this->options['email'])));
    }

    /**
     * {@inheritDoc}
     */
    public function get(): string
    {
        $result = '';
        $saved = false;
        $filename = $this->getAvatarUrl($this->options);
        $file = WWW_ROOT . $filename;

        if (file_exists($file)) {
            return $filename;
        }

        $gravatarBaseUrl = 'https://www.gravatar.com/avatar/';

        $http = new Client();

        $response = $http->get($gravatarBaseUrl . $this->options['email'], [
            'size' => $this->options['size'],
            'default' => $this->options['default'],
            'rating' => $this->options['rating'],
        ]);

        if (404 == $response->getStatusCode()) {
            return $result;
        }

        $resource = imagecreatefromstring($response->getBody());

        if ($resource) {
            $saved = $this->processAvatarResource($file, $resource);
        }

        if ($saved) {
            $result = $filename;
        }

        return $result;
    }
}
