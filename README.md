# bigtree-gathercontent
Import data from a GatherContent project into BigTreeCMS.

## Usage
1. Download the zip.
2. Go to the admin panel in your BigTree project and install the downloaded extension.
3. Go to the Modules section and add a GatherContent setting. This setting consists of your GatherContent username and API key as well as the account name and project name you want to retrieve the data from.
4. Click the Import button in the Nav Bar of the GatherContent module.
5. Select the setting you want to use for connecting to your GatherContent account and click the Import Button.

## Current Limitations
As of version 0.1, an assumption is made that you have a tab labeled "SEO" in each of your GatherContent items in the project with fields labeled "Page Title" and "Meta Description". Until I add some kind of configurator, that logic is hardcoded.

Right now the importer will import everything under GatherContent's Home item and organize them into the same hierarchy in BigTree Pages. The following data is brought over for each page:

- Page Title
- Nav Title
- Meta Description

