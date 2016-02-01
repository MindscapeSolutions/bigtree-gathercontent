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
    die("Account not found");
}

// get the projects
$c = curl_init();

curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($c, CURLOPT_HTTPHEADER, array('Accept: application/vnd.gathercontent.v0.5+json'));
curl_setopt($c, CURLOPT_USERPWD, $gContent["username"] . ":" . $gContent["apikey"]);
curl_setopt($c, CURLOPT_URL, "https://api.gathercontent.com/projects?account_id=" . $accountId);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

$response = json_decode(curl_exec($c));
curl_close($c);

// find the project we are looking for
$projectId = -1;
foreach ($response->data as $projectCounter => $projectInfo) {
    if (strtolower($projectInfo->name) == strtolower($gContent["project"])) {
        $projectId = $projectInfo->id;
        break;
    }
}

if ($projectId == -1) {
    die('Project not found');
}

// get the project items
$c = curl_init();

curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($c, CURLOPT_HTTPHEADER, array('Accept: application/vnd.gathercontent.v0.5+json'));
curl_setopt($c, CURLOPT_USERPWD, $gContent["username"] . ":" . $gContent["apikey"]);
curl_setopt($c, CURLOPT_URL, "https://api.gathercontent.com/items?project_id=" . $projectId);
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

$response = json_decode(curl_exec($c));
curl_close($c);

GatherContent::import($bigtree["commands"][0], $response->data);
?>
