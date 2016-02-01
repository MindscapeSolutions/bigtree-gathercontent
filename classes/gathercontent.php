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

            $homeId = -1;

            // gatherContentPageId => bigtreePageId
            $newStructure = array();

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
                $btPage["nav_title"] = trim(strip_tags($pageData->name));

                // get the seo info
                foreach ($contentData->config as $cCounter => $configData) {
                    if (strtolower($configData->label) == "seo") {
                        foreach ($configData->elements as $eCounter => $elementData) {
                            switch (strtolower($elementData->label)) {
                                case "meta description":
                                    $btPage["meta_description"] = trim(strip_tags($elementData->value));
                                    break;
                                case "page title":
                                    $btPage["title"] = trim(strip_tags($elementData->value));
                                    break;
                            }
                        }
                        break; 
                    }
                }

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

                // create bigtree page
                $newId = $btAdmin->createPage($btPage);

                // store both page ids in $newStructure
                if (!empty($newId)) {
                    $newStructure[$pageData->id] = $newId;
                }
            }
        }
	}
?>
