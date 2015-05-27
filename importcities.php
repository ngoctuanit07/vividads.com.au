<?php
function getText() {
    $RandomArray = array();
    $rKey = rand(0, 8);
    $title_h1 = array(
        "{KEYWORD} Outdoor Banner Products",
        "{KEYWORD} Outdoor Signage Supply",
        "{KEYWORD} Outdoor Display Stands",
        "{KEYWORD} Outdoor Signage Supplier",
        "{KEYWORD} Outdoor Banner Signage",
        "{KEYWORD} Outdoor Designs",
        "{KEYWORD} Outdoor Sign Makers",
        "{KEYWORD} Vinyl Outdoor Signage",
        "{KEYWORD} New Outdoor Sales",
    );

    $title_h2 = array(
        "{KEYWORD} Outdoor Banner Products",
        "{KEYWORD} Banner Sign Prints in {KEYWORD} Everyday",
        "{KEYWORD} Outdoor Banner Sign Industry Supplier Aus Wide",
        "{KEYWORD}, Outdoor Banner Display Supplier Aus Wide",
        "{KEYWORD} Outdoor Banner Tasmania Signage Printing Aus Wide",
        "{KEYWORD} Outdoor Banner Display Sign Maker Design",
        "{KEYWORD} Outdoor Banner Advertising Displays Sign",
        "More {KEYWORD} Outdoor Banner Signage Digitally Printed Signs Vinyl",
        "{KEYWORD} Outdoor Banner Skins with Keder",
    );

    $content = array(
        "<strong>{KEYWORD} Signs Banners and Designs</strong><br />Outside and Inside Advertising Tips for business",
        "<strong>{KEYWORD} Signs Banner and Designs.</strong><br />Street Advertising Road Signs and more graphic business design ideas for Queensland and other regions. See more.",
        "<strong>Signwriting Examples {KEYWORD} Sign Supplies</strong><br />Queanbeyan and {KEYWORD} listing of sign supply. Get advertising prices and read more or visit {KEYWORD}",
        "<strong>Signwriting Examples Northern Territory Sign Supplies</strong><br />We love doing this kind of regional work, take a look at these examples",
        "<strong>Apple Isle Designs.</strong><br />Banners and Signmaker professional signs supplied Australia wide daily.",
        "<strong>From {KEYWORD} and Country and Nationally supply of Sign Work</strong><br />The images show the resulting job and the place where shipping was.",
        "<strong>West Designs.</strong><br />Supply and Discount production like these below are easy to supply, here are the latest design and banner examples.",
        "<strong>{KEYWORD} City and Regional Sign Supplies</strong><br />Supply and Discount production for regional and city companies, full range including recently added below.",
        "<strong>{KEYWORD} City and Regional Sign Supplies</strong><br />We love doing this kind of regional work, take a look at these examples",
    );
    $bottom_content = array(
        "Call us to chat about your vinyl or retractable display today!<br />Toll Free<br />1800 645 115",
        "Call us to chat about your vinyl or retractable display today!<br />Toll Free<br />1800 645 115",
        "Call us to chat about your vinyl or retractable display today!<br />Toll Free<br />1800 645 115",
        "Call us to chat about your vinyl or retractable display today!<br />Toll Free<br />1800 645 115",
        "Call us to chat about your vinyl or retractable display today!<br />Toll Free<br />1800 645 115",
        "Call us to chat about your vinyl or retractable display today!<br />Toll Free<br />1800 645 115",
        "Call us to chat about your vinyl or retractable display today!<br />Toll Free<br />1800 645 115",
        "Call us to chat about your vinyl or retractable display today!<br />Toll Free<br />1800 645 115",
        "Call us to chat about your vinyl or retractable display today!<br />Toll Free<br />1800 645 115",
    );
    $page_title = array(
        "{KEYWORD} Outdoor Banner Products",
        "{KEYWORD} Outdoor Signage Supply",
        "{KEYWORD} Outdoor Display Stands",
        "{KEYWORD} Outdoor Signage Supplier",
        "{KEYWORD} Outdoor Banner Signage",
        "{KEYWORD} Outdoor Designs",
        "{KEYWORD} Outdoor Sign Makers",
        "{KEYWORD} Vinyl Outdoor Signage",
        "{KEYWORD} New Outdoor Sales",
    );
    $meta_keyword = array(
        "Outdoor, banner, {KEYWORD} Outdoor Banner, {KEYWORD}, outdoor banner, signage, signs, sign, vinyl, ropes, eyelets, display, retractable, pull up, standing, aframes a frame, sandwich boards",
        "Outdoor, banner, outdoor banner, {KEYWORD} outdoor banner, {KEYWORD}, signage, signs, sign, vinyl, ropes, eyelets, sleeves, display, retractable, pull up, aframes a frame, sandwich boards",
        "Outdoor, outdoor banner, {KEYWORD} outdoor banner, {KEYWORD}, act, banner, signage, signs, sign, vinyl, ropes, eyelets, sleeves, display, retractable, pull up, aframes a frame, frames, sandwich boards",
        "Outdoor, {KEYWORD} outdoor banner, outdoor banner, darwin, banner, signage, signs, sign, vinyl, ropes, eyelets, sleeves, display, retractable, pull up, standout, aframes a frame, sandwich boards",
        "Outdoor, outdoor banner, hobart, hobart outdoor banner, banner, signage, signs, sign, vinyl, ropes, eyelets, sleeves, display, retractable, pull up, aframes, a frame, sandwich boards",
        "Outdoor, outdoor banner,{KEYWORD}, {KEYWORD} outdoor banner, banner, signage, signs, sign, vinyl, ropes, eyelets, sleeves, display, retractable, pull up, aframes a frame, sandwich boards",
        "Outdoor, {KEYWORD}, {KEYWORD} outdoor banner, outdoor banner, banner, signage, signs, sign, vinyl, ropes, eyelets, sleeves, display, retractable, pull up, aframes a frame, sandwich boards",
        "Outdoor, outdoor banner, {KEYWORD}, {KEYWORD} outdoor banner, banner, signage, signs, sign, vinyl, ropes, eyelets, sleeves, display, retractable, pull up, aframes a frame, sandwich boards",
        "{KEYWORD}, Outdoor, {KEYWORD} outdoor banners, banner, signage, signs, sign, vinyl, ropes, eyelets, display, retractabe, pull up,  aframes a frame, sandwich boards",
    );
    $meta_description = array(
        "{KEYWORD} Outdoor Banner  Signs can produce your banners quickly and from the highest quality materials available. Give us a call to discuss your signage and to obtain a free quote today.",
        "Indoor and Outdoor signage banners made from the best quality materials available today. Call us today for a free quote for all your signage needs.",
        "{KEYWORD} Outdoor Banner Supplier. Give us a call for a free banner quote today. We can print any size banner you may require for your next trade show or promotional event",
        "{KEYWORD} Outdoor Banner signs delivered to {KEYWORD} NT and anywhere in Australia. Give us a call for a free banner quote today.",
        "{KEYWORD} Outdoor Banner signage banners made from the best quality materials available today. Call us today for a free quote for all your signage needs.",
        "We can produce {KEYWORD} outdoor banners finished with ropes and eyelets or finished with sleeves to suit a rod. Call us today for a free quote for all your signage needs.",
        "{KEYWORD} Outdoor Banner company with over 20 years experience in the signage field we can help you out with any signage question. Call us today for a free quote for all your signage needs.",
        "{KEYWORD} Outdoor Banners signage made from the best quality materials available today. Call us on our toll free phone number today for a free quote for all your banner sign needs.",
        "{KEYWORD} Outdoor Banner skin supplier ready to take your phone call. Wherever you are we can make a banner for you fast, cheap and from the best quality materials!",
    );
    return $RandomArray = array("title_h1" => $title_h1[$rKey], "title_h2" => $title_h2[$rKey], "content" => $content[$rKey], "bottom_content" => $bottom_content[$rKey], "page_title" => $page_title[$rKey], "meta_keyword" => $meta_keyword[$rKey], "meta_description" => $meta_description[$rKey]);
}

