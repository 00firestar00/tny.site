<?php

function getTravisKey() {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.travis-ci.org/config");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($curl);
    curl_close($curl);
    $resp = json_decode($resp, true);
    return $resp["config"]["notifications"]["webhook"]["public_key"];
}

function postDiscord($payload) {
    $webhook_url = "PUT_YOUR_WEBHOOK_URL_HERE";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $webhook_url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    curl_setopt($curl, CURLOPT_POSTFIELDS, '{"username":"TravisCI","embeds":' . $payload . "}");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl);
    curl_close($curl);
}

function getSignatureHeader() {
    return base64_decode($_SERVER["HTTP_SIGNATURE"]);
}

function buildMessage($payload_arr) {
    $build_num = $payload_arr["number"];
    $build_url = $payload_arr["build_url"];

    $start = $payload_arr["started_at"];
    $finish = $payload_arr["finished_at"];
    $time = strtotime($finish) - strtotime($start);
    $min = (int)($time / 60);
    $seconds = (int)($time % 60);
    $str_time = "$min min $seconds sec";

    $commit = substr($payload_arr["commit"], 0, 10);
    $author = $payload_arr["committer_name"];
    $date = $payload_arr["committed_at"];
    $compare_url = $payload_arr["compare_url"];

    $repo_name = $payload_arr["repository"]["name"];
    $repo_owner = $payload_arr["repository"]["owner_name"];
    $branch = $payload_arr["branch"];

    $repo = "$repo_owner/$repo_name@$branch";

    $status_message = strtolower($payload_arr["status_message"]);
    $color = "";
    $status = "";
    if ($status_message == "passed") {
        $status = ":white_check_mark:";
        $color = 1164829; //#11c61d green-ish
    }
    else if (in_array($status_message, array("failed", "broken", "still failing"))) {
        $status = ":no_entry_sign:";
        $color = 12194333; //#ba121d red-ish
    }

    $embeds = array();
    $embeds["color"] = $color;
    $embeds["author"] = array(
        "name" => "View Changes on GitHub",
        "url" => $compare_url,
        "icon_url" => "https://assets-cdn.github.com/images/modules/logos_page/GitHub-Mark.png"
    );
    $embeds["title"] = "Build #$build_num $status";
    $embeds["url"] = $build_url;
    $embeds["description"] = "commit: `$commit` on `$repo`\nby `$author` $status_message in $str_time";
    $embeds["timestamp"] = $date;
    $embeds["footer"] = array();

    return json_encode(array($embeds));
}

if ($_POST["payload"]) {
    $payload = $_POST["payload"];

    $payload_arr = json_decode($payload, true);
    $signature = getSignatureHeader();

    $key = getTravisKey();

    if (openssl_verify($payload, $signature, $key) == 1) {
        file_put_contents("payload", $payload);
        postDiscord(buildMessage($payload_arr));
    }
}
