<?php
namespace Eusonlito\LaravelMeta;

use Eusonlito\LaravelMeta\Tags\MetaProduct;

class Meta
{
    use FixesTrait;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $metas = [];

    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $defaults = [
        'title_limit' => 70,
        'description_limit' => 200,
        'image_limit' => 5,
        'tags' => ['Tag', 'MetaName', 'MetaProperty', 'MetaProduct', 'TwitterCard'],
        'separator' => ' - '
    ];

    /**
     * @var object;
     */
    protected static $instance;

    /**
     * @param  array $config = []
     *
     * @return object
     */
    public static function getInstance(array $config = [])
    {
        return static::$instance ?: (static::$instance = new self($config));
    }

    /**
     * @param  array $config = []
     *
     * @return this
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            $this->setConfig($config);
        }

        $this->metas['image'] = [];
    }

    /**
     * @param  array $config = []
     *
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
     * @param  string|null $title = null
     *
     * @return string
     */
    public function title($title = null)
    {
        if ($title === null) {
            return $this->title;
        }

        $title = $this->plain($title);

        if (empty($this->metas['title'])) {
            $this->metas['title'] = $title;
        }

        return $this->title = $title;
    }

    /**
     * @param  string $key
     * @param  string $value
     *
     * @return string
     */
    public function set($key, $value)
    {
        if (!is_array($value)) {
            $value = $this->plain($value);
        }

        $method = 'set'.$key;

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        return $this->metas[$key] = self::cut($value, $key);
    }

    /**
     * @param  string $key
     *
     * @return void
     */
    public function remove($key)
    {
        $method = 'remove'.$key;

        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            unset($this->metas[$key]);
        }
    }

    /**
     * @param  string $value
     *
     * @return string
     */
    protected function setTitle($value)
    {
        $title = $this->title;

        if ($title && $this->config['title_limit']) {
            $title = $this->config['separator'].$title;
            $limit = $this->config['title_limit'] - mb_strlen($title);
        } else {
            $limit = 'title';
        }

        return $this->metas['title'] = self::cut($value, $limit).$title;
    }

    /**
     * @param  string $value
     *
     * @return string
     */
    protected function setImage($value)
    {
        if (count($this->metas['image']) >= $this->config['image_limit']) {
            return '';
        }

        $this->metas['image'][] = $value;

        return $value;
    }

    /**
     * @return void
     */
    protected function removeImage()
    {
        $this->metas['image'] = [];
    }

    /**
     * @param  string $value
     *
     * @return string
     */
    protected function setProduct($value)
    {
        $this->metas['product'][] = $value;

        $this->set('type', 'og:product');

        return $value;
    }

    /**
     * @param  string       $key
     * @param  string|array $default = ''
     *
     * @return string|array
     */
    public function get($key, $default = '')
    {
        $method = 'get'.$key;

        if (method_exists($this, $method)) {
            return $this->$method($default);
        }

        if (empty($this->metas[$key])) {
            return $default;
        }

        return $this->metas[$key];
    }

    /**
     * @param  string|array $default
     *
     * @return array
     */
    public function getImage($default)
    {
        if ($default) {
            $default = is_array($default) ? $default : [$default];
        } else {
            $default = [];
        }

        return array_slice(array_merge($this->metas['image'], $default), 0, $this->config['image_limit']);
    }

    /**
     * @return array
     */
    public function getProduct()
    {
        return $this->metas['product'];
    }

    /**
     * @param  string       $key
     * @param  string|array $default = ''
     *
     * @return string
     */
    public function tag($key, $default = '')
    {
        if (!($values = $this->get($key, $default))) {
            return '';
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        $html = '';

        foreach ($this->config['tags'] as $tag) {
            $class = __NAMESPACE__.'\\Tags\\'.$tag;

            foreach (array_unique($values) as $value) {
                $html .= "\n".$class::tag($key, $value);
            }
        }

        return $html;
    }

    /**
     * @param  array $keys = []
     *
     * @return string
     */
    public function tags(array $keys = [])
    {
        $html = '';

        foreach (($keys ?: array_keys($this->metas)) as $key) {
            $html .= "\n".$this->tag($key);
        }

        return $html;
    }
}
