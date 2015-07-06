<?php
namespace Eusonlito\LaravelMeta;

class Meta
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $metas = [];

    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $og = [
        'title', 'type', 'image', 'url', 'audio', 'description',
        'determiner', 'locale', 'site_name', 'video'
    ];

    /**
     * @var array
     */
    private $processed = [];

    /**
     * @param  array $config
     * @return this
     */
    public function __construct(array $config = [])
    {
        if (!isset($config['title_limit'])) {
            $config['title_limit'] = 70;
        }

        if (!isset($config['description_limit'])) {
            $config['description_limit'] = 200;
        }

        if (!isset($config['image_limit'])) {
            $config['image_limit'] = 5;
        }

        $this->config = $config;
    }

    /**
     * @param  string $title
     * @return string
     */
    public function title($title = null)
    {
        if ($title === null) {
            return $this->title;
        }

        $this->title = null;

        $this->set('title', $title);

        return $this->title = $this->fix($title);
    }

    /**
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public function meta($key, $value = null)
    {
        if ($value === null) {
            return $this->get($key);
        }

        return $this->set($key, $value);
    }

    /**
     * @param  string $key
     * @return string
     */
    public function get($key)
    {
        if (empty($this->metas[$key])) {
            return null;
        }

        return $this->metas[$key];
    }

    /**
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public function set($key, $value = null)
    {
        $value = $this->fix($value);

        $method = 'set'.$key;

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        return $this->metas[$key] = self::cut($value, $key);
    }

    /**
     * @param  string $value
     * @return string
     */
    private function setTitle($value)
    {
        $title = $this->title;

        if ($title && $this->config['title_limit']) {
            $title = ' - '.$title;
            $limit = $this->config['title_limit'] - strlen($title);
        } else {
            $limit = 'title';
        }

        return $this->metas['title'] = self::cut($value, $limit).$title;
    }

    /**
     * @param  string $value
     * @return string
     */
    private function setImage($value)
    {
        if (!array_key_exists('image', $this->metas)) {
            $this->metas['image'] = [];
        }

        if (count($this->metas['image']) < $this->config['image_limit']) {
            return $this->metas['image'][] = $value;
        }
    }

    /**
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public function tag($key, $value = null)
    {
        $this->processed = [];

        $method = 'tag'.ucfirst($key);

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        return $this->tagDefault($key, $value);
    }

    /**
     * @param  string $key
     * @param  string $value
     * @return string
     */
    private function tagDefault($key, $value = null)
    {
        return $this->tagMetaName($key, $value).$this->tagMetaProperty($key, $value);
    }

    /**
     * @param  string $key
     * @param  mixed $images
     * @return string
     */
    public function tagImage($images = null)
    {
        if (empty($images) && !array_key_exists('image', $this->metas)) {
            return '';
        }

        $html = '';

        foreach ((array)($images ?: $this->metas['image']) as $image) {
            if ($tag = $this->tagDefault('image', $image)) {
                $html .= $tag.'<link rel="image_src" href="'.$image.'" />';
            }
        }

        return $html;
    }

    /**
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public function tagMetaName($key, $value = null)
    {
        return $this->tagString('name', $key, $value);
    }

    /**
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public function tagMetaProperty($key, $value = null)
    {
        return $this->tagString('property', $key, $value);
    }

    /**
     * @param  string $name
     * @param  string $key
     * @param  string $value
     * @return string
     */
    private function tagString($name, $key, $value = null)
    {
        if (empty($value) && !array_key_exists($key, $this->metas)) {
            return '';
        }

        $value = $value ?: $this->metas[$key];
        $tag = '<meta '.$name.'="'.$key.'" content="'.$value.'" />';

        if ((strpos($key, 'og:') !== 0) && in_array($key, $this->og, true)) {
            $tag .= $this->tagString('property', 'og:'.$key, $value);
        }

        return $tag;
    }

    /**
     * @param  string $text
     * @return string
     */
    private function fix($text)
    {
        $text = preg_replace('/<[^>]+>/', ' ', $text);
        $text = preg_replace('/[\r\n\s]+/', ' ', $text);

        return trim(str_replace('"', '&quot;', $text));
    }

    /**
     * @param  string $text
     * @param  string $key
     * @return string
     */
    private function cut($text, $key)
    {
        if (is_string($key) && isset($this->config[$key.'_limit'])) {
            $limit = $this->config[$key.'_limit'];
        } elseif (is_integer($key)) {
            $limit = $key;
        } else {
            return $text;
        }

        $length = strlen($text);

        if ($length <= (int) $limit) {
            return $text;
        }

        $text = substr($text, 0, ($limit -= 3));

        if ($space = strrpos($text, ' ')) {
            $text = substr($text, 0, $space);
        }

        return $text.'...';
    }
}
