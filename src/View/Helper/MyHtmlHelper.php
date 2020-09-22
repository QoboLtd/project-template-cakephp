<?php
namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\View\Helper\HtmlHelper;
use Qobo\Utils\Utility\User;
use RolesCapabilities\Access\AccessFactory;

class MyHtmlHelper extends HtmlHelper
{
    /**
     * Template for help tooltip
     *
     * @param string $message Help message
     * @return string
     */
    public function help(string $message): string
    {
        $dataToggle = Configure::read("CsvMigrations.HelperConfig.dataToggle");
        $dataPlacement = Configure::read("CsvMigrations.HelperConfig.dataPlacement");

        return empty($message) ? '' : 'data-toggle="' . $dataToggle . '" data-placement="' . $dataPlacement . '" data-original-title="' . __($message) . '"';
    }

    /**
     * Creates an HTML link or a block, depending on user permissions.
     *
     * @param string|array $title The content to be wrapped by `<a>` tags.
     *   Can be an array if $url is null. If $url is null, $title will be used as both the URL and title.
     * @param string|array|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array $options Array of options and HTML attributes.
     * @return null|string An `<a />` or <div /> element.
     * @link https://book.cakephp.org/3/en/views/helpers.html#aliasing-helpers
     */
    public function link($title, $url = null, array $options = [])
    {
        $rawUrl = $url;
        $escapeTitle = true;
        if ($url !== null) {
            $url = $this->Url->build($url, $options);
            unset($options['fullBase']);
        } else {
            $url = $this->Url->build($title);
            $title = htmlspecialchars_decode($url, ENT_QUOTES);
            $title = h(urldecode($title));
            $escapeTitle = false;
        }

        if (isset($options['escapeTitle'])) {
            $escapeTitle = $options['escapeTitle'];
            unset($options['escapeTitle']);
        } elseif (isset($options['escape'])) {
            $escapeTitle = $options['escape'];
        }

        if ($escapeTitle === true) {
            $title = h($title);
        } elseif (is_string($escapeTitle)) {
            $title = htmlentities($title, ENT_QUOTES, $escapeTitle);
        }

        $confirmMessage = null;
        if (isset($options['confirm'])) {
            $confirmMessage = $options['confirm'];
            unset($options['confirm']);
        }
        if ($confirmMessage) {
            $options['onclick'] = $this->_confirm($confirmMessage, 'return true;', 'return false;', $options);
        }

        $templater = $this->templater();

        if (!is_array($rawUrl) && (filter_var($rawUrl, FILTER_VALIDATE_URL) || strpos((string)$rawUrl, '#') === 0)) {
            $rawUrl = [
                'controller' => '',
                'action' => '',
            ];
        }

        if (!is_array($rawUrl)) {
            $rawUrl = Router::getRouteCollection()->parse((string)$rawUrl);
        }

        $rawUrl['controller'] = !isset($rawUrl['controller']) ?: ucfirst($rawUrl['controller']);

        $accessFactory = new AccessFactory();
        $user = User::getCurrentUser();
        if (empty($user) || $accessFactory->hasAccess($rawUrl, $user)) {
            return $templater->format('link', [
                'url' => $url,
                'attrs' => $templater->formatAttributes($options),
                'content' => $title,
            ]);
        }

        return $templater->format('block', [
           'content' => $title,
        ]);
    }

    /**
     * Return style tag with random background image for login
     * page.
     *
     * @return string
     */
    public function backgroundLogoImage(): string
    {
        $result = '';
        $path = '/img/login/' . (string)Configure::read('Theme.backgroundImages') . '/';
        $images = (new Folder(WWW_ROOT . $path))->find('.*\.png|jpg|jpeg', true);

        if (! empty($images)) {
            $result = $this->tag('style', sprintf(
                '.login-page {%s}',
                $this->style(['background-image' => 'url(' . $path . '/' . $images[array_rand($images)] . ')'])
            ));
        }

        return $result;
    }
}
