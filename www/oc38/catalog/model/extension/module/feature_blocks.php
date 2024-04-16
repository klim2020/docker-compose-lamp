<?php
class ModelExtensionModuleFeatureBlocks extends Model {

    public function __construct($registry)
    {

        parent::__construct($registry);
        $datediff = strtotime("+1 week")-strtotime('now');
        $this->cache = new Cache('file',$datediff);

    }

    public function getAllCategories($catid){

        $out = [$catid];
        //check if have childs
        $this->load->model('catalog/category');
        $cats = array_column($this->model_catalog_category->getCategories($catid),'category_id');
        //no
        if (count($cats)==0){
            return $out;
        }
        foreach ($cats as $cat){
            $tempcats = $this->getAllCategories($cat);
            $out = array_merge($out,$tempcats);
            $out = array_unique($out);
        }
        return $out;

    }

    public function getItemsByPrice($cat_id, $filter = array()){
        if (!isset($filter['sort'])){
            $filter['sort'] = 'asc';
        }
        if (isset($filter['date'])){
            $filter['date'] = 'AND p.date_added > '.$filter['date'];
        }else{
            $filter['date'] = '';
        }

        $cachr_info = ['cat-id',$cat_id,'price',$filter['sort']];
        $cachename = implode('-',$cachr_info);

        $ret = $this->cache->get($cachename);

        if ($ret == false){

            $cats = $this->getAllCategories($cat_id);
            $cats = implode(',',$cats);

            $str = "SELECT `p`.`product_id`, pd.`name`, p.`price`, pddis.price as discount, 
                case 
                    when `p`.quantity>0  then 0
                    else 1
                end
                as sort_field 
                                    FROM " . DB_PREFIX . "product_to_category ptc 
                                    LEFT JOIN " . DB_PREFIX . "product p ON ptc.product_id = p.product_id
                                    RIGHT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id AND pd.language_id =  ". (int)$this->config->get('config_language_id'). "
                                    LEFT JOIN " . DB_PREFIX . "product_special pddis ON pddis.product_id = p.product_id AND ((pddis.date_start = '0000-00-00' OR pddis.date_start < NOW()) AND (pddis.date_end = '0000-00-00' OR pddis.date_end > NOW())) AND pddis.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
                                    WHERE `category_id` in ({$cats}) and p.`price` > 0 
                                    GROUP BY p.`product_id`,p.`price`
                                    ORDER BY sort_field asc ,p.`price` {$filter['sort']}
                                    LIMIT 5";
            $ret = $this->db->query($str)->rows;

            $this->cache->set($cachename,$ret);
        }



        return $ret;
    }

    public function getItemsByDiscount($cat_id, $filter = array()){
        if (!isset($filter['sort'])){
            $filter['sort'] = 'asc';
        }

        $cachr_info = ['cat-id',$cat_id,'discount',$filter['sort']];
        $cachename = implode('-',$cachr_info);

        $ret = $this->cache->get($cachename);

        if ($ret == false){

            $cats = $this->getAllCategories($cat_id);
            $cats = implode(',',$cats);

            $str = "SELECT `p`.`product_id`, pd.`name`, p.`price`, pddis.price as discount FROM " . DB_PREFIX . "product_to_category ptc 
                                    LEFT JOIN " . DB_PREFIX . "product p ON ptc.product_id = p.product_id
                                    RIGHT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id AND pd.language_id =  ". (int)$this->config->get('config_language_id'). "
                                    RIGHT JOIN " . DB_PREFIX . "product_special pddis ON pddis.product_id = p.product_id AND ((pddis.date_start = '0000-00-00' OR pddis.date_start < NOW()) AND (pddis.date_end = '0000-00-00' OR pddis.date_end > NOW())) AND pddis.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
                                    WHERE `category_id` in ({$cats}) and p.`price` > 0 and p.`quantity` > 0
                                    GROUP BY p.`product_id` , pddis.`price` 
                                    ORDER BY pddis.`price` {$filter['sort']}
                                    LIMIT 5";
            $ret = $this->db->query($str)->rows;
            $this->cache->set($cachename,$ret);


        }


        return $ret;
    }

