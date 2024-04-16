<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

class ControllerExtensionModuleUniSettings extends Controller
{
    private $error = [];
    private $time = NULL;
    private $trial = NULL;
    public function index()
    {
        $this->load->model("extension/module/uni_settings");
        $this->load->model("tool/image");
        $this->load->model("localisation/language");
        $this->load->model("setting/store");
        $this->load->model("setting/setting");
        $this->load->model("setting/extension");
        $this->load->model("setting/module");
        $this->load->model("catalog/information");
        $this->load->model("catalog/category");
        $this->load->model("catalog/product");
        $this->load->language("extension/module/uni_settings");
        $this->document->setTitle(strip_tags($this->language->get("heading_title")));
        $data["heading_title"] = $this->language->get("heading_title");
        $this->document->addStyle("view/stylesheet/unishop.css");
        $this->document->addStyle("view/stylesheet/bootstrap-colorpicker.min.css");
        $this->document->addScript("view/javascript/bootstrap-colorpicker.min.js");
        $this->document->addScript("view/javascript/unishop.js");
        if ($this->config->get("config_editor_default")) {
            $this->document->addScript("view/javascript/ckeditor/ckeditor.js");
            $this->document->addScript("view/javascript/ckeditor/ckeditor_init.js");
        } else {
            $this->document->addStyle("view/javascript/summernote/summernote.css");
            $this->document->addScript("view/javascript/summernote/summernote.js");
            $this->document->addScript("view/javascript/summernote/summernote-image-attributes.js");
            $this->document->addScript("view/javascript/summernote/opencart.js");
        }
        $user_token = $this->session->data["user_token"];
        $data["ckeditor"] = $this->config->get("config_editor_default");
        $data["token"] = "user_token=" . $user_token;
        $data["error_warning"] = isset($this->error["warning"]) ? $this->error["warning"] : "";
        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $language) {
            $data["languages"][] = ["id" => $language["language_id"], "name" => $language["name"], "code" => $language["code"]];
        }
        $data["breadcrumbs"] = [];
        $data["breadcrumbs"][] = ["text" => $this->language->get("text_home"), "href" => $this->url->link("common/dashboard", "user_token=" . $user_token, true)];
        $data["breadcrumbs"][] = ["text" => $this->language->get("text_module"), "href" => $this->url->link("marketplace/extension", "user_token=" . $user_token . "&type=module", true)];
        $data["breadcrumbs"][] = ["text" => $this->language->get("heading_title"), "href" => $this->url->link("extension/module/uni_settings", "user_token=" . $user_token, true)];
        $data["cancel"] = $this->url->link("marketplace/extension", "user_token=" . $user_token . "&type=module", true);
        $data["catalog"] = $this->config->get("config_secure") ? HTTPS_CATALOG : HTTP_CATALOG;
        $data["placeholder"] = $this->model_tool_image->resize("no_image.png", 100, 100);
        $data["store_id"] = $store_id = isset($this->request->get["store_id"]) ? $this->request->get["store_id"] : 0;
        $store_info = $this->model_setting_setting->getSetting("config", $store_id);
        $data["telephone"] = $store_info ? $store_info["config_telephone"] : $this->config->get("config_telephone");
        $data["stores"][] = ["store_id" => 0, "name" => $this->config->get("config_name"), "href" => $this->url->link("extension/module/uni_settings", "user_token=" . $user_token . "&store_id=0", true)];
        $sort_string = isset($data["set"]["sort_stories"]) ? $data["set"]["sort_stories"] : "id,asc";
        $sort_1 = substr($sort_string, 0, 2) == "id" ? "store_id" : "url";
        $sort_2 = substr($sort_string, -3) == "asc" ? "ASC" : "DESC";
        $stores = $this->model_setting_store->getStores();
        if (1 < count($stores)) {
            foreach ($stores as $key => $value) {
                $sort[$key] = $value[$sort_1];
            }
            if ($sort_2 == "ASC") {
                array_multisort($sort, SORT_ASC, $stores);
            } else {
                array_multisort($sort, SORT_DESC, $stores);
            }
        }
        if ($stores) {
            foreach ($stores as $store) {
                $data["stores"][] = ["store_id" => $store["store_id"], "name" => html_entity_decode($store["name"], ENT_QUOTES, "UTF-8"), "href" => $this->url->link("extension/module/uni_settings", "user_token=" . $user_token . "&store_id=" . $store["store_id"], true)];
            }
        }
        $data["modules"] = [];
        $request_modules = ["latest", "special", "featured", "bestseller"];
        $modules = $this->model_setting_extension->getInstalled("module");
        foreach ($modules as $module) {
            $this->load->language("extension/module/" . $module);
            $modules = $this->model_setting_module->getModulesByCode($module);
            foreach ($modules as $module) {
                if (in_array($module["code"], $request_modules)) {
                    $data["modules"][] = ["name" => $this->language->get("heading_title"), "name2" => $module["name"]];
                }
            }
        }
        $settings = $this->model_extension_module_uni_settings->getSetting($store_id);
        $data["informations"] = [];
        $filter_data = ["sort" => "name", "order" => "ASC"];
        $infos = $this->model_catalog_information->getInformations($filter_data);
        if ($infos) {
            foreach ($infos as $info) {
                $seolink = $this->model_catalog_information->getInformationSeoUrls($info["information_id"]);
                $data["informations"]["articles"][] = ["id" => $info["information_id"], "name" => $info["title"], "link" => "index.php?route=information/information&information_id=" . $info["information_id"], "seolink" => isset($seolink[$store_id]) && $seolink[$store_id] != "" ? str_replace("\"", "'", json_encode($seolink[$store_id], true)) : ""];
            }
        }
        if ($this->config->get("uni_news")) {
            $this->load->model("extension/module/uni_news");
            $filter_data = ["filter_store_id" => $store_id, "sort" => "n.date_added", "order" => "DESC"];
            $news_arr = $this->model_extension_module_uni_news->getNews($filter_data);
            if ($news_arr) {
                foreach ($news_arr as $news) {
                    $seolink = $this->model_extension_module_uni_news->getNewsSeoUrls($news["news_id"]);
                    $data["informations"]["news"][] = ["id" => $news["news_id"], "name" => $news["name"], "link" => "index.php?route=information/news_story&news_id=" . $news["news_id"], "seolink" => isset($seolink[$store_id]) && $seolink[$store_id] != "" ? str_replace("\"", "'", json_encode($seolink[$store_id], true)) : ""];
                }
            }
        }
        $data["categories"] = [];
        $filter_data = ["sort" => "name", "order" => "ASC"];
        $categories = $this->model_catalog_category->getCategories($filter_data);
        if ($categories) {
            foreach ($categories as $category) {
                $data["categories"][$category["category_id"]] = ["category_id" => $category["category_id"], "name" => $category["name"]];
            }
        }
        $data["categories2"] = [];
        $categories2 = $this->model_extension_module_uni_settings->getCategories(0, $store_id);
        if ($categories2) {
            foreach ($categories2 as $category) {
                $data["categories2"][$category["category_id"]] = ["category_id" => $category["category_id"], "name" => $category["name"]];
            }
        }
        $data["featured_page_products"] = [];
        $fp_products = isset($settings["catalog"]["featured_page"]["products"]) ? $settings["catalog"]["featured_page"]["products"] : [];
        if ($fp_products) {
            $featured_page_products = $this->model_extension_module_uni_settings->getProducts($fp_products, $store_id);
            if ($featured_page_products) {
                foreach ($featured_page_products as $product) {
                    $data["featured_page_products"][$product["product_id"]] = ["product_id" => $product["product_id"], "name" => $product["name"]];
                }
            }
        }
        $data["key_empty"] = !$this->config->get("theme_unishop2_key") ? true : false;
        $check_key = $this->chK();
        $trial = $this->trial;
        $time = $this->time;
        $data["show_settings"] = $check_key;
        $data["is_trial"] = $trial;
        $data["trial_end"] = $trial && !$time ? true : false;
        $data["version_type"] = [];
        if ($check_key && $trial && $time) {
            $time = ceil(($time - strtotime("now")) / 3600 / 24);
            $data["version_type"] = ["type" => "trial", "text" => $time ? sprintf($this->language->get("text_remain"), $time) : ""];
        }
        if ($check_key && !$trial) {
            $data["version_type"] = ["type" => "full", "text" => $this->language->get("text_full_version")];
        }
        $data["sys_info"] = $this->checkSysInfo();
        $data["set"] = $settings;
        $data["header"] = $this->load->controller("common/header");
        $data["column_left"] = $this->load->controller("common/column_left");
        $data["footer"] = $this->load->controller("common/footer");
        $this->response->setOutput($this->load->view("extension/module/uni_settings", $data));
    }
    public function save()
    {
        $this->load->model("extension/module/uni_settings");
        $result = [];
        $store_id = isset($this->request->post["store_id"]) ? $this->request->post["store_id"] : 0;
        if ($this->request->server["REQUEST_METHOD"] == "POST" && isset($this->request->post["uni_set"])) {
            $result = $this->model_extension_module_uni_settings->setSetting($store_id, $this->request->post["uni_set"]);
        }
        $this->delUnused();
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($result));
    }
    public function saveSetting()
    {
        $this->save();
    }
    public function install()
    {
        $this->load->model("extension/module/uni_settings");
        $this->load->model("setting/setting");
        $this->model_extension_module_uni_settings->install();
        $this->model_setting_setting->editSetting("theme_unishop2_key", ["theme_unishop2_key" => ""]);
    }
    public function addTrial()
    {
        $json = [];
        $key = $this->rK();
        if ($key && $this->chK($key)) {
            $json["success"] = $this->setK($key);
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function addKey()
    {
        $json = [];
        if ($this->request->server["REQUEST_METHOD"] == "POST" && isset($this->request->post["key"])) {
            $key = trim(strip_tags($this->request->post["key"]));
            if ($key && $this->chK($key)) {
                $json["success"] = $this->setK($key);
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function addKey2()
    {
        $json = [];
        $key = $this->rK();
        if ($key && $this->chK($key) && !$this->trial) {
            $json["success"] = $this->setK($key);
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function getIconBlock()
    {
        $this->load->language("extension/module/uni_settings");
        $this->response->setOutput($this->load->view("extension/module/uni_icon_block", $data = []));
    }
    private function chK($key = "", $f = 1)
    {
        $result = "";
        $k = $key ? $key : $this->config->get("theme_unishop2_key");
        $k1 = $k ? json_decode($this->dK($this->dK(substr($k, 0, -2), $this->cK($k[-1])), $this->cK($k[-2])), true) : [];
        if ($k1 && count($k1) == 3 && $k1[1] == $this->host()) {
            $t = strtotime("now") < $k1[0] ? true : false;
            if (!$k1[2]) {
                $this->trial = true;
                if ($t) {
                    $this->time = $k1[0];
                }
            }
            $result = !$t ? $this->chK2() : true;
        } else {
            if ($f) {
                $result = $this->chK2();
            }
        }
        return $result;
    }
    private function chK2()
    {
        $key = $this->rK();
        return $key && $this->chK($key, 0) ? $this->setK($key) : "";
    }
    private function setK($key)
    {
        $this->load->model("setting/setting");
        $this->model_setting_setting->editSettingValue("theme_unishop2_key", "theme_unishop2_key", $key);
        return true;
    }
    private function rK($url = "", $f = 1)
    {
        $url = $url ? $url : "http://1getlic.tk/api/api.php?key=";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . urlencode($this->host()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);
        if ($result === false && $f) {
            $result = $this->rK("http://api.xn--2-otboff6cyb.xn--p1ai/api.php?key=", 0);
        }
        return $result !== false || $result != "" ? trim(urldecode($result)) : "";
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
    private function permission()
    {
        $this->load->language("extension/module/uni_settings");
        if ($this->user->hasPermission("modify", "extension/module/uni_settings")) {
            return true;
        }
        $this->error["warning"] = $this->language->get("error_permission");
        return false;
    }
    private function checkSysInfo()
    {
        $max_input_vars = 0;
        if ((int) ini_get("max_input_vars")) {
            $max_input_vars = (int) ini_get("max_input_vars");
        } else {
            if ((int) ini_get("suhosin.post.max_vars")) {
                $max_input_vars = (int) ini_get("suhosin.post.max_vars");
            }
        }
        $version = explode(".", PHP_VERSION);
        return ["input_vars" => $max_input_vars, "php_version" => $version];
    }
    private function delUnused()
    {
        $unlink_files = [DIR_SYSTEM . "unishop2_admin.ocmod.xml", DIR_SYSTEM . "unishop2.news.ocmod.xml", DIR_SYSTEM . "unishop2.ocmod.xml"];
        foreach ($unlink_files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
    private function cK($k)
    {
        return json_decode(base64_decode("WyJRNm55IiwiOHQ0VSIsIkdIMmkiLCI1SkwxIiwiRjMycCIsIlVJNmYiLCJUSDdxIiwiTzlMaSIsIjduSDMiLCJYQzluIl0="))[$k];
    }
}

?>