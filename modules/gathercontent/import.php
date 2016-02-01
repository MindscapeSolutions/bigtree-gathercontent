<?
$gSettings = new GatherContent();
$gSettings = $gSettings->getAll();
?>

<form id="importForm">

    <fieldset>
        <label>Select the GatherContent settings you would like to use for the import.<br />WARNING! Importing will delete all of your current pages.</label>
        <select id="project">
<?
foreach ($gSettings as $gCounter => $settings) {
?>
            <option value="<?= $settings["id"] ?>"><?= $settings["account"] . ": " . $settings["project"] ?></option>
<?
}
?>
        </select>
    </fieldset>

    <fieldset>
        <input id="submitImport" type="button" value="Import" />
    </fieldset>

</form>

<script type="text/javascript">
adminRoot = '<?= ADMIN_ROOT ?>';

$(document).ready(function() {
    $('#submitImport').click(function() {
        if (confirm("Are you sure? This will delete all of the existing pages on this site.")) {
            window.location = adminRoot + 'com.mindscapesolutions.gathercontent*gathercontent/process-import/' + $('#project').val();
        }
    });
});

</script>
