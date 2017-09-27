<?php
/*
* Techpin Telegram Bot Source Code
*
* @author  Tadeh Alexani
* @version 1.00, 2017-09-06
* PHP
*/

// Check connection to DB (Optional)
$connectDB = mysqli_connect("localhost", "YOUR_USERNAME", "YOUR_PASSWORD", "YOUR_DB");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
mysqli_set_charset($connectDB, "utf8");


//Define variables
$apiLink = "https://api.telegram.org/bot[YOUR_API_KEY]/";
$update = file_get_contents("php://input");
$updateArray = json_decode($update, TRUE);
$username = $updateArray["message"]["chat"]["username"];
$userID = $updateArray["message"]["chat"]["id"];
$chatID = $updateArray["message"]["chat"]["id"];
$messageText = $updateArray["message"]["text"];

//Add Users to DB to keep track of users (Optional)
$select_user_log = "SELECT * from USERS_TABLE_NAME where YOUR_USERNAMES_COLUMN_NAME = '$username' ";
$run_user_log = mysqli_query($connectDB, $select_user_log);
if (mysqli_num_rows($run_user_log) == 1) {

} else {
    $insert_log = "insert into USERS_TABLE_NAME (YOUR_USERNAMES_COLUMN_NAME) VALUE ('$username')";
    $run_insert = mysqli_query($connectDB, $insert_log);
}


//Add Keyboard
$keyboard = array(
    ["ارزش بالای صد میلیون دلار"], ["ارزش بالای ده میلیون دلار"], ["جستجو"], ["تصادفی"]
);
$resp = array("keyboard" => $keyboard, "resize_keyboard" => true, "one_time_keyboard" => true);
$reply = json_encode($resp);

//Respond to user's message using if-else statements
if ($messageText == "/start") {
    $welcomeText = "به ربات تلگرام تکپین خوش آمدید، با استفاده از منوی زیر شما می توانید به امکانات ربات دسترسی داشته باشید";
    //Send welcome message to the user
    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $welcomeText . "&reply_markup=" . $reply);
} else if ($messageText == "ارزش بالای صد میلیون دلار") {
    //Get JSON data from Techpin's API and then parse it to strings
    $string = file_get_contents("https://api.techpin.xyz/category/100m/products");
    $json = json_decode($string);
    $products = $json->products;
    foreach ($products as $product) {
        $name = $product->name_en;
        $logo = $product->details->logo;
        $slug = $product->slug;
        $startupLink = "<a href='techpin.ir/" . $slug . "'>" . $name . "</a>";
        $output .= $startupLink;
        $output .= "%0A%0A";
    };
    //Send message to the user
    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $output . "&disable_web_page_preview=true&parse_mode=HTML&reply_markup=" . $reply);
} else if ($messageText == "ارزش بالای ده میلیون دلار") {
    //Get JSON data from Techpin's API and then parse it to strings
    $string = file_get_contents("https://api.techpin.xyz/category/10m/products");
    $json = json_decode($string);
    $products = $json->products;
    foreach ($products as $product) {
        $name = $product->name_en;
        $logo = $product->details->logo;
        $slug = $product->slug;
        $startupLink = "<a href='techpin.ir/" . $slug . "'>" . $name . "</a>";
        $output .= $startupLink;
        $output .= "%0A%0A";
    };
    //Send message to the user
    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $output . "&disable_web_page_preview=true&parse_mode=HTML&reply_markup=" . $reply);
} else if ($messageText == "جستجو") {
    $search = "برای جستجو کلید واژه یا نام استارتاپ مورد نظر خود را به انگلیسی وارد کنید:";
    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $search . "&reply_markup=" . $reply);
} else if ($messageText == "تصادفی") {
    //Get a random result using Techpin's API
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
    if (!empty($website)) {
        $rep .= "⏺ http://" . $slug;
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
    //Send a random result with details to the user
    file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $rep . "&reply_markup=" . $reply);
} else {
    //Search through Techpin startups and get the results
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
        //If startups do not found send this message:
        file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . "استارتاپ مورد نظر شما یافت نشد");
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
            if (!empty($website)) {
                $rep .= "⏺ http://" . $slug;
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
            //Send the search results
            file_get_contents($apiLink . "sendmessage?chat_id=$chatID&text=" . $rep . "&reply_markup=" . $reply);
        };
    };
}
?>
