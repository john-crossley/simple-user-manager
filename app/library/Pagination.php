<?php

class Pagination {

  private $_perPage;

  private $_instance;

  private $_page;

  private $_limit;

  private $_totalRows;

  public function __construct($perPage, $instance)
  {
    $this->_instance = $instance;
    $this->_perPage = $perPage;
    $this->setInstance();
  }

  private function getStart()
  {
    return ($this->_page * $this->_perPage) - $this->_perPage;
  }

  private function setInstance()
  {
    $this->_page = (int)(!isset($_GET[$this->_instance]) ? 1 : $_GET[$this->_instance]);
    $this->_page = ($this->_page == 0 ? 1 : $this->_page);
  }

  public function setTotal($totalRows)
  {
    $this->_totalRows = (int)$totalRows;
  }

  public function getLimit()
  {
    return $this->getStart() . ', ' . $this->_perPage;
  }

  public function pageLinks($path = '?', $ext = null)
  {

    $adjacents = 2;
    $prev      = $this->_page - 1;
    $next      = $this->_page + 1;
    $lastpage  = ceil( $this->_totalRows / $this->_perPage );
    $lpm1      = $lastpage - 1;

    $pagination = "";
    if ( $lastpage > 1 ) {
      $pagination .= "<ul class='pagination pagination-small'>";
      if ( $this->_page > 1 )
        $pagination .= "<li><a href='" . $path . "$this->_instance=$prev" . "$ext'>Prev</a></li>";
      else
        $pagination .= "<li class='disabled'><a href='#'>Prev</a></li>";

      if ( $lastpage < 7 + ( $adjacents * 2 ) ) {
        for ( $i = 1; $i <= $lastpage; $i++ ) {
          if ( $i == $this->_page )
            $pagination .= "<li class='active'><a href='#'>$i</a></li>";
          else
            $pagination .= "<li><a href='" . $path . "$this->_instance=$i" . "$ext'>$i</a></li>";
        } //$i = 1; $i <= $lastpage; $i++
      } //$lastpage < 7 + ( $adjacents * 2 )
      elseif ( $lastpage > 5 + ( $adjacents * 2 ) ) {
        if ( $this->_page < 1 + ( $adjacents * 2 ) ) {
          for ( $i = 1; $i < 4 + ( $adjacents * 2 ); $i++ ) {
            if ( $i == $this->_page )
              $pagination .= " <li class='active'><a href=''>$i</a></li>";
            else
              $pagination .= "<li><a href='" . $path . "$this->_instance=$i" . "$ext'>$i</a></li>";
          } //$i = 1; $i < 4 + ( $adjacents * 2 ); $i++
          $pagination .= "<li class='active'><a href='#'>...</a></li>";
          $pagination .= "<li><a href='" . $path . "$this->_instance=$lpm1" . "$ext'>$lpm1</a></li>";
          $pagination .= "<li><a href='" . $path . "$this->_instance=$lastpage" . "$ext'>$lastpage</a></li>";
        } //$this->_page < 1 + ( $adjacents * 2 )
        elseif ( $lastpage - ( $adjacents * 2 ) > $this->_page && $this->_page > ( $adjacents * 2 ) ) {
          $pagination .= "<li><a href='" . $path . "$this->_instance=1" . "$ext'>1</a></li>";
          $pagination .= "<li><a href='" . $path . "$this->_instance=2" . "$ext'>2</a></li>";
          $pagination .= "<li class='active'><a href='#'>...</a></li>";
          for ( $i = $this->_page - $adjacents; $i <= $this->_page + $adjacents; $i++ ) {
            if ( $i == $this->_page )
              $pagination .= "<li class='active'><a href='#'>$i</a></li>";
            else
              $pagination .= "<li><a href='" . $path . "$this->_instance=$i" . "$ext'>$i</a></li>";
          } //$i = $this->_page - $adjacents; $i <= $this->_page + $adjacents; $i++
          $pagination .= "<li><a href='#'>..</a></li>";
          $pagination .= "<li><a href='" . $path . "$this->_instance=$lpm1" . "$ext'>$lpm1</a></li>";
          $pagination .= "<li><a href='" . $path . "$this->_instance=$lastpage" . "$ext'>$lastpage</a></li>";
        } //$lastpage - ( $adjacents * 2 ) > $this->_page && $this->_page > ( $adjacents * 2 )
        else {
          $pagination .= "<li><a href='" . $path . "$this->_instance=1" . "$ext'>1</a></li>";
          $pagination .= "<li><a href='" . $path . "$this->_instance=2" . "$ext'>2</a></li>";
          $pagination .= "<li class='active'><a href='#'>..</a></li>";
          for ( $i = $lastpage - ( 2 + ( $adjacents * 2 ) ); $i <= $lastpage; $i++ ) {
            if ( $i == $this->_page )
              $pagination .= "<li class='active'><a href='#'>$i</a></li>";
            else
              $pagination .= "<li><a href='" . $path . "$this->_instance=$i" . "$ext'>$i</a></li>";
          } //$i = $lastpage - ( 2 + ( $adjacents * 2 ) ); $i <= $lastpage; $i++
        }
      } //$lastpage > 5 + ( $adjacents * 2 )

      if ( $this->_page < $i - 1 )
        $pagination .= "<li><a href='" . $path . "$this->_instance=$next" . "$ext'>Next</a></li>";
      else
        $pagination .= "<li class='disabled'><a href='#'>Next</a></li>";

      $pagination .= "</ul>\n";
    } //$lastpage > 1


    return $pagination;
  }

}
