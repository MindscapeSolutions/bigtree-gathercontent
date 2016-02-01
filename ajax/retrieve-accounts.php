<?
// get the settings
$gContent = new GatherContent();
$gContent = $gContent->get($bigtree["commands"][0]);

// get the accounts
$c = curl_init();

curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($c, CURLOPT_HTTPHEADER, array('Accept: application/vnd.gathercontent.v0.5+json'));
curl_setopt($c, CURLOPT_USERPWD, $gContent["username"] . ":" . $gContent["apikey"]);
curl_setopt($c, CURLOPT_URL, "https://api.gathercontent.com/accounts");
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

$response = json_decode(curl_exec($c));
curl_close($c);

// find the account we are looking for
$accountId = -1;
foreach ($response->data as $rCounter => $accountData) {
    if (strtolower($accountData->name) == strtolower($gContent["account"])) {
        $accountId = $accountData->id;
        break;
    }
}

if ($accountId == -1) {
    echo "not found";
}
else {
    echo $accountId;
}
?>
