<?php
/**
 * -----------------------------------
 * File  : Pagination.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */


namespace ArPHP\Pagination;

class Pagination
{
    private $count = false;
    private $limit = false;
    private $page = false;
    private $page_limit = 5;
    private $page_url = false;
    private $pages = false;
    private $offset = false;
    private $class_name = false;
    private $next;
    private $back;
    private $last;
    /**
     * link title
     * @var string
     */
    protected $title;
    /**
     * setting prep
     */
    protected $ceil;
    protected $start;
    protected $end;
    protected $json = [];
    /**
     * Pagination constructor.
     * @param $count
     * @param $limit
     * @param $page
     * @param string $page_url
     * @param int $page_limit
     * @param string $class_name
     */
    public function __construct($count, $limit, $page, $page_url = '', $page_limit = 5, $class_name = 'page_name')
    {
        $this->count = (int)$count;
        $this->limit = (int)$limit;
        $this->page = (int)$page;
        $this->page_url = $page_url;
        $this->class_name = $class_name;
        $this->page_limit = (int)$page_limit;
        $this->setSetting();
    }
    /**
     * set linke title
     * @param $title
     * @return $this
     */
    public function setTitle($title){
        $this->title = $title;
        return $this;
    }
    /**
     * create new object
     * @param $count
     * @param $limit
     * @param $page
     * @param string $page_url
     * @param int $page_limit
     * @param string $class_name
     * @return static
     */
    public static function create($count, $limit, $page, $page_url = '', $page_limit = 5, $class_name = 'page_name')
    {
        return new static($count, $limit, $page, $page_url, $page_limit, $class_name);
    }
    /**
     * @param $url
     * @return $this
     */
    public function setPath($url){
        $this->page_url = $url;
        return $this;
    }

    /**
     * @param $class_name
     * @return $this
     */
    public function setClassName($class_name)
    {
        $this->class_name = $class_name;
        return $this;
    }
    
    public function next($next){
        $this->next = $next;
        return $this;
    }
    public function back($back){
        $this->back = $back;
        return $this;
    }
    public function last($last){
        $this->last = $last;
        return $this;
    }
    public function getDefault(){
        $this->next("&raquo;");
        $this->back("&laquo;");
        $this->last("&raquo;");
        return $this;
    }

    /**
     *
     */
    protected function setSetting(){
        if($this->limit === false || $this->limit === 0) return;
        $this->page = ((int)$this->page == false ? 1 : (int)$this->page);
        $this->offset = (($this->page - 1) * $this->limit);
        $ceil = ceil($this->count / $this->limit);
        if($this->page > $ceil){
            $this->offset = $ceil;
            $this->page = $ceil;
        }
        $start = 1;
        $end = $ceil;
        if (is_numeric($this->page_limit)) {
            $end = ($ceil > $this->page_limit) ? $this->page_limit : $ceil;
            if ($this->page >= $this->page_limit && $this->page < $ceil) {
                $start = ($this->page - 2);
                $end = (($start + $this->page_limit) - 1);
            } else if ($this->page == $ceil && $ceil > $this->page_limit) {
                $start = ($this->page - $this->page_limit) + 1;
                $end = $this->page;
            }
            if ($end > $ceil) {
                $end = $ceil;
            }
        }
        $this->ceil = $ceil;
        $this->start = $start;
        $this->end = $end;
    }
//------------------------------------------------------------------------
    protected function run()
    {
        if($this->limit === false || $this->limit === 0) return;
        $this->pages = '<ul class="Pagination">';
        if ($this->page > 1 && $this->ceil > $this->page) {
            $this->pages .= '<li><a href="' . $this->changePageUrl(($this->page + 1)) . '" class="page_next ' . $this->class_name . '  " title="'.$this->title.' '.($this->page + 1).'" alt="'.$this->title.' '.($this->page + 1).'">'.(!empty($this->next)?$this->next:'&nbsp;').'</a></li>';
            $this->json[] = [
                'text' => 'next',
                'href' => $this->changePageUrl(($this->page + 1))
            ];
        }
        if ($this->page > 1 && $this->page > ($this->page_limit - 1) && $this->ceil >= $this->page) {
            $this->pages .= '<li><a href="' . $this->changePageUrl(1) . '"  class="' . $this->class_name . '"  title="'.$this->title.' 1" alt="'.$this->title.' 1">1</a> </li>';
            $this->json[] = [
                'text' =>  1,
                'href' => $this->changePageUrl( 1)
            ];
        }
        for ($i = $this->start; $i <= $this->end; $i++) {
            if ($this->page == $i) {
                $this->pages .= '<li class="active"><span  class="page_active">' . $i . '</span> </li>';
                $this->json[] = [
                    'text' =>  $i,
                    'href' => null
                ];
            } else {
                $this->json[] = [
                    'text' =>  $i,
                    'href' => $this->changePageUrl( $i)
                ];
                $this->pages .= '<li><a href="' . $this->changePageUrl($i) . '"  class="' . $this->class_name . '"  title="'.$this->title.' '.$i.'" alt="'.$this->title.' '.$i.'">' . $i . '</a>  </li>';
            }
        }
        if ($this->page > 1) {
            $this->pages .= '<li><a href="' . $this->changePageUrl(($this->page - 1)) . '"  class="' . $this->class_name . ' page_back"  title="'.$this->title.' '.($this->page - 1).'" alt="'.$this->title.' '.($this->page - 1).'">'.(!empty($this->back)?$this->back:'&nbsp;').'</a></li>';
            $this->json[] = [
                'text' =>  'back',
                'href' => $this->changePageUrl( ($this->page - 1))
            ];
        }
        if ($this->ceil > $this->end) {
            $this->json[] = [
                'text' =>  'last',
                'href' => $this->changePageUrl( $this->ceil)
            ];
            $this->pages .= '<li><a href="' . $this->changePageUrl($this->ceil) . '"  class="page_last ' . $this->class_name . ' "  title="'.$this->title.' '.($this->ceil).'" alt="'.$this->title.' '.($this->ceil).'">'.(!empty($this->last)?$this->last:'&nbsp;').'</a></li>';
        }
        $this->pages .= '</ul>';
    }

    /**
     * change page url
     * @param $number
     * @return string
     */
    protected function changePageUrl($number){
        if(is_callable($this->page_url)){
            return call_user_func($this->page_url,$number);
        }
        return ($this->page_url.$number);
    }
//------------------------------------------------------------------------
    public function getPages()
    {
        $this->run();
        return $this->pages;
    }

    public final function getArray(){
        $this->run();
        return $this->json;
    }
//------------------------------------------------------------------------
    public function getOffset()
    {
        if($this->limit === false || $this->limit === 0) return false;
        if($this->count == $this->ceil && $this->limit == $this->count){
            return 0;
        }
        return $this->offset;
    }
//------------------------------------------------------------------------
}