    public function getItemsThisYear($cat_id, $filter = array()){
        if (!isset($filter['sort'])){
            $filter['sort'] = 'desc';
        }
        if (isset($filter['date'])){
            $filter['date'] = 'AND p.date_added > '.$filter['date'];
        }else{
            $filter['date'] = '';
        }

        $cachr_info = ['cat-id',$cat_id,'year',$filter['sort']];
        $cachename = implode('-',$cachr_info);

        $ret = $this->cache->get($cachename);

        if ($ret == false){

            $cats = $this->getAllCategories($cat_id);
            $cats = implode(',',$cats);

            $str = "SELECT `p`.`product_id`, pd.`name`, p.`price`, pddis.price as discount, 
                case 
                    when `p`.quantity>0  then 0
                    else 1
                end
                as sort_field  FROM " . DB_PREFIX . "product_to_category ptc 
                                    LEFT JOIN " . DB_PREFIX . "product p ON ptc.product_id = p.product_id
                                    RIGHT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id AND pd.language_id =  ". (int)$this->config->get('config_language_id'). "
                                    LEFT JOIN " . DB_PREFIX . "product_special pddis ON pddis.product_id = p.product_id AND ((pddis.date_start = '0000-00-00' OR pddis.date_start < NOW()) AND (pddis.date_end = '0000-00-00' OR pddis.date_end > NOW())) AND pddis.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
                                    WHERE `category_id` in ({$cats}) AND YEAR(NOW()) = YEAR(p.`date_available`) and p.`price` > 0
                                    GROUP BY p.`product_id`,p.`price`
                                    ORDER BY sort_field asc, p.`price` {$filter['sort']}
                                    LIMIT 5";
            $ret = $this->db->query($str)->rows;
            $this->cache->set($cachename,$ret);
        }
        return $ret;
    }

    public function getItemsNew($cat_id, $filter = array()){
        if (!isset($filter['sort'])){
            $filter['sort'] = 'desc';
        }
        if (isset($filter['date'])){
            $filter['date'] = 'AND p.date_added > '.$filter['date'];
        }else{
            $filter['date'] = '';
        }

        $cachr_info = ['cat-id',$cat_id,'new',$filter['sort']];
        $cachename = implode('-',$cachr_info);

        $ret = $this->cache->get($cachename);

        if ($ret == false){

            $cats = $this->getAllCategories($cat_id);
            $cats = implode(',',$cats);

            $str = "SELECT `p`.`product_id`, pd.`name`, p.`price`, pddis.price as discount,
            case 
                    when `p`.quantity>0  then 0
                    else 1
                end
                as sort_field FROM " . DB_PREFIX . "product_to_category ptc 
                                    LEFT JOIN " . DB_PREFIX . "product p ON ptc.product_id = p.product_id
                                    RIGHT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id AND pd.language_id =  ". (int)$this->config->get('config_language_id'). "
                                    LEFT JOIN " . DB_PREFIX . "product_special pddis ON pddis.product_id = p.product_id AND ((pddis.date_start = '0000-00-00' OR pddis.date_start < NOW()) AND (pddis.date_end = '0000-00-00' OR pddis.date_end > NOW())) AND pddis.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
                                    WHERE `category_id` in ({$cats}) and p.`price` > 0 and p.`quantity` > 0
                                    GROUP BY p.`product_id`,p.`price`
                                    ORDER BY sort_field asc,  p.`price` {$filter['sort']}
                                    LIMIT 5";
            $ret = $this->db->query($str)->rows;
            $this->cache->set($cachename,$ret);
        }
        return $ret;
    }

    public function getRandomItems($cat_id,$filter = array()){
        if (!isset($filter['sort'])){
            $filter['sort'] = 'desc';
        }
        if (isset($filter['date'])){
            $filter['date'] = 'AND p.date_added > '.$filter['date'];
        }else{
            $filter['date'] = '';
        }

        $cachr_info = ['cat-id',$cat_id,'rand',$filter['sort']];
        $cachename = implode('-',$cachr_info);

        $ret = $this->cache->get($cachename);

        if ($ret == false){

            $cats = $this->getAllCategories($cat_id);
            $cats = implode(',',$cats);

            $str = "SELECT `p`.`product_id`, pd.`name`, p.`price`, pddis.price as discount,
            case 
                    when `p`.quantity>0  then 0
                    else 1
                end
                as sort_field FROM " . DB_PREFIX . "product_to_category ptc 
                                    LEFT JOIN " . DB_PREFIX . "product p ON ptc.product_id = p.product_id
                                    RIGHT JOIN " . DB_PREFIX . "product_description pd ON pd.product_id = p.product_id AND pd.language_id =  ". (int)$this->config->get('config_language_id'). "
                                    LEFT JOIN " . DB_PREFIX . "product_special pddis ON pddis.product_id = p.product_id AND ((pddis.date_start = '0000-00-00' OR pddis.date_start < NOW()) AND (pddis.date_end = '0000-00-00' OR pddis.date_end > NOW())) AND pddis.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'
                                    WHERE `category_id` in ({$cats}) and p.`price` > 0
                                    ORDER BY sort_field asc, RAND()
                                    LIMIT 7";

            $ret = $this->db->query($str)->rows;
            $this->cache->set($cachename,$ret);
        }


        return $ret;
    }
}

