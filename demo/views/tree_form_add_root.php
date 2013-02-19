<form name="dim_add_root_form" id="dim_add_root_form" method="get" action="">
    <input type="hidden" name="action" id="action" value="add_root"/>
    <table class="form ui-styled-table">
        <td colspan="2" class="section">Add new Dimension root level</td>
        <tr>
            <th>Code</th>
            <td><input type="text" class="input-1" name="code" id="code"  /></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><input type="text" class="input-3" name="label" id="label"  /></td>
        </tr>
        <tr>
            <th>Hidden</th>
            <td><input type="checkbox" name="hidden" id="hidden" value="1" disabled /></td>
        </tr>
        <tr>
            <td colspan="2" class="button">
                <input type="button" class="button-1" value="Save" id="save_dim_add_root_form" name="save_dim_add_root_form" />
            </td>
        </tr>
    </table>
</form>
