<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

class ModelExtensionModuleUniSettings extends Model
{
    public function getSetting($store_id)
    {
        $query = $this->db->query("show columns FROM `" . DB_PREFIX . "uni_setting` WHERE Field = 'store_id'");
        if (!$query->num_rows) {
            $this->update();
        }
        $query = $this->db->query("SELECT data FROM `" . DB_PREFIX . "uni_setting` WHERE store_id = " . (int) $store_id . "");
        return $query->rows ? json_decode($query->row["data"], true) : [];
    }
    public function setSetting($store_id, $data)
    {
        
            $this->db->query("DELETE FROM `" . DB_PREFIX . "uni_setting` WHERE store_id = '" . (int) $store_id . "'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "uni_setting` SET store_id = '" . (int) $store_id . "', data = '" . $this->db->escape(json_encode($data, true)) . "'");
            $this->cache->delete("unishop.settings");
            return "success";
        
        return "error";
    }
    public function deleteSetting()
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "uni_setting");
    }
    public function install()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "uni_setting (`store_id` int(11) NOT NULL, `data` mediumtext NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        $arr = ["upc", "ean", "jan", "isbn", "mpn"];
        foreach ($arr as $name) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` CHANGE `" . $name . "` `" . $name . "` varchar(255) COLLATE 'utf8_general_ci' NOT NULL");
        }
    }
    public function update()
    {
        $this->load->model("setting/store");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "uni_setting` ADD COLUMN `store_id` int(11) DEFAULT 99 FIRST");
        $results = $this->model_setting_store->getStores();
        $stores = [0];
        foreach ($results as $result) {
            $stores[] = $result["store_id"];
        }
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "uni_setting`");
        if ($query->rows) {
            $data = json_decode($query->row["data"], true);
            $new_data = "";
            foreach ($stores as $store_id) {
                $new_data = $this->db->escape(json_encode($data[(int) $store_id], true));
                $this->db->query("INSERT INTO `" . DB_PREFIX . "uni_setting` SET store_id = '" . (int) $store_id . "', data = '" . $new_data . "'");
            }
        }
    }
    public function getCategories($parent_id = 0, $store_id = 0)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.category_id = cd.category_id) LEFT JOIN `" . DB_PREFIX . "category_to_store` c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int) $parent_id . "' AND cd.language_id = '" . (int) $this->config->get("config_language_id") . "' AND c2s.store_id = '" . (int) $store_id . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");
        return $query->rows ? $query->rows : [];
    }
    public function getCategoriesNew($categories, $store_id = 0)
    {
        $lang_id = (int) $this->config->get("config_language_id");
        $result = [];
        if ($categories) {
            $sql = "SELECT c.category_id, cd.name FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.category_id = cd.category_id) LEFT JOIN `" . DB_PREFIX . "category_to_store` c2s ON (c.category_id = c2s.category_id)";
            $sql .= " WHERE c.category_id IN (" . implode(",", array_map("intval", $categories)) . ") AND cd.language_id = '" . $lang_id . "' AND c2s.store_id = '" . $store_id . "' AND c.status = '1' GROUP BY c.category_id ORDER BY LCASE(cd.name) ASC";
            $query = $this->db->query($sql);
            if ($query->rows) {
                $result = $query->rows;
            }
        }
        return $result;
    }
    public function getProducts($products, $store_id)
    {
        $lang_id = (int) $this->config->get("config_language_id");
        $result = [];
        if ($products) {
            $sql = "SELECT p.product_id, pd.name FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) LEFT JOIN `" . DB_PREFIX . "product_to_store` p2s ON (p.product_id = p2s.product_id)";
            $sql .= " WHERE p.product_id IN (" . implode(",", array_map("intval", $products)) . ") AND pd.language_id = '" . $lang_id . "' AND p2s.store_id = '" . $store_id . "' AND p.status = '1' GROUP BY p.product_id ORDER BY LCASE(pd.name) ASC";
            $query = $this->db->query($sql);
            if ($query->rows) {
                $result = $query->rows;
            }
        }
        return $result;
    }
    private function chK()
    {
        $k = $this->config->get("theme_unishop2_key");
        $k1 = $k ? json_decode($this->dK($this->dK(substr($k, 0, -2), $this->cK($k[-1])), $this->cK($k[-2])), true) : [];
        return isset($k1[0]) && strtotime("now") < $k1[0] && isset($k1[1]) && $k1[1] == $this->host() ? true : false;
    }
    private function host()
    {
        $host = explode("/", $this->config->get("config_secure") ? HTTPS_SERVER : HTTP_SERVER);
        return mb_substr($host[2], 0, 3) == "www" ? mb_substr($host[2], 4) : $host[2];
    }
    private function dK($t, $k)
    {
        $t = base64_decode($t);
        $r = "";
        while (strlen($r) < strlen($t)) {
            $r .= substr(md5(md5($k . $r)), 0, 10);
        }
        return $t ^ $r;
    }
    private function cK($k)
    {
        return json_decode(base64_decode("WyJRNm55IiwiOHQ0VSIsIkdIMmkiLCI1SkwxIiwiRjMycCIsIlVJNmYiLCJUSDdxIiwiTzlMaSIsIjduSDMiLCJYQzluIl0="))[$k];
    }
}

?>