function parse($file) {
    $cities = array();
    if (($handle = fopen($file, "r")) === FALSE)
        return;
    $i = 0;
    while (!feof($handle)) {
        $line_of_text = fgetcsv($handle, 1024);
        $CategorySef = preg_replace(array('/[^a-z0-9-]/i', '/[ ]{2,}/', '/[ ]/'), array(' ', ' ', '-'), strtolower($line_of_text[0]));
        $cities[$i]['title'] = uc_words($line_of_text[0]);
        $cities[$i]['url'] = $CategorySef;
        $i++;
    }
    return $cities;
    fclose($handle);
}

require_once 'app/Mage.php';
//umask(0);
Mage::app();
//default keyworkd settings
$RandomArray        = getText();

$title_h1           = $RandomArray['title_h1'];
$title_h2           = $RandomArray['title_h2'];
$page_title         = $RandomArray['page_title'];
$url                = "";
$filename           = "";
$imagefilename      = "";
$content            = $RandomArray['content'];
$bottom_content     = $RandomArray['bottom_content'];
$meta_keyword       = $RandomArray['meta_keyword'];
$meta_description   = $RandomArray['meta_description'];
$status             = "1";
$created_time       = "";
$updated_time       = "";

$write          = Mage::getSingleton('core/resource')->getConnection('core_write');
$store_id       = Mage::app()->getStore()->getStoreId();
$model          = Mage::getModel('gallery/category');
$rwObj          = new Sag_Gallery_Helper_Data();
$read           = Mage::getSingleton('core/resource')->getConnection('core_read');
$citiesFilePath = Mage::getBaseUrl('media') . "cites.csv";
$cities         = parse($citiesFilePath);
$recordCount    = 0;
$data           = array();
//Current Resources:
//$core_resources = $read->fetchAll("select * from core_resource where code='gallery_setup'");
//echo "<pre>Core Resources:<br />";
//print_r($core_resources);
//echo "</pre>";

