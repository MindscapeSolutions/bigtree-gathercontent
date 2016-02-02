<?
	class GatherContent extends BigTreeModule {
		var $Table = "gcontent";

        /**
         * Import data from GatherContent
         *
         * @param int $settingsId The gcontent record id
         * @param Object $data GatherContent data object
         *
         */
        public static function import($settingsId, $data) {
            $data = json_decode($data);
            $data = $data->data;

            $btAdmin = new BigTreeAdmin();

            // remove the existing site pages
            sqlquery("delete from bigtree_pages where id > 0");

            // get the settings
            $gContent = new GatherContent();
            $gContent = $gContent->get($settingsId);
            $dataMap = $gContent["data_map"];

            // gatherContentPageId => bigtreePageId
            $newStructure = array();

            $homeId = -1;
            foreach ($data as $dCounter => $pageData) {
                if ($pageData->parent_id == 0) {
                    $homeId = $pageData->id;
                    continue;
                }

                // get the page content data
                $c = curl_init();

                curl_setopt($c, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($c, CURLOPT_HTTPHEADER, array('Accept: application/vnd.gathercontent.v0.5+json'));
                curl_setopt($c, CURLOPT_USERPWD, $gContent["username"] . ":" . $gContent["apikey"]);
                curl_setopt($c, CURLOPT_URL, "https://api.gathercontent.com/items/" . $pageData->id);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

                $contentData = json_decode(curl_exec($c));
                $contentData = $contentData->data;
                curl_close($c);

                $btPage = array();
                $btPage["trunk"] = "";

                if ($pageData->parent_id == 0) {
                    $btPage["parent"] = 0;
                }
                else {
                    if (array_key_exists($pageData->parent_id, $newStructure)) {
                        $btPage["parent"] = $newStructure[$pageData->parent_id];
                    }
                    else {
                        $btPage["parent"] = 0;
                    }
                }

                $btPage["in_nav"] = "on";
                $btPage["meta_description"] = "";
                $btPage["meta_keywords"] = "";
                $btPage["seo_invisible"] = "";
                $btPage["template"] = "content";
                $btPage["external"] = "";
                $btPage["new_window"] = "";
                $btPage["resources"] = "";
                $btPage["archived"] = "";
                $btPage["archived_inherited"] = "";
                $btPage["max_age"] = 0;
                $btPage["last_edited_by"] = $_SESSION["bigtree_admin"]["id"];
                $btPage["ga_page_views"] = 0;
                $btPage["position"] = 0;


                foreach ($dataMap as $fieldCounter => $fieldData) {
                    switch (strtolower($fieldData["bigTreeField"])) {
                        case "metadescription":
                            $btPage["meta_description"] = trim(strip_tags(GatherContent::getFieldData($contentData->config, $fieldData["gatherContentTab"], $fieldData["gatherContentField"])));
                            break;
                        case "metakeywords":
                            $btPage["meta_keywords"] = trim(strip_tags(GatherContent::getFieldData($contentData->config, $fieldData["gatherContentTab"], $fieldData["gatherContentField"])));
                            break;
                        case "navigationtitle":
                            $btPage["nav_title"] = trim(strip_tags(GatherContent::getFieldData($contentData->config, $fieldData["gatherContentTab"], $fieldData["gatherContentField"])));
                            break;
                        case "pagetitle":
                            $btPage["title"] = trim(strip_tags(GatherContent::getFieldData($contentData->config, $fieldData["gatherContentTab"], $fieldData["gatherContentField"])));
                            break;
                        case "custom":
                            break;
                    }

                }

                if (empty($btPage["title"])) {
                    $btPage["title"] = trim(strip_tags($pageData->name));
                }

                if (empty($btPage["nav_title"])) {
                    $btPage["nav_title"] = trim(strip_tags($pageData->name));
                }

                // create bigtree page
                $newId = $btAdmin->createPage($btPage);

                // store both page ids in $newStructure
                if (!empty($newId)) {
                    $newStructure[$pageData->id] = $newId;
                }
            }
        }

        /**
         * Get the field data from a specified GatherContent tab and field
         * 
         * @param array $gcData GatherContent data
         * @param string $tabName The name of the tab in GatherContent that holds the field
         * @param string $fieldName The name of the field in GatherContent
         *
         * @return string The field's value
         */
        public static function getFieldData($gcData, $tabName, $fieldName) {

            // loop through the GatherContent tab names
            foreach ($gcData as $cCounter => $configData) {

                // if we found the tab
                if (strtolower($configData->label) == strtolower($tabName)) {

                    // loop through the fields on this tab
                    if (!empty($configData->elements)) {
                        foreach ($configData->elements as $eCounter => $elementData) {

                            // if we found the field
                            if (strtolower($elementData->label) == strtolower($fieldName)) {
                                return trim(strip_tags($elementData->value));
                            }
                        }
                    }

                    return "";
                }
            }
        }
	}
?>
