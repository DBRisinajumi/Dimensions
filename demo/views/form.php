<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Actual level</a></li>
        <li><a href="#tabs-2">Add sublevel</a></li>
    </ul>
    <div id="tabs-1">
         <form name="dim_edit_form" id="dim_edit_form" method="get" action="">
            <input type="hidden" name="id" id="id" value="<?=$aLevel['id']?>"/>
            <input type="hidden" name="level" id="level" value="<?=$aLevel['level']?>"/>
            <table class="form dim_table">
                <tr>
                    <th>Code</th>
                    <td><input type="text" class="input-1" name="code" id="code" value="<?=$aLevel['code']?>" /></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><input type="text" class="input-3" name="label" id="label" value="<?=$aLevel['label']?>" /></td>
                </tr>
                <?php if ($aLevel['level'] >= 2 && !empty($aTables)) { ?>
                <tr>
                    <th>External table</th>
                    <td>
                        <select name="table_id">
                        <option value="0">---</option>
                        <?php foreach ($aTables as $aTable) { ?>
                            <?php if (!empty($aLevel['levels_table_id']) && $aTable['id'] == $aLevel['levels_table_id']) { ?>
                            <option value="<?=$aTable['id']?>" selected="selected"> <?=$aTable['table_name']?></option>
                            <?php } else { ?>
                            <option value="<?=$aTable['id']?>"> <?=$aTable['table_name']?></option>
                            <?php } ?>
                        <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th>Hidden</th>
                    <td><input type="checkbox" name="hidden" id="hidden" value="1"<?php if (!empty($aTable['hidden'])) { ?> checked<?php } ?> /></td>
                </tr>
                <tr>
                    <td colspan="2" class="button">
                        <input type="button" class="button-1" value="Delete" id="delete_dim" name="delete_dim" />
                        <input type="button" class="button-1" value="Save" id="save_dim_form" name="save_dim_form" />
                    </td>
                </tr>
            </table>
        </form>

    </div>
    <div id="tabs-2">
    <?php if (!empty($show_add_form)) { ?>
    <form name="dim_add_form" id="dim_add_form" method="get" action="">
        <input type="hidden" name="level" id="level" value="<?=$aLevel['level']?>"/>
        <input type="hidden" name="id" id="id" value="<?=$aLevel['id']?>"/>
        <table class="form dim_table">
            <tr>
                <th>Code</th>
                <td><input type="text" class="input-1" name="code" id="code"  /></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><input type="text" class="input-3" name="label" id="label"  /></td>
            </tr>
                <?php if ($aLevel['level'] >= 2 && !empty($aTables)) { ?>
                <tr>
                    <th>External table</th>
                    <td>
                        <select name="table_id">
                        <option value="0">---</option>
                        <?php foreach ($aTables as $aTable) { ?>
                            <option value="<?=$aTable['id']?>"> <?=$aTable['table_name']?></option>
                        <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } ?>
            <tr>
                <th>Hidden</th>
                <td><input type="checkbox" name="hidden" id="hidden" value="1" disabled /></td>
            </tr>
            <tr>
                <td colspan="2" class="button">
                    <input type="button" class="button-1" value="Save" id="save_dim_add_form" name="save_dim_form" />
                </td>
            </tr>
        </table>
    </form>
    <?php } ?>

    </div>

</div>
