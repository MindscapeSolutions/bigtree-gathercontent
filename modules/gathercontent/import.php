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

<ul id="progress">
</ul>

</script>

<script type="text/javascript">
adminRoot = '<?= ADMIN_ROOT ?>';

$(document).ready(function() {
    $('#submitImport').click(function() {
        if (confirm("Are you sure? This will delete all of the existing pages on this site.")) {
            //window.location = adminRoot + 'com.mindscapesolutions.gathercontent*gathercontent/process-import/' + $('#project').val();
            startImport();
        }
    });
});

function addMessage(message) {
    $('#progress').append('<li>' + message + '</li>');
}

function startImport() {
    addMessage('Starting import.');

    addMessage('Retrieving GatherContent accounts.');
    $.ajax({
        url: adminRoot + '*/com.mindscapesolutions.gathercontent/ajax/retrieve-accounts/' + $('#project').val(),
        type: 'POST',
        success: function(result) {
            if (result != "not found") {
                addMessage('Account found.');

                addMessage('Retrieving projects.');
                $.ajax({
                    url: adminRoot + '*/com.mindscapesolutions.gathercontent/ajax/retrieve-projects/' + $('#project').val() + '/' + result,
                    type: 'POST',
                    success: function(result) {
                        if (result != 'not found') {
                            addMessage('Project found.');

                            addMessage('Retrieving project pages.');
                            $.ajax({
                                url: adminRoot + '*/com.mindscapesolutions.gathercontent/ajax/retrieve-project-items/' + $('#project').val() + '/' + result,
                                type: 'POST',
                                success: function(result) {
                                    parsedResult = JSON.parse(result);

                                    addMessage(parsedResult.data.length + ' project pages retrieved.');
                                    addMessage('Importing all page data. This may take a minute. Please wait until the finished message.');
                                    $.ajax({
                                        url: adminRoot + '*/com.mindscapesolutions.gathercontent/ajax/import/',
                                        type: 'POST',
                                        data: {
                                            gatherContentId: $('#project').val(),
                                            projectData: result
                                        },
                                        success: function(result) {
                                            addMessage("Finished");
                                        },
                                        error: function(result) {
                                            addMessage('Error importing page data.');
                                        }
                                    });
                                },
                                error: function(result) {
                                    addMessage('Error retrieving project items.');
                                }
                            });
                        }
                        else {
                            addMessage('Project not found.');
                        }
                    },
                    error: function(result) {
                        addMessage('Error retrieving projects.');
                    }
                });
            }
            else {
                addMessage('Account not found.');
            }
        },
        error: function(result) {
            addMessage('Error retrieving accounts.');
        }
    });
}

</script>
