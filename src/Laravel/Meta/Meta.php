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

        return $this->title = ' - '.$title;
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

        $method = 'get'.$key;

        if (method_exists($this, $method)) {
            return $this->$method();
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
        $value = preg_replace('#<[^>]+>#', ' ', $value);
        $value = preg_replace('/[\r\n\s]+/', ' ', $value);
        $value = trim(str_replace('"', '&quot;', $value));

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
        if ($this->title && $this->settings['limit_title']) {
            $limit = $this->settings['limit_title'] - strlen($this->title);
        } else {
            $limit = 'title';
        }

        return $this->metas['title'] = self::cut($value, $limit).$this->title;
    }

    /**
     * @param  string $value
     * @return array
     */
    private function setImage($value)
    {
        if (!isset($this->metas['image'])) {
            $this->metas['image'] = [];
        } elseif ($this->settings['image_limit'] > count($this->metas['image'])) {
            return;
        }

        return $this->metas['image'][] = asset($value);
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
     * @param  string $key
     * @return string
     */
    private static function cut($text, $key)
    {
        if (is_string($key) && isset($this->settings[$key.'_limit'])) {
            $limit = $this->settings[$key.'_limit'];
        } elseif (is_integer($key)) {
            $limit = $key;
        } else {
            return $text;
        }

        if (strlen($text) <= (int)$limit) {
            return $text;
        }

        $length = strlen($text);
        $num = 0;
        $tag = 0;

        for ($n = 0; $n < $length; $n++) {
            if ($text[$n] === '<') {
                $tag++;
                continue;
            }

            if ($text[$n] === '>') {
                $tag--;
                continue;
            }

            if ($tag !== 0) {
                continue;
            }

            $num++;

            if ($num < $limit) {
                continue;
            }

            $text = substr($text, 0, $n);

            if ($space = strrpos($text, ' ')) {
                $text = substr($text, 0, $space);
            }

            break;
        }

        if (strlen($text) === $length) {
            return $text;
        }

        $text .= $end;

        if (!preg_match_all('|(<([\w]+)[^>]*>)|', $text, $aBuffer) || empty($aBuffer[1])) {
            return $text;
        }

        preg_match_all('|</([a-zA-Z]+)>|', $text, $aBuffer2);

        if (count($aBuffer[2]) === count($aBuffer2[1])) {
            return $text;
        }

        foreach ($aBuffer[2] as $k => $tag) {
            if (empty($aBuffer2[1][$k]) || ($tag !== $aBuffer2[1][$k])) {
                $text .= '</'.$tag.'>';
            }
        }

        return $text;
    }
}