if (count($cities) > 0) {
    $read->query("truncate table category");
    foreach ($cities as $city) {
        $data = array(
            'title'             => $city['title'],
            'title_h1'          => str_replace("{KEYWORD}", $city['title'], $title_h1),
            'title_h2'          => str_replace("{KEYWORD}", $city['title'], $title_h2),
            'page_title'        => str_replace("{KEYWORD}", $city['title'], $page_title),
            'url'               => $city['url'],
            'filename'          => "NULL",
            'imagefilename'     => "NULL",
            'content'           => str_replace("{KEYWORD}", $city['title'], $content),
            'bottom_content'    => str_replace("{KEYWORD}", $city['title'], $bottom_content),
            'meta_keyword'      => str_replace("{KEYWORD}", $city['title'], $meta_keyword),
            'meta_description'  => str_replace("{KEYWORD}", $city['title'], $meta_description),
            'status'            => $status,
            'created_time'      => "",
            'updated_time'      => ""
        );
        //echo "<pre>";
        //print_r($data);
        //echo "</pre>";
        //exit;
        $model->setData($data);
        $model->setCreatedTime(now())->setUpdateTime(now());
        if ($model->save()) {
            $recordCount++;
            //rewrite url for category.
            $cat_id = $model->getId();
            $id_path = "gallery_category_" . $cat_id;
            //category-name.html
            $urlKey = preg_replace(array('/[^a-z0-9-]/i', '/[ ]{2,}/', '/[ ]/'), array(' ', ' ', '-'), strtolower($data['title']));
            $request_path = $urlKey . ".html";
            
            //echo "select * from core_url_rewrite where id_path = $id_path";
            $result = $read->fetchAll("select * from core_url_rewrite where id_path = '".$id_path."' OR request_path = '".$request_path."'");            
            if(count($result)>0){
                $where = "id_path = '{$id_path}' OR request_path = '{$request_path}'";
                $write->delete("core_url_rewrite", $where);
            }
            
            //gallery/category/index/cat/id
            $target_path = "gallery/category/index/cat/" . $cat_id;
            $rwObj->CustomReriteUrl($id_path, $request_path, $target_path, $store_id);
        }
    }
}
echo "Total $recordCount record imported successfully.";