import '../editable-table-master/index.css'
import '../editable-table-master/mindmup-editabletable'

jQuery(document).ready(function () {
    $('#player-sheet .editable').editableTableWidget();
    $('#player-sheet td').on('validate', function(event, value) {
        /*if (....) {
            return false; // mark cell as invalid
        }*/
    });
});