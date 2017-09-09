<?php

/*
* Techpin Telegram Bot Source Code
*
* @author  Tadeh Alexani
* @version 1.00, 2017-09-06
* PHP
*/

$apiLink = "https://api.telegram.org/bot446725932:AAFalcJySFHiT6vfcq14DHwHalEg0l5qF6M/";
$update = file_get_contents("php://input");
$updateArray = json_decode($update, TRUE);
$username = $updateArray["message"]["chat"]["username"];
$userID = $updateArray["message"]["chat"]["id"];
$chatID = $updateArray["message"]["chat"]["id"];
$messageText = $updateArray["message"]["text"];

$keyboard = array(
    ["ارزش بالای صد میلیون دلار"], ["ارزش بالای ده میلیون دلار"], ["جستجو"], ["تصادفی"]
);
$resp = array("keyboard" => $keyboard, "resize_keyboard" => true, "one_time_keyboard" => true);
$reply = json_encode($resp);

if ($messageText == "/start") {
    $welcomeText = "به ربات تلگرام تکپین خوش آمدید، با استفاده از منوی زیر شما می توانید به امکانات ربات دسترسی داشته باشید";
    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $welcomeText . "&reply_markup=" . $reply);
} else if ($messageText == "ارزش بالای صد میلیون دلار") {

    $string = file_get_contents("https://api.techpin.xyz/category/100m/products");
    $json = json_decode($string);
    $products = $json->products;
    foreach ($products as $product) {
        $name = $product->name_en;
        $logo = $product->details->logo;
        $slug = $product->slug;
        $startupLink = "<a href='techpin.ir/".$slug."'>".$name."</a>";
        $output .= $startupLink;
        $output .= "%0A%0A";
    };
    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $output . "&disable_web_page_preview=true&parse_mode=HTML&reply_markup=" . $reply);


} else if ($messageText == "ارزش بالای ده میلیون دلار") {
    $string = file_get_contents("https://api.techpin.xyz/category/10m/products");
    $json = json_decode($string);
    $products = $json->products;
    foreach ($products as $product) {
        $name = $product->name_en;
        $logo = $product->details->logo;
        $slug = $product->slug;
        $startupLink = "<a href='techpin.ir/".$slug."'>".$name."</a>";
        $output .= $startupLink;
        $output .= "%0A%0A";
    };
    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $output . "&disable_web_page_preview=true&parse_mode=HTML&reply_markup=" . $reply);

} else if ($messageText == "جستجو") {
    $search = "برای جستجو کلید واژه یا نام استارتاپ مورد نظر خود را وارد کنید:";
    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $search . "&reply_markup=" . $reply);


} else if ($messageText == "تصادفی") {
    $string = file_get_contents("https://api.techpin.xyz/random-product/");
    $json = json_decode($string);
    $product = $json->product;
    $name = $product->name_en;
    $NPS = $product->n_p_score;
    $employees = $product->details->employees;
    $launch = $product->details->year;
    $website = $product->website;
    $slug = $product->slug;
    $photo = $product->details->logo;
    $description = $product->details->description_en;
    $android = $product->details->android_app;
    $ios = $product->details->ios_app;

    if (!empty($name)) {
        $rep = "🔡 " . $name;
        $rep .= "%0A%0A";
    }
    if (!empty($description)) {
        $rep .= "▶️ " . $description;
        $rep .= "%0A%0A";
    }
    if (!empty($NPS)) {
        $rep .= "*️⃣ NPS: " . $NPS;
        $rep .= "%0A%0A";
    }
    if (!empty($employees)) {
        $rep .= "🚻 Employees: " . $employees;
        $rep .= "%0A%0A";
    }
    if (!empty($launch)) {
        $rep .= "🔢 Launch Year: " . $launch;
        $rep .= "%0A%0A";
    }
    if (!empty($website)){
        $rep .=  "⏺ http://" . $slug;
        $rep .= "%0A%0A";
    }
    
    $rep .= "ℹ️ techpin.ir/" . $slug;
    $rep .= "%0A%0A";
        


    if (!empty($android)) {
        $rep .= "♨ Android App: " . $android;
        $rep .= "%0A%0A";
    }
    if (!empty($ios)) {
        $rep .= " iOS App: " . $ios;
        $rep .= "%0A%0A";
    }

    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $rep . "&reply_markup=" . $reply);

} else {
    $string = file_get_contents("http://api.techpin.xyz/products/?search=" . $messageText);
    $json = json_decode($string);
    $productGroups = $json->products;

    $products = [];

    foreach ($productGroups as $group) {
        foreach ($group as $product) {
            array_push($products, $product);
        }
    }

    if (empty($products)) {
        file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . "دستور نامعتبری وارد کرده اید");
    } else {
        foreach ($products as $product) {
            $name = $product->name_en;
            $NPS = $product->n_p_score;
            $employees = $product->details->employees;
            $launch = $product->details->year;
            $website = $product->website;
            $slug = $product->slug;
            $photo = $product->details->logo;
            $description = $product->details->description_en;
            $android = $product->details->android_app;
            $ios = $product->details->ios_app;

            if (!empty($name)) {
                $rep = "🔡 " . $name;
                $rep .= "%0A%0A";
            }
            if (!empty($description)) {
                $rep .= "▶️ " . $description;
                $rep .= "%0A%0A";
            }
            if (!empty($NPS)) {
                $rep .= "*️⃣ NPS: " . $NPS;
                $rep .= "%0A%0A";
            }
            if (!empty($employees)) {
                $rep .= "🚻 Employees: " . $employees;
                $rep .= "%0A%0A";
            }
            if (!empty($launch)) {
                $rep .= "🔢 Launch Year: " . $launch;
                $rep .= "%0A%0A";
            }
            if (!empty($website)){
                $rep .=  "⏺ http://" . $slug;
                $rep .= "%0A%0A";
            }   
            
            $rep .= "ℹ️ techpin.ir/" . $slug;
            $rep .= "%0A%0A";

            if (!empty($android)) {
                $rep .= "♨ Android App: " . $android;
                $rep .= "%0A%0A";
            }
            if (!empty($ios)) {
                $rep .= " iOS App: " . $ios;
                $rep .= "%0A%0A";
            }

            file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $rep . "&reply_markup=" . $reply);
        };
    };
}
?>
