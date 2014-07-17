<html>
<head>
    <title>Delivery Slip</title>

<?php echo $this->ag_asset->load_css('colors.css');?>
<style type="text/css">
    .label{
        float: left;
        font-family: Arial, sans-serif;
        max-height:<?php print $cell_height;?>px;
        min-height:<?php print $cell_height;?>px;
        height:<?php print $cell_height;?>px;

        max-width:<?php print $cell_width;?>px;
        min-width:<?php print $cell_width;?>px;
        width:<?php print $cell_width;?>px;

        margin-right: <?php print $margin_right;?>px;
        margin-bottom: <?php print $margin_bottom;?>px;
        display: table;
    }

    .label table{
        width: 100%;
        height: 100%;
    }

    h3{
        margin: 4px 10px;
        font-size: 16px;
    }
    td{
        padding: 4px;
        font-size: 12px;
    }

    p.shipping{
        word-wrap:break-word;
    }

    img.barcode{
        height:auto;
    }

    #container{
        width:<?php print ($cell_width * $columns) + ($margin_right * $columns) + 2 ?>;
    }

</style>
</head>
<body>
<div id="container">

<?php foreach( $main_info as $address ):?>
    <?php
        // assume resolution in 72 ppi
        $paper_pw = 1100;
        if($columns > 1){
            $min_width = ((int) floor($paper_pw/$columns)).'px';
            $width = ($min_width - 10).'px';
        }else{
            $min_width = ((int) floor($paper_pw/2)).'px';
            $width = ($min_width - 10).'px';
        }
        /*
        $resolution;
        $cell_width;
        $cell_height;
        $columns;
        $margin_right;
        $margin_bottom;
        */
    ?>
    <div class="label">
        <table>
            <tr>
                <td style="width:50%;text-align:left">
                    <?php
                        $logo = get_logo($address['merchant_id']);
                        if($logo['exist'] == true){
                            print '<img src="'.$logo['logo'].'" />';
                        }else{
                            print $address['merchant'];
                        }
                    ?>
                </td>
                <td style="width:50%;text-align:right">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width:50%;text-align:right">
                    <h3><?php print $address['recipient_name'] ?>&nbsp;</h3>
                    <p class="shipping"><?php print $address['shipping_address'] ?>&nbsp;</p>
                    <p><?php print $address['buyerdeliverycity'] ?>&nbsp;</p>
                    <img class="barcode" src="<?php print base_url()?>admin/prints/barcode/<?php print $address['merchant_trans_id'] ?>" alt="<?php print $address['merchant_trans_id'] ?>">
                </td>
            </tr>
            <tr>
                <td style="width:50%;text-align:left">
                    <?php print colorizetype( $address['delivery_type'], 'Jayon ' )?>
                </td>
                <td style="width:50%;text-align:right;border:thin solid black;">
                    <?php print $address['buyerdeliveryzone'] ?>
                </td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>

</div>

</body>
</html>