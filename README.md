# bigtree-gathercontent
Import data from a GatherContent project into BigTreeCMS.

## Usage
1. Download the zip.
2. Go to the admin panel in your BigTree project and install the downloaded extension.
3. Go to the Modules section and add a GatherContent setting. This setting consists of your GatherContent username and API key as well as the account name and project name you want to retrieve the data from.
4. Click the Import button in the Nav Bar of the GatherContent module.
5. Select the setting you want to use for connecting to your GatherContent account and click the Import Button.

## Release Notes
### 0.2
The biggest improvement in this release is the ability to map GatherContent fields to BigTree Page fields. For example, if your Navigation Title content is inside a field named "Nav Title" on a tab named "Page Content" in GatherContent, you can set that configuration in this extension's Data Map field.

Available BigTree Page Fields to import are now:
- Page Title
- Navigation Title
- Meta Description
- Meta Keywords

In addition, you can select a BigTree Page Resource to map GatherContent data to. For example, you may have text in GatherContent that you want mapped to the Page Content field on a BigTree page.

### 0.1
An assumption is made that you have a tab labeled "SEO" in each of your GatherContent items in the project with fields labeled "Page Title" and "Meta Description". Until I add some kind of configurator, that logic is hardcoded.

Right now the importer will import everything under GatherContent's Home item and organize them into the same hierarchy in BigTree Pages. The following data is brought over for each page:

- Page Title
- Nav Title
- Meta Description

