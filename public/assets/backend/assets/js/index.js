// dark mode by https://github.com/coliff/dark-mode-switch
const darkSwitch = document.getElementById('darkSwitch');

// this is here so we can get the body dark mode before the page displays
// otherwise the page will be white for a second...
initTheme();

window.addEventListener('load', () => {
    if (darkSwitch) {
        initTheme();
        darkSwitch.addEventListener('change', () => {
            resetTheme();
        });
    }
});

// end darkmode js

$(document).ready(function () {
    $('.table-container tr').on('click', function () {
        $('#' + $(this).data('display')).toggle();
    });
    $('#table-log').DataTable({
        "order": [$('#table-log').data('orderingIndex'), 'desc'],
        "stateSave": true,
        "stateSaveCallback": function (settings, data) {
            window.localStorage.setItem("datatable", JSON.stringify(data));
        },
        "stateLoadCallback": function (settings) {
            var data = JSON.parse(window.localStorage.getItem("datatable"));
            if (data) data.start = 0;
            return data;
        }
    });
    $('#delete-log, #clean-log, #delete-all-log').click(function () {
        return confirm('Are you sure?');
    });
});
