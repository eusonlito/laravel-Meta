<?php
namespace Laravel\Meta;

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
        if (!isset($this->metas['image'])) {
            $this->metas['image'] = [];
        } elseif (count($this->metas['image']) >= $this->config['image_limit']) {
            return;
        }

        return $this->metas['image'][] = $value;
    }

    /**
     * @param  string $key
     * @return string
     */
    public function tag($key)
    {
        if (empty($this->metas[$key])) {
            return '';
        }

        $method = 'tag'.$key;

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return $this->tagDefault($key);
    }

    /**
     * @param  string $key
     * @return string
     */
    private function tagDefault($key)
    {
        return '<meta name="'.$key.'" content="'.$this->metas[$key].'" />'
            .'<meta property="og:'.$key.'" content="'.$this->metas[$key].'" />';
    }

    /**
     * @param  string $key
     * @return string
     */
    private function tagImage()
    {
        $html = '';

        foreach ($this->metas['image'] as $image) {
            $html .= '<meta name="image" content="'.$image.'" />'
                .'<meta property="og:image" content="'.$image.'" />'
                .'<link rel="image_src" href="'.$image.'" />';
        }

        return $html;
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

        if ($length <= (int)$limit) {
            return $text;
        }

        $text = substr($text, 0, ($limit -= 3));

        if ($space = strrpos($text, ' ')) {
            $text = substr($text, 0, $space);
        }

        return $text.'...';
    }
}
