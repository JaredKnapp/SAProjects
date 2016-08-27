function getProjectTaskData(ajax_tasktable_url, control, projects_id, efforttypes_id, formatControlData) {
    if (efforttypes_id) {
        $.ajax({
            url: ajax_tasktable_url,
            data: {
                projects_id: projects_id,
                efforttypes_id: efforttypes_id
            },
            type: "POST",
            success: function (data) {
                jsonData = JSON.parse(data);
                formatControlData(control, jsonData);
            }
        }).fail(function () {
            alert("ERROR: problem populating Project Tasks table.");
        });
    }
}