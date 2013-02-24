function popitup(url) {
	newwindow=window.open(url,'name','height=460,width=820');
	if (window.focus) {newwindow.focus()}
	return false;
}

$(function () {

var ajaxUrl = "ajax.php?print=ajax"
var externalTable = "dim_sample_bank_trans"
var userInputDateFormatJs = "dd.MM.yyyy";
var userInputDateFormatJquery = "dd.mm.yy";


$("input[name=dim_date_from]").datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: userInputDateFormatJquery,
    showOn: "button",
    buttonImage: "images/calendar.gif",
    buttonImageOnly: true
});

$("input[name=dim_date_to]").datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: userInputDateFormatJquery,
    showOn: "button",
    buttonImage: "images/calendar.gif",
    buttonImageOnly: true
});

/**
 * load next selectbox when dimension is selected
 */
$('body').on('change','[name="dim_l1_id"],[name="dim_l2_id"]',function() {
    var nDimValue = $(this).val();
    console.log(nDimValue);
    var sThisName = $(this).attr('name');
    var elThisRow = $(this).parent().parent();
    var elL1Selectbox = $(elThisRow).find('[name="dim_l1_id"]');
    var elL2Selectbox = $(elThisRow).find('[name="dim_l2_id"]');
    var elL3Selectbox = $(elThisRow).find('[name="dim_l3_id"]');

    if(sThisName == 'dim_l1_id' && nDimValue != 0) {
        //clear items for rpevious selectboxes
        elL2Selectbox.html('');
        elL3Selectbox.html('');
        set_selectbox_items(2, nDimValue, elL2Selectbox);
        elL2Selectbox.attr('disabled', false);
        elL3Selectbox.attr('disabled', true);
    }
    if(sThisName == 'dim_l2_id' && nDimValue != 0) {
        //clear items for rpevious selectboxes
        elL3Selectbox.html('');
        set_selectbox_items(3, nDimValue, elL3Selectbox);
        elL3Selectbox.attr('disabled', false);
    }
});

/**
 * 
 */
$('body').on('click','input.button-save',function() {
    var elThisRow = $(this).parent().parent();
    var elDateFrom = $(elThisRow).find('[name=dim_date_from]');
    var elDateTo = $(elThisRow).find('[name=dim_date_to]');
    var elL1Selectbox = $(elThisRow).find('[name="dim_l1_id"]');
    var elL2Selectbox = $(elThisRow).find('[name="dim_l2_id"]');
    var elL3Selectbox = $(elThisRow).find('[name="dim_l3_id"]');

    if($(this).hasClass('button-save')){

        var item_id = $('#record_id').val();
        var date_from = $(elDateFrom).val();
        var date_to = $(elDateTo).val();
        var amt = $('#amt').val();
        console.log('item_id: '+item_id);
        save_dimension(
            elL3Selectbox,
            externalTable,
            item_id,
            elL1Selectbox.val(),
            elL2Selectbox.val(),
            elL3Selectbox.val(),
            amt,
            date_from,
            date_to
            );

    }

    if($(this).hasClass('button-cancel')){
        $(elL1Selectbox).parent().html(sDimLevel1Value + '<input type="hidden" value="'+sDimLevel1Id+'" />');
        $(elL2Selectbox).parent().html(sDimLevel2Value + '<input type="hidden" value="'+sDimLevel2Id+'" />');
        $(elL3Selectbox).parent().html(sDimLevel3Value + '<input type="hidden" value="'+sDimLevel3Id+'" />');

        $(elDateFrom).parent().text(sDimDateFrom);
        $(elDateTo).parent().text(sDimDateTo);

        $(this).parent().html('<input type="button" class="button-edit2" />');

    }

    return false;
})

/**
 * set selectbox items for next selectbox based on previous one
 * 
 * @param {int} nLevel
 * @param {int} nParentLevelId
 * @param {obj} SelectboxSelector
 * @param {int} nValue
 * @returns {undefined}
 */
function set_selectbox_items(nLevel, nParentLevelId, SelectboxSelector,nValue) {
    $.get(ajaxUrl,
    {
        action: "get_selectbox_values",
        level: nLevel,
        parent_level_id: nParentLevelId

    },
    function(data) {

        data = $.parseJSON(data);
        /**
         * show error
         */
        if (data["error"]){
            alert(data["error"]);
            return false;
        }

        /**
         * insert option for next selectbox
         */
        SelectboxSelector.append('<option value="0">-Select-</option>');
        $.each(data, function(key, val) {
            SelectboxSelector.append($("<option></option>")
                .attr("value",val.data.id)
                .text(val.data.title));
        })
        if(nValue){
            $(SelectboxSelector).val(nValue);
        }
    })
}

/**
 * save selectboxes and split amount (server-side) by periods
 * 
 * @param {obj} ResultSelector
 * @param {string} table_name
 * @param {int} record_id
 * @param {int} l1_id
 * @param {int} l2_id
 * @param {int} l3_id
 * @param {int} amt
 * @param {string} date_from
 * @param {string} date_to
 * @returns {undefined}
 */
function save_dimension(ResultSelector, table_name, record_id, l1_id, l2_id, l3_id, amt, date_from, date_to) {
    $.post(ajaxUrl,
    {
        action: "save_dim_data",
        table_name: table_name,
        record_id: record_id,
        l1_id: l1_id,
        l2_id: l2_id,
        l3_id: l3_id,
        amt: amt,
        date_from: date_from,
        date_to: date_to
    },
    function(data) {

        data = $.parseJSON(data);
        /**
         * show error
         */
        if (data["error"]){
            alert(data["error"]);
            return false;
        }
        window.opener.location.reload(false);
        window.close();
        return false;
    })
}
})

