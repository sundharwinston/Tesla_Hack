<?php

$table=array(
'popular'=>array()
);

$shipments = json_decode(@file_get_contents( ABS_URL . 'charts/symbols.php'), false);
If (!empty($shipments)){
    foreach($shipments as $item) { //foreach element in $arr
        if ($item->popular==="true"){
            $table['popular'][]=
            '<div class="col-sm-6 col-md-6 col-xl-5 mb-4">'
                .'<div class="currency-block" data-marketsymbol="'.$item->code.'" data-update=\'{"child":false,"usechange":true,"positive":"positive-border","negative":"negative-border"}\'>'
                    .'<p>'.$item->display.'</p>'
                    .'<span class="big" data-field="change">&nbsp;</span>'
                    .'<hr class="small">'
                    .'<div class="row">'
                        .'<div class="col-6 col-md-6 " data-field="bid">'
                            .'<span>&nbsp;</span>'
                        .'</div>'
                        .'<div class="col-6 col-md-6 " data-field="ask">'
                            .'<span>&nbsp;</span>'
                        .'</div>'
                    .'</div>'
                .'</div>'
            .'</div>';
        }
    }
}
$newarray=array_pad($table['popular'],5,'');
echo implode('',$newarray);
?>