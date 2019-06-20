<?php
echo '
<table class="sorted">
    <thead>
        <tr>
            <th data-placeholder="Hae ryhmää">Ryhmä</th>
            <th data-placeholder="Hae toimistoa">Toimisto</th>
            <th class="sorter-false filter-false">Valitse</th>
        </tr>
    </thead>
    <tfoot>
        <tr style="height: 20px;"></tr>
        <tr class="tablesorter-ignoreRow">
            <th colspan="3" class="ts-pager form-horizontal">
            <button type="button" class="btn first"><i class="fas fa-angle-double-left"></i></button>
            <button type="button" class="btn prev"><i class="fas fa-angle-left"></i></button>
            <span class="pagedisplay"></span>
            <button type="button" class="btn next"><i class="fas fa-angle-right"></i></button>
            <button type="button" class="btn last"><i class="fas fa-angle-double-right"></i></button>
            </th>
        </tr>
        <tr class="tablesorter-ignoreRow">
            <th colspan="3" class="ts-pager form-horizontal">
            max rivit: <select class="pagesize browser-default" title="Select page size">
                <option value="10">10</option>
                <option value="20">20</option>
                <option selected="selected" value="30">30</option>
                <option value="40">40</option>
                <option value="all">Kaikki rivit</option>
            </select>
            sivu: <select class="pagenum browser-default" title="Select page number"></select>
            </th>
        </tr>
    </tfoot>
    <tbody>';

while ( $group = mysqli_fetch_array($groupquery) ) {

echo '      <tr>
            <td>'.$group['groupName'].'</td>
            <td>'.$group['officeName'].'</td>
            <td style="text-align:center;">
                <label class="container">
                    <input type="checkbox" name="grouplist[]" value='.$group['groupID'].' class="check">                        
                    <span class="checkmark"></span>
                </label>
            </td>
        </tr>';
}
echo '          
    </tbody>
</table>';

echo '<script type="text/javascript" src="/scripts/tablesorter/load.js"></script>';
?>