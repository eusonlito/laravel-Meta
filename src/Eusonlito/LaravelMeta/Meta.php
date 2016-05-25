<?php
namespace Eusonlito\LaravelMeta;

class Meta
{
    use FixesTrait;

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
    private $defaults = [
        'title_limit' => 70,
        'description_limit' => 200,
        'image_limit' => 5,
        'tags' => ['Tag', 'MetaName', 'MetaProperty', 'TwitterCard']
    ];

    /**
     * @var array
     */
    private $processed = [];

    /**
     * @var object;
     */
    private static $instance;

    /**
     * @param  array $config
     * @return object
     */
    public static function getInstance(array $config = [])
    {
        return static::$instance ?: (static::$instance = new self($config));
    }

    /**
     * @param  array $config
     * @return this
     */
    public function __construct($config = [])
    {
        if ($config) {
            $this->setConfig($config);
        }

        return $this;
    }

    /**
     * @param  array $config
     * @return this
     */
    public function setConfig(array $config = [])
    {
        $config = $config + $this->config;

        foreach ($this->defaults as $key => $value) {
            if (!array_key_exists($key, $config)) {
                $config[$key] = $value;
            }
        }

        $this->config = $config;

        return $this;
    }

    /**
     * @param  string      $key
     * @param  null|string $default
     * @return string
     */
    public function get($key, $default = null)
    {
        if (empty($this->metas[$key])) {
            return $default;
        }

        return $this->metas[$key];
    }

    /**
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public function set($key, $value)
    {
        $value = $this->plain($value);
        $method = 'set'.$key;

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        return $this->metas[$key] = self::cut($value, $key);
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

        return $this->title = $this->plain($title);
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
            $limit = $this->config['title_limit'] - mb_strlen($title);
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
        if (empty($this->metas['image'])) {
            $this->metas['image'] = [];
        } elseif (count($this->metas['image']) >= $this->config['image_limit']) {
            return;
        }

        $this->metas['image'][] = $value;

        return $value;
    }

    /**
     * @param  string $key
     * @param  string $default
     * @return string
     */
    public function tag($key, $default = null)
    {
        if (!($values = $this->get($key, $default))) {
            return '';
        }

        $values = is_array($values) ? $values : [$values];
        $html = '';

        foreach ($this->config['tags'] as $tag) {
            $class = __NAMESPACE__.'\\Tags\\'.$tag;

            foreach ($values as $value) {
                $html .= $class::tag($key, $value);
            }
        }

        return $html;
    }

    /**
     * @param  string $key
     * @return string
     */
    public function tags(array $keys = [])
    {
        $html = '';

        foreach (($keys ?: array_keys($this->metas)) as $key) {
            $html .= $this->tag($key);
        }

        return $html;
    }
}
