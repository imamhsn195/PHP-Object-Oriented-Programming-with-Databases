<?php
    class Pagination{
        public $current_page;
        public $per_page;
        public $total_count;

        public function __construct($current_page=1, $per_page=20, $total_count = 0){
            $this->current_page = (int) $current_page;
            $this->per_page = (int)  $per_page;
            $this->total_count = (int)  $total_count;
        }

        public function offset(){
            return $this->per_page * ($this->current_page - 1);
        }
         
        public function total_count_pages(){
            return ceil($this->total_count / $this->per_page);
        }

        public function prv_page(){
            $prv = $this->current_page - 1;
            return $prv > 0 ? $prv : false;
        }

        public function next_page(){
            $next = $this->current_page + 1;
            return $next <= $this->total_count_pages() ? $next : false;
        }

        public function next_page_link($url=""){
            $link = "";
            if($this->next_page() != false){ 
                $link .= "<a href=\"{$url}?page={$this->next_page()}\">Next &raquo;</a>";
            } 
            return $link; 
        }

        public function prv_page_link($url=""){
            $link = "";
            if($this->prv_page() != false){
                $link .= "<a href=\"{$url}?page={$this->prv_page()}\"> &laquo; Privous </a>";
            } 
            return $link;
        }

        public function number_links($url=""){
            $output = "";
            for($i = 1; $i <= $this->total_count_pages(); $i++){
                if($this->current_page == $i){
                    $output .= "<span class=\"selected\">{$i}</span>";
                }else{
                    $output .= "<a href=\"{$url}?page={$i}\">{$i}</a>";
                }
            }
            return $output;
        }
        public function pagination_links($url=""){
            $output = "";
            if($this->total_count > $this->per_page){ 
                $output .= "<div class=\"pagination\">"; 
                $output .=  $this->prv_page_link($url);
                $output .=  $this->number_links($url);
                $output .=  $this->next_page_link($url);
                $output .=  "</div>";
            }
            return $output;
        }
    }
?>