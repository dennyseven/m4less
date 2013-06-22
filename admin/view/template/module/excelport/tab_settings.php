<table class="form">
    <tr>
        <td>
            <?php echo $text_export_product_number; ?>
        </td>
        <td>
            <input type="number" min="50" max="800" name="ExcelPort[Settings][ExportProductNumber]" value="<?php echo !empty($data['ExcelPort']['Settings']['ExportProductNumber']) ? $data['ExcelPort']['Settings']['ExportProductNumber'] : '500'; ?>" />
        </td> 
    </tr>
    <tr>
        <td>
            <?php echo $text_import_limit; ?>
        </td>
        <td>
            <input type="number" min="10" max="800" name="ExcelPort[Settings][ImportLimit]" value="<?php echo !empty($data['ExcelPort']['Settings']['ImportLimit']) ? $data['ExcelPort']['Settings']['ImportLimit'] : '100'; ?>" />
        </td> 
    </tr>
</table>