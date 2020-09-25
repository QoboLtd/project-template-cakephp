<?php

namespace App\Avatar;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class Service
{
    /**
     * @var \App\Avatar\AvatarInterface
     */
    protected $avatar;

    /**
     * Constructor method.
     *
     * @param \App\Avatar\AvatarInterface $avatar Avatar instance
     * @param mixed[] $defaults for Avatar Service
     */
    public function __construct(AvatarInterface $avatar = null, array $defaults = [])
    {
        if (empty($avatar)) {
            $source = Hash::get(Configure::read('Avatar.order'), '0', 'App\Avatar\Type\ImageSource');
            $avatar = $this->invokeAvatarSource($source, $defaults);
        }

        Assert::isInstanceOf($avatar, AvatarInterface::class);
        $this->setAvatarSource($avatar);
    }

    /**
     * Set Avatar Source separately
     *
     * @param \App\Avatar\AvatarInterface $avatar object
     *
     * @return void
     */
    public function setAvatarSource(AvatarInterface $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * Get Avatar Source instance
     *
     * @return \App\Avatar\AvatarInterface
     */
    public function getAvatarSource(): AvatarInterface
    {
        return $this->avatar;
    }

    /**
     * Fetches avatar image.
     *
     * @param mixed[] $options if any present on the avatar provider
     *
     * @return string
     */
    public function getImage(array $options): string
    {
        $filename = $this->getImageName($options);
        $options['filename'] = $filename;

        $order = Configure::read('Avatar.order');

        foreach ($order as $classType) {
            $defaultOptions = Configure::read('Avatar.options.' . $classType);
            $options = array_merge($defaultOptions, $options);

            /**
             * @var \App\Avatar\AvatarInterface $source
             */
            $source = $this->invokeAvatarSource($classType, $options);
            $image = $source->get();

            if (!empty($image)) {
                return $image;
            }
        }

        // using anonymous image as the default.
        $defaultAvatar = Configure::read('Avatar.defaultImage');
        /**
         * @var \App\Avatar\AvatarInterface $imageSource
         */
        $imageSource = $this->invokeAvatarSource('ImageSource', ['src' => $defaultAvatar]);
        $this->setAvatarSource($imageSource);

        return $this->avatar->get();
    }

    /**
     * Invoke class object base on its name
     *
     * @param string $class name
     * @param mixed[] $options if any required
     *
     * @throws InvalidArgumentException in case we couldn't allocate AvatarSource service
     *
     * @return \App\Avatar\AvatarInterface $instance of the class
     */
    public function invokeAvatarSource(string $class, array $options = []): ?AvatarInterface
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException("Avatar Source [$class] wasn't found");
        }

        $instance = new $class($options);

        return $instance;
    }

    /**
     * Get Image Name of the service
     *
     * @param mixed[] $options config
     * @param bool $ext whether to use file extension or not
     *
     * @return string $name of the file.
     */
    public function getImageName(array $options, bool $ext = true): string
    {
        $extension = ($ext) ? Configure::read('Avatar.extension') : '';
        $name = $options['id'] . $extension;

        return $name;
    }

    /**
     * Saving image resource
     *
     * @param string $file path including its name
     * @param resource $resource of the file
     *
     * @return bool $status of the file save.
     */
    public function saveImage(string $file, $resource): bool
    {
        $status = $this->avatar->processAvatarResource($file, $resource);

        return $status;
    }

    /**
     * Remove image resource from memory
     *
     * @param resource $resource of the file
     *
     * @return bool whether the resource was removed from the memory
     */
    public function removeImageResource($resource): bool
    {
        return $this->avatar->removeAvatarResource($resource);
    }

    /**
     * is uploading resource is Image
     *
     * @param mixed[] $data from the form
     *
     * @return bool $result whether mime type is image.
     */
    public function isImage(array $data): bool
    {
        $result = false;
        list($mimeGroup, ) = explode('/', $data['type']);

        if ('image' == strtolower($mimeGroup)) {
            $result = true;
        }

        return $result;
    }

    /**
     * File size check
     *
     * @param mixed[] $data of the image from the form
     *
     * @return bool $result comparing with config/avatar.
     */
    public function isAllowedSize(array $data): bool
    {
        $result = false;
        $allowedSize = Configure::read('Avatar.fileSize');

        if ($allowedSize > $data['size']) {
            $result = true;
        }

        return $result;
    }

    /**
     * Get Image resource stream
     *
     * @param mixed $data of the file origin (form, base64 data)
     * @param bool $isBase64 flag to check how to handle $data
     *
     * @return resource|false $source of the image
     */
    public function getImageResource($data, bool $isBase64 = false)
    {
        $source = false;

        /**
         * @var resource $source
         */
        if ($isBase64) {
            $source = imagecreatefromstring((string)file_get_contents($data));

            return $source;
        }

        $extension = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));

        if ('png' == $extension) {
            /**
             * @var resource $source
             */
            $source = imagecreatefrompng($data['tmp_name']);
        }

        if (in_array($extension, ['jpg', 'jpeg'])) {
            $source = imagecreatefromjpeg($data['tmp_name']);
        }

        return $source;
    }
